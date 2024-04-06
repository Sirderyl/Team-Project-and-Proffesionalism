import  { useState, useEffect } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import { apiRoot } from '../settings';

const AssignedTasks = ({ tasks, user, activities }) => {
const [volunteerTasks, setVolunteerTasks] = useState([]);
    useEffect(() => {
        const fetchVolunteerTasks = async () => {
            if (!user) {
                setVolunteerTasks([]);
                return;
            }

            try {
                const response = await fetch(`${apiRoot}/recommendedActivities/${user.userId}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch tasks');
                }
                const data = await response.json();
                const sortedTasks = data.sort((a, b) => new Date(a.activityStart) - new Date(b.activityStart));
                setVolunteerTasks(sortedTasks);
            } catch (error) {
                console.error('Error fetching tasks:', error);
            }
        };

        fetchVolunteerTasks();
    }, [user]);


    if (!user.isManager) {
        // Volunteer view
        return (
            <div className="max-w-4xl mx-auto mt-8">
                <h1 className="text-3xl font-bold mb-4 text-blue-700">Assigned Tasks</h1>
                {tasks.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {tasks.map(task => (
                            <div key={task.activity.id + task.start.date} className="bg-gray-100 rounded-lg shadow-md p-6">
                                <h3 className="text-xl font-semibold mb-2 text-blue-700">{task.activity.name}</h3>
                                <p className="text-gray-700 mb-4">{task.activity.shortDescription}</p>
                                <p className="text-gray-700 mb-4">Start Date: {new Date(task.start.date).toLocaleDateString('en-US',
                                    { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                                <Link to={`/activity/${task.activity.id}`}>
                                    <button className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                                        View Details
                                    </button>
                                </Link>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-700">No assigned tasks</p>
                )}

                <h1 className="text-3xl font-bold mb-4 text-blue-700 mt-8">Suggested Activities</h1>
                {volunteerTasks.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {volunteerTasks.map(task => (
                            <div key={task.activityId + task.activityStart} className="bg-gray-100 rounded-lg shadow-md p-6">
                                <h3 className="text-xl font-semibold mb-2 text-blue-700">{task.activityName}</h3>
                                <p className="text-gray-700 mb-4">{task.shortDescription}</p>
                                <p className="text-gray-700 mb-4">Start Date: {new Date(task.activityStart).toLocaleDateString('en-US',
                                    { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-700">No suggested activities</p>
                )}
            </div>
        );
    } else {
        // Manager view
        return (
            <div className="max-w-4xl mx-auto mt-8">
                <h1 className="text-3xl font-bold mb-4 text-blue-700">{`Your organization's current activities`}</h1>
                {activities.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {activities.map(activity => (
                            <div key={activity.activity.id + activity.startTime.date} className="bg-gray-100 rounded-lg shadow-md p-6">
                                <h3 className="text-xl font-semibold mb-2 text-blue-700">{activity.activity.name}</h3>
                                <p className="text-gray-700 mb-4">{activity.activity.shortDescription}</p>
                                <p className="text-gray-700 mb-4">Start Date: {new Date(activity.startTime.date).toLocaleDateString('en-US',
                                    { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                                <p className='text-gray-700 mb-4'>Assigned to:{" "}
                                    {activity.users.map((user, index) => (
                                        <span key={user.id}>
                                            {index > 0 && ', '}
                                            {user.name}
                                        </span>
                                    ))}
                                </p>
                                <Link to={`/activity/${activity.activity.id}`}>
                                    <button className="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                                        View Details
                                    </button>
                                </Link>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-700">No assigned tasks</p>
                )}
            </div>
        );
    }
};

AssignedTasks.propTypes = {
    tasks: PropTypes.array.isRequired,
    user: PropTypes.object.isRequired,
    activities: PropTypes.array.isRequired
};

export default AssignedTasks;
