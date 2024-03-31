import { Rating } from "@mui/material"
import { apiRoot } from '../settings'
import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import PropTypes from 'prop-types'

export default function Feedback({ user }) {
    const [activityRating, setActivityRating] = useState(null);
    const [error, setError] = useState(null);
    const [selectedActivity, setSelectedActivity] = useState(null);
    const [pastActivities, setPastActivities] = useState([]);
    const [submitted, setSubmitted] = useState(false);
    const currentDate = new Date();
    const startDate = new Date();
    startDate.setDate(currentDate.getDate() - 7);

    const currentDateStr = currentDate.toISOString().split('T')[0];
    const startDateStr = startDate.toISOString().split('T')[0];

    useEffect(() => {
        const fetchPastActivities = async () => {
            try {
                const response = await fetch(`https://w21017158.nuwebspace.co.uk/api/userSchedule/${user.userId}?start=${startDateStr}&end=${currentDateStr}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch tasks');
                }
                const data = await response.json();
                setPastActivities(data);
            } catch (error) {
                console.error('Error fetching tasks:', error);
            }
        };

        fetchPastActivities();
    }, [user, currentDateStr, startDateStr]);

    const handleSelectActivity = (selectedActivityId) => {

        const strSelectedActivityId = parseInt(selectedActivityId);
        const selectedActivity = pastActivities.find(activity => activity.activity.id === strSelectedActivityId);
        if (selectedActivity) {
            setSelectedActivity(selectedActivity);
        }
    }




    const handleSubmitFeedback = async () => {
        try {
            if (activityRating) {

                await axios.post('https://w21017158.nuwebspace.co.uk/api/userSchedule/rating', null, {
                    params: {
                        id: selectedActivity.activity.id,
                        rating: activityRating
                    }
                });
                setSubmitted(true);
                setTimeout(() => {
                    setSubmitted(false);
                }, 5000);
            } else {
                setError(true);
                setTimeout(() => {
                    setError(false);
                }, 5000);
            }
        } catch (error) {
            console.error('Error submitting feedback:', error);
        }
    };

    if (user.isManager) {
        return (
            <div>
                <h1 className="text-3xl font-bold mb-3 ml-5">Managers are not allowed to rate activities</h1>
                <Link to='/'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-5">Back</button>
                </Link>
            </div>
        )
    }
    return (
        <div>

            <h1 className="text-3xl font-bold mb-3 ml-5">Volunteering Feedback</h1>
            <form className="max-w-sm">
                <label htmlFor="assignedTasks" className="ml-5 block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select an activity you have completed: </label>
                <select id="assignedTasks" className="ml-5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" onChange={(e) => handleSelectActivity(e.target.value)}>
                    {pastActivities.map((activity, index) => (
                        <option key={index + 1} value={activity.activity.id}>
                            {activity.activity.name} - {new Date(activity.start.date).toLocaleDateString('en-US',
                                { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                        </option>
                    ))}
                </select>
            </form>

            {selectedActivity && selectedActivity.activity && (
                <>
                    {<img className="w-40 mt-6 h-40 ml-5" src={`${apiRoot}/activity/${selectedActivity.activity.id}/previewimage`} />}
                    <p className="text-lg mt-6 ml-5">Rate your experience doing the activity, {selectedActivity.activity.name}.</p>
                    <p className="ml-5 mt-6">{selectedActivity.activity.name}</p>
                    <Rating className="ml-5" value={activityRating ? activityRating : null} onChange={e => setActivityRating(parseInt(e.target.value))}></Rating>

                    <p className="text-lg mt-6 ml-5">Rate your experience volunteering with the following people:</p>
                    <div className="flex flex-col ml-5 mt-6">
                        <p>John</p><Rating></Rating>
                        <p>Emma</p><Rating></Rating>
                        <p>Alex</p><Rating></Rating>
                    </div>
                    {error && (
                        <div className="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                        <svg className="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span className="sr-only">Info</span>
                        <div>
                          <span className="font-medium">Error</span> You must rate the activity before submitting feedback.
                        </div>
                      </div>
                    )}
                    {submitted ? (
                        <button className="ml-5 mb-5 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mt-5">
                            Submitted
                        </button>
                    ) : (
                        <button
                            className="ml-5 mb-5 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5"
                            onClick={handleSubmitFeedback}
                        >
                            Submit Feedback
                        </button>
                    )}
                    
                </>
            )}

        </div>
    )
}

Feedback.propTypes = {
    user: PropTypes.object.isRequired
}