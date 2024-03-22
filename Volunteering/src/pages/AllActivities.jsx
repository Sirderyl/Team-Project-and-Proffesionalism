 import { useState, useEffect } from 'react';
// import PropTypes from 'prop-types';

const AllActivities = () => {
    const [activities, setActivities] = useState([]);

    useEffect(() => {
        const fetchActivities = async () => {
            try {
                const response = await fetch('https://w20010297.nuwebspace.co.uk/api/activities');
                if (!response.ok) {
                    throw new Error('Failed to fetch activities');
                }
                const data = await response.json();
                setActivities(data);
            } catch (error) {
                console.error('Error fetching activities:', error);
            }
        };

        fetchActivities();
    }, []);

    return (
        <div className="max-w-4xl mx-auto mt-8">
            <h1 className="text-3xl font-bold mb-4">All Activities</h1>
            {activities.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {activities.map(activity => (
                        <div key={activity.id} className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-lg font-semibold mb-2">{activity.name}</h3>
                            <p className="text-gray-600 mb-2">{activity.shortDescription}</p>
                            <p className="text-gray-600 mb-2">Needed Volunteers: {activity.neededVolunteers}</p>
                            <div className="text-gray-600">
                                <p>Schedule:</p>
                                <ul>
                                    {Object.entries(activity.times).map(([day, time], index) => (
                                        <li key={index}>{day}: {time.start} - {time.end}</li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-gray-600">No activities available</p>
            )}
        </div>
    );
};

export default AllActivities;
