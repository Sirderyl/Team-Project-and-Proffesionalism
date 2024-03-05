import React from 'react';

const AssignedTasks = ({ tasks }) => {
    return (
        <div className="max-w-4xl mx-auto mt-8">
            <h1 className="text-3xl font-bold mb-4">Page Title</h1>
            <h2 className="text-2xl font-bold mb-4">Assigned Tasks</h2>
            {tasks.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {tasks.map(task => (
                        <div key={task.id} className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-lg font-semibold mb-2">{task.title}</h3>
                            <p className="text-gray-600 mb-2">{task.description}</p>
                            <div className="flex flex-wrap items-center mb-2">
                                <span className="text-gray-600 mr-2">Volunteers:</span>
                                {task.volunteers.map(volunteer => (
                                    <span key={volunteer.id} className="bg-gray-100 text-gray-800 rounded-full px-2 py-1 text-sm mr-2 mb-2">
                                        {volunteer.name}
                                    </span>
                                ))}
                            </div>
                            <p className="text-gray-600 mb-2">Deadline: {task.deadline}</p>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-gray-600">No assigned tasks</p>
            )}
        </div>
    );
};

export default AssignedTasks;
