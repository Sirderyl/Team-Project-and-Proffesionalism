import { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import PropTypes from 'prop-types';

const ActivityDetailsPage = ({ user }) => {
    const { id } = useParams();

    const [activity, setActivity] = useState(null);

    useEffect(() => {
        const fetchActivity = async () => {
            try {
                const response = await fetch(`https://w20010297.nuwebspace.co.uk/api/activity/${id}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch activity');
                }
                const data = await response.json();
                setActivity(data);
            } catch (error) {
                console.error('Error fetching activity:', error);
            }
        };

        fetchActivity();
    }, [id]);

    const handleSignUp = () => {
        // 1. Offer the user for which day and time they want to sign up
        // 2. Check if the user is already signed up for that day and time, and if so, show an error message
        // 3. Check if the selected day and time is already full, and if so, show an error message
        // 4. Check if the selected day and time matches the user's availability, and if not, show an error message
        // 5. If all checks pass, sign up the user for the activity and remove the relevant availability from the user's schedule
        console.log('User signed up for the activity');
    };

    return (
        <div className="max-w-4xl mx-auto mt-8">
            {activity ? (
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h3 className="text-2xl font-semibold mb-4 text-blue-700">{activity.name}</h3>
                    <p className="text-base text-gray-700 mb-4">{activity.description}</p>
                    <div className="text-gray-700 mb-4">
                        <p className="font-semibold mb-2">Schedule:</p>
                        <ul className="list-disc list-inside">
                            {activity.times.map((time, index) => (
                                <li key={index}>{time.day}: {time.start} - {time.end}</li>
                            ))}
                        </ul>
                    </div>
                    <p className="text-gray-700 mb-4">Needed Volunteers: {activity.neededVolunteers}</p>
                    <p className="text-gray-700 mb-2">Organization: {activity.organization.name}</p>
                    {!user.isManager && (
                        <button onClick={handleSignUp} className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                            Sign Up
                        </button>
                    )}
                </div>
            ) : (
                <p className="text-gray-600">Loading...</p>
            )}
        </div>
    );
};

export default ActivityDetailsPage;

ActivityDetailsPage.propTypes = {
    user: PropTypes.object.isRequired
};
