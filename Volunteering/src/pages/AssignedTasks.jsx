import React, { useState, useEffect } from 'react';
import axios from 'axios';

const AssignedTasks = () => {
    const [schedule, setSchedule] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchSchedule = async () => {
            try {
                const response = await axios.get('https://w21017158.nuwebspace.co.uk/api/userSchedule/1');
                setSchedule(response.data);
                setIsLoading(false);
            } catch (error) {
                console.error('Error fetching schedule:', error);
                setError('Failed to fetch data');
                setIsLoading(false);
            }
        };

        fetchSchedule();
    }, []);

    return (
        <div className="container mx-auto mt-8">
            <h1 className="text-3xl font-bold mb-4">Assigned Tasks</h1>
            {isLoading ? (
                <p className="text-gray-600">Loading...</p>
            ) : error ? (
                <p className="text-red-600">{error}</p>
            ) : schedule.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {schedule.map((item, index) => (
                        <div key={index} className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-xl font-semibold mb-2">{item.activity.name}</h3>
                            <p className="text-gray-600 mb-4">{item.activity.shortDescription}</p>
                            <p className="text-gray-600">Start Date: {item.start.date}</p>
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
