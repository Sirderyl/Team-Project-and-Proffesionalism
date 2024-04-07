import { useState, useEffect, useCallback } from 'react';
import { useParams,Link } from 'react-router-dom';
import { apiRoot } from '../settings';
import toast, { Toaster } from 'react-hot-toast'
import PropTypes from 'prop-types';

function ActivityDetailsPage({ user, availability, setAvailability, currentDate, endDate }) {
    const { id } = useParams();

    const [activity, setActivity] = useState(null);
    const [userSchedule, setUserSchedule] = useState([]);
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [selectedDay, setSelectedDay] = useState('');
    const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const daysOfWeekArr = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    const fetchUserSchedule = useCallback(() => {
        fetch(`${apiRoot}/userSchedule/${user.userId}?start=${currentDate}&end=${endDate}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch user schedule');
                }
                return response.json();
            })
            .then(data => setUserSchedule(data))
            .catch(error => console.error('Error fetching user schedule:', error));
    }, [user.userId, currentDate, endDate]);

    useEffect(() => {
        const fetchActivity = async () => {
            try {
                const response = await fetch(`${apiRoot}/activity/${id}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch activity');
                }
                const data = await response.json();
                setActivity(data);
            } catch (error) {
                console.error('Error fetching activity:', error);
            }
        };

        if (localStorage.getItem('user') !== null) {
            setIsLoggedIn(true);
        }

        fetchActivity();
        fetchUserSchedule();
    }, [id, fetchUserSchedule]);

    function getNextDate(dayOfWeek, time) {
        const today = new Date();
        const todayIndex = today.getDay();
        const targetIndex = daysOfWeekArr.indexOf(dayOfWeek);

        let difference = targetIndex - todayIndex;

        if (difference <= 0) {
            difference += 7;
        }

        today.setDate(today.getDate() + difference);
        today.setHours(time, 0, 0, 0);
        return today;
    }

    
    
    const sortedDays = activity?.times.sort((a, b) => daysOfWeek.indexOf(a.day) - daysOfWeek.indexOf(b.day));

    const handleSignUp = () => {
        if(!selectedDay) {
            toast.error('Please select a day to sign up for the activity');
            return;
        }

        // Assign the selected day of week to a date
        const date = getNextDate(selectedDay, activity.times.find(time => time.day === selectedDay).start);
        
        // Check if the user is already signed up for that day and time
        const isUserAlreadySignedUp = userSchedule.some(schedule => {
            const scheduleDate = new Date(schedule.start.date);
            return scheduleDate.getTime() === date.getTime();
        });

        if (isUserAlreadySignedUp) {
            toast.error('You are already signed up for an activity on this day and time');
            return;
        }

        // TODO: Check if the activity on the selected day and time is already full

        // Check if the selected day and time matches the user's availability
        const userAvailability = user.availability[selectedDay];
        if (!userAvailability) {
            toast.error('You are not available on this day');
            return;
        }

        const activityTime = activity.times.find(time => time.day === selectedDay);
        if (activityTime.start < userAvailability.start || activityTime.end > userAvailability.end) {
            toast.error('This activity does not fit into your schedule');
            return;
        }

        // 5. If all checks pass, sign up the user for the activity and remove the relevant availability from the user's schedule
        const formattedDate = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}T${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')}`;

        let formData = new FormData();
        formData.append('userId', user.userId);
        formData.append('activityId', id);
        formData.append('start', formattedDate);

        fetch(`${apiRoot}/activity/${id}/userSignup`,
            {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!(response.status === 200 || response.status === 201 || response.status === 204)) {
                    toast.error('Error signing up for the activity');
                    throw new Error('Error signing up for the activity: ' + response.status);
                } else {
                    toast.success('You have successfully signed up for the activity');
                    fetchUserSchedule();
                }
            
            })
            .catch(err => console.error(err));

        fetch(`${apiRoot}/user/${user.userId}/availability/${daysOfWeekArr[date.getDay()]}`,
            {
                method: 'DELETE'
            })
            .then(response => {
                if (!(response.status === 200 || response.status === 204)) {
                    toast.error('Error deleting record')
                    throw new Error('Error deleting record: ' + response.status)
                } else {
                    setAvailability(availability.filter(item => item.day !== daysOfWeekArr[date.getDay()]));
                }
            })
            .catch(err => console.error(err));

        let emailDate = new Date(formattedDate);
        let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };
        let formattedEmailDate = emailDate.toLocaleString('en-US', options).replace(',', ' at').replace(':', ' ');

        let formDataMail = new FormData();
        formDataMail.append('email', user.email);
        formDataMail.append('name', user.userName);
        formDataMail.append('activity', activity.name);
        formDataMail.append('activityDetails', activity.description);
        formDataMail.append('start', formattedEmailDate);

        fetch(`${apiRoot}/user/sendNotification`,
            {
                method: 'POST',
                body: formDataMail
            })
            .then(response => {
                if (!response.status == 200) {
                    throw new Error('Error sending email: ' + response.status);
                }
            })
            .catch(err => console.error(err));
    };

    return (
        <div className="max-w-4xl mx-auto mt-8">
            <Toaster />
            {activity ? (
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h3 className="text-2xl font-semibold mb-4 text-blue-700">{activity.name}</h3>
                    <p className="text-base text-gray-700 mb-4">{activity.description}</p>
                    <div className="text-gray-700 mb-4">
                        <p className="font-semibold mb-2">Schedule:</p>
                        <ul className="list-disc list-inside">
                            {sortedDays.map((time, index) => (
                                <li key={index + time.day}>{time.day}: {time.start} - {time.end}</li>
                            ))}
                        </ul>
                    </div>
                    <p className="text-gray-700 mb-4">Needed Volunteers: {activity.neededVolunteers}</p>
                    <p className="text-gray-700 mb-4">Organization: {activity.organization.name}</p>
                    {isLoggedIn && !user.isManager && (
                        <>
                            <p className='text-gray-700 mb-2'>Choose the day you want to sign up for:</p>
                            <select className="mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" onChange={e => setSelectedDay(e.target.value)}>
                                <option value="">Select a day</option>
                                {sortedDays.map((time, idx) => (
                                    <option key={idx + time.day} value={time.day}>{time.day}</option>
                                ))}
                            </select>

                            <button onClick={handleSignUp} className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                                Sign Up
                            </button>
                        </>
                    )}
                    {isLoggedIn && user.isManager ? (
                        <Link to={`/InviteForm`} className="text-blue-700">Invite Volunteers</Link>
                    ) : (
                        <p className="text-gray-700">
                            {isLoggedIn ? "You are not a manager and can sign up for activities" : "Please log in to sign up for activities"}
                        </p>
                    )}

                </div>
            ) : (
                <p className="text-gray-600">Loading...</p>
            )}
        </div>
    );
}

export default ActivityDetailsPage;

ActivityDetailsPage.propTypes = {
    user: PropTypes.object.isRequired,
    availability: PropTypes.array.isRequired,
    setAvailability: PropTypes.func.isRequired,
    currentDate: PropTypes.string.isRequired,
    endDate: PropTypes.string.isRequired
};
