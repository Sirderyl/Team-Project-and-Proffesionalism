import { useState, useEffect, useCallback } from 'react';
import { v4 } from 'uuid';
import LeaderboardEntry from "./LeaderboardEntry";

export default function Leaderboard() {
    const [userData, setUserData] = useState([]);
    const position = 1;

    const fetchUsers = useCallback(() => {
        fetch("https://w21010679.nuwebspace.co.uk/api/user/all")
            .then(response => {
                if (!response.ok) {
                    throw new Error("Error fetching users: " + response.status);
                }
                return response.json();
            })
            .then(data => {
                setUserData(data);
            })
            .catch(error => {
                console.error("Error fetching users:", error);
            });
    }, []);
    
    useEffect(() => {
        fetchUsers();
    }, [fetchUsers]);

    const volunteerUsers = userData.filter(user => !user.isManager);

    return (
        volunteerUsers.map(user => (
            <LeaderboardEntry
                key={v4()}
                position={position}
                name={user.userName}
                stats={100}
            />
        ))
    );
}
