import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import {apiRoot} from '../settings';
import PropTypes from 'prop-types';

// Search Component
const Search = ({ search, handleSearch }) => {
    return (
        <input
            type="text"
            placeholder="Search activities..."
            value={search}
            onChange={handleSearch}
            className="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:border-blue-400"
        />
    );
};
Search.propTypes = {
    search: PropTypes.string.isRequired,
    handleSearch: PropTypes.func.isRequired,
};

const AllActivities = () => {
    const [activities, setActivities] = useState([]);
    const [page, setPage] = useState(1);
    const [search, setSearch] = useState('');

    useEffect(() => {
        const fetchActivities = async () => {
            try {
                const response = await fetch(`${apiRoot}/activities`);
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

    const startIndex = (page - 1) * 20;
    const endIndex = startIndex + 20;
    const paginatedActivities = activities.filter(activity =>
        activity.name.toLowerCase().includes(search.toLowerCase()) ||
        activity.shortDescription.toLowerCase().includes(search.toLowerCase())
    ).slice(startIndex, endIndex);

    const nextPage = () => {
        setPage(page + 1);
    };

    const prevPage = () => {
        setPage(page - 1);
    };

    const handleSearch = (event) => {
        setSearch(event.target.value);
        setPage(1);
    };

    return (
        <div className="max-w-4xl mx-auto mt-8">
            <h1 className="text-3xl font-bold mb-4 text-blue-700">All Activities</h1>
            <Search search={search} handleSearch={handleSearch} />
            <div className="flex justify-between mt-4">
                <button onClick={prevPage} disabled={page === 1} className="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">Previous</button>
                <button onClick={nextPage} disabled={endIndex >= activities.length} className="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">Next</button>
            </div>
            {paginatedActivities.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {paginatedActivities.map(activity => (
                        <div key={activity.id} className="bg-white rounded-lg shadow-md p-6">
                            <h3 className="text-lg font-semibold mb-2 text-blue-700">{activity.name}</h3>
                            <p className="text-gray-700 mb-2">{activity.shortDescription}</p>
                            <p className="text-gray-700 mb-2">Needed Volunteers: {activity.neededVolunteers}</p>
                            <div className="text-gray-700">
                                <p>Schedule:</p>
                                <ul>
                                    {Object.entries(activity.times).map(([day, time], index) => (
                                        <li key={index}>{day}: {time.start} - {time.end}</li>
                                    ))}
                                </ul>
                            </div>
                            <Link to={`/activity/${activity.id}`} className="inline-block px-4 py-2 mt-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 hover:text-white transition duration-300 ease-in-out">View Details</Link>
                        </div>
                    ))}
                </div>
            ) : (
                <p className="text-gray-700">No activities available</p>
            )}
            <div className="flex justify-between mt-4">
                <button onClick={prevPage} disabled={page === 1} className="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">Previous</button>
                <button onClick={nextPage} disabled={endIndex >= activities.length} className="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300">Next</button>
            </div>
        </div>
    );
};

export default AllActivities;
