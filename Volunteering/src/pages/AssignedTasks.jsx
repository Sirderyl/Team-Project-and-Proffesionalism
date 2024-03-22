// import React from 'react';
import PropTypes from 'prop-types';

const AssignedTasks = ({ tasks, user, activities }) => {

    if (!user.isManager) {
        return (
            <div className="max-w-4xl mx-auto mt-8">
                <h1 className="text-3xl font-bold mb-4">Assigned Tasks</h1>
                {tasks.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {tasks.map(task => (
                            <div key={task.id} className="bg-white rounded-lg shadow-md p-6">
                                <h3 className="text-lg font-semibold mb-2">{task.activity.name}</h3>
                                <p className="text-gray-600 mb-2">{task.activity.shortDescription}</p>
                                <p className="text-gray-600 mb-2">Start Date: {task.start.date}</p>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-600">No assigned tasks</p>
                )}
            </div>
        );
    } else {
        return (
            <div className="max-w-4xl mx-auto mt-8">
                <h1 className="text-3xl font-bold mb-4">Assigned Tasks</h1>
                {activities.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {activities.map(activity => (
                            <div key={activity.id} className="bg-white rounded-lg shadow-md p-6">
                                <h3 className="text-lg font-semibold mb-2">{activity.name}</h3>
                                <p className="text-gray-600 mb-2">{activity.shortDescription}</p>
                                <p className='text-gray-600 mb-2'>Assigned to: {activity.assignedTo}</p>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-gray-600">No assigned tasks</p>
                )}
            </div>
        );
    }
};

AssignedTasks.propTypes = {
    tasks: PropTypes.array.isRequired,
    user: PropTypes.object.isRequired
};

export default AssignedTasks;
