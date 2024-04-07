import { useState, useEffect, useCallback } from 'react';
import { v4 } from 'uuid';
import LeaderboardEntry from "./LeaderboardEntry";
import { apiRoot } from '../settings';

export default function Leaderboard() {
    const [userData, setUserData] = useState([])
    const [currentPage, setCurrentPage] = useState(1)
    const [totalPages, setTotalPages] = useState(1)
    const usersPerPage = 20
    const [loading, setLoading] = useState(true);

    const fetchUsers = useCallback(async () => {
        try {
            setLoading(true);
            const userDataResponse = await fetch(`${apiRoot}/user/all`)
            if (!userDataResponse.ok) {
                throw new Error("Error fetching users: " + userDataResponse.status);
            }
            const userData = await userDataResponse.json()

            const usersWithTasks = await Promise.all(userData.map(async (user) => {
                const userTasksResponse = await fetch(`${apiRoot}/userSchedule/${user.userId}`)
                if (!userTasksResponse.ok) {
                    throw new Error(`Error fetching tasks for user ${user.userId}: ` + userTasksResponse.status)
                }
                const userTasks = await userTasksResponse.json()
                const totalStats = userTasks.length
                return { ...user, stats: totalStats }
            }));
            setUserData(usersWithTasks)
        } catch (error) {
            console.error("Error fetching users and tasks:", error)
        } finally {
            setLoading(false)
        }
    }, []);

    useEffect(() => {
        fetchUsers();
    }, [fetchUsers]);

    const volunteerUsers = userData.filter(user => !user.isManager && user.stats !== 0);
    volunteerUsers.sort((a, b) => b.stats - a.stats);

    useEffect(() => {
        setTotalPages(Math.ceil(volunteerUsers.length / usersPerPage));
    }, [volunteerUsers]);

    const startIndex = (currentPage - 1) * usersPerPage;
    const endIndex = currentPage * usersPerPage;

    const usersForCurrentPage = volunteerUsers.slice(startIndex, endIndex);

    return (
        <>
            {loading ? (
                <div className="text-blue-700">Loading...</div>
            ) : (
                <>
                    <div className="rounded-lg text-2xl text-blue-700 p-3 m-2 flex justify-between items-center">
                        <h2>Rank</h2>
                        <h2>Name</h2>
                        <h2>Tasks</h2>
                    </div>
                    {usersForCurrentPage.map((user, index) => (
                        <LeaderboardEntry
                            key={v4()}
                            position={startIndex + index + 1}
                            name={user.userName}
                            stats={user.stats}
                        />
                    ))}
                    <div className='text-center'>Page {currentPage} of {totalPages}</div>
                    {totalPages > 1 && (
                        <div>
                            {currentPage > 1 && (
                                <button className='m-2 px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300 transition duration-300 ease-in-out float-left' onClick={() => setCurrentPage(currentPage - 1)}>Previous Page</button>
                            )}
                            {currentPage < totalPages && (
                                <button className='m-2 px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300 transition duration-300 ease-in-out float-right' onClick={() => setCurrentPage(currentPage + 1)}>Next Page</button>
                            )}
                        </div>
                    )}
                </>
            )}
        </>
    );
}