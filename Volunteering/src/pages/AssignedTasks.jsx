// import React from 'react';
import PropTypes from 'prop-types';

const AssignedTasks = ({ tasks, user, activities }) => {

    if (!user.isManager) {
        // Volunteer view
        return (
            <div className="max-w-4xl mx-auto mt-8">
                <h1 className="text-3xl font-bold mb-4 text-blue-700">Assigned Tasks</h1>
                {tasks.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {tasks.map(task => (
                            <div key={task.id} className="bg-gray-100 rounded-lg shadow-md p-6">
                                <h3 className="text-xl font-semibold mb-2 text-blue-700">{task.activity.name}</h3>
                                <p className="text-gray-700 mb-4">{task.activity.shortDescription}</p>
                                <p className="text-gray-700 mb-4">Start Date: {new Date(task.start.date).toLocaleDateString('en-US',
                                    { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-700">No assigned tasks</p>
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
                            <div key={activity.id} className="bg-gray-100 rounded-lg shadow-md p-6">
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
