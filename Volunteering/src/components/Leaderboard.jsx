import { useState, useEffect, useCallback } from 'react';
import { v4 } from 'uuid';
import LeaderboardEntry from "./LeaderboardEntry";

export default function Leaderboard() {
    const [userData, setUserData] = useState([]);
    const position = 1;

    const fetchUsers = useCallback(async () => {
        try {
            const userDataResponse = await fetch("https://w21010679.nuwebspace.co.uk/api/user/all");
            if (!userDataResponse.ok) {
                throw new Error("Error fetching users: " + userDataResponse.status);
            }
            const userData = await userDataResponse.json();

            const usersWithTasks = await Promise.all(userData.map(async (user) => {
                const userTasksResponse = await fetch(`https://w21017158.nuwebspace.co.uk/api/userSchedule/${user.userId}`);
                if (!userTasksResponse.ok) {
                    throw new Error(`Error fetching tasks for user ${user.userId}: ` + userTasksResponse.status);
                }
                const userTasks = await userTasksResponse.json();
                const totalStats = userTasks.length;
                return { ...user, stats: totalStats };
            }));
            setUserData(usersWithTasks);
        } catch (error) {
            console.error("Error fetching users and tasks:", error);
        }
    }, []);
    
    useEffect(() => {
        fetchUsers();
    }, [fetchUsers]);

    var volunteerUsers = userData.filter(user => !user.isManager);
    volunteerUsers = volunteerUsers.filter(user => !user.stats == 0)

    return (
        volunteerUsers.map(user => (
            <LeaderboardEntry
                key={v4()}
                position={position}
                name={user.userName}
                stats={user.stats}
            />
        ))
    );
}
