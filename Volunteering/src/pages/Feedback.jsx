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
                const response = await fetch(`${apiRoot}/userSchedule/${user.userId}?start=${startDateStr}&end=${currentDateStr}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch tasks');
                }
                const data = await response.json();

                const existingActivities = {};

                const filteredData = data.filter(({ activity }) => {
                    if (existingActivities[activity.name]) {
                        return false;
                    } else {
                        existingActivities[activity.name] = true;
                        return true;
                    }
                });

                setPastActivities(filteredData);
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

                await axios.post(`${apiRoot}/userSchedule/rating`, null, {
                    params: {
                        id: selectedActivity.activity.rowid,
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
            <div className="max-w-4xl mx-auto mt-8">
                <h1 className="text-3xl font-bold mb-4 text-blue-700">Managers are not allowed to rate activities</h1>
                <Link to='/'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5">Back</button>
                </Link>
            </div>
        )
    }
    return (
        <div className="max-w-4xl mx-auto mt-8">
            <h1 className="text-3xl font-bold mb-4 text-blue-700">Volunteering Feedback</h1>
            <form className="max-w-md">
                <p className="font-semibold mb-2">Select an activity from last week you have completed: </p>
                <select id="assignedTasks" className="mb-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" onChange={(e) => handleSelectActivity(e.target.value)}>
                    {pastActivities.map((activity, index) => (
                        <option key={index + 1} value={activity.activity.id}>
                            {activity.activity.name}
                        </option>
                    ))}
                </select>
            </form>

            {selectedActivity && selectedActivity.activity && (
                <>
                    {<img className="w-40 mt-6 h-40" src={`${apiRoot}/activity/${selectedActivity.activity.id}/previewimage`} />}
                    <p className="font-semibold mb-2">Rate your experience doing the activity, {selectedActivity.activity.name}.</p>
                    <p className="text-gray-700 mb-2">{selectedActivity.activity.name}</p>
                    <Rating value={activityRating ? activityRating : null} onChange={e => setActivityRating(parseInt(e.target.value))}></Rating>
                    <br></br>
                    {error && (
                        <div className="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                            <svg className="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                            </svg>
                            <span className="sr-only">Info</span>
                            <div>
                                <span className="font-medium">Error</span> You must rate the activity before submitting feedback.
                            </div>
                        </div>
                    )}
                    {submitted ? (
                        <button className="inline-block px-4 py-2 mt-2 bg-green-500 text-white rounded-md hover:bg-green-700 hover:text-white transition duration-300 ease-in-out">
                            Submitted
                        </button>
                    ) : (
                        <button
                            className="inline-block px-4 py-2 mt-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 hover:text-white transition duration-300 ease-in-out"
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