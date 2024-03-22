import { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';

const ActivityDetailsPage = () => {
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
       
        console.log('User signed up for the activity');
    };

    return (
        <div className="max-w-4xl mx-auto mt-8">
            {activity ? (
                <div className="bg-white rounded-lg shadow-md p-6">
                    <h3 className="text-2xl font-semibold mb-4 text-gray-800">{activity.name}</h3>
                    <p className="text-base text-gray-700 mb-4">{activity.description}</p>
                    <div className="text-gray-600 mb-4">
                        <p className="font-semibold mb-2">Schedule:</p>
                        <ul className="list-disc list-inside">
                            {activity.times.map((time, index) => (
                                <li key={index}>{time.day}: {time.start} - {time.end}</li>
                            ))}
                        </ul>
                    </div>
                    <p className="text-gray-600 mb-4">Needed Volunteers: {activity.neededVolunteers}</p>
                    <p className="text-gray-600 mb-2">Organization: {activity.organization.name}</p>
                    <button onClick={handleSignUp} className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Sign Up
                    </button>
                </div>
            ) : (
                <p className="text-gray-600">Loading...</p>
            )}
        </div>
    );
};

export default ActivityDetailsPage;