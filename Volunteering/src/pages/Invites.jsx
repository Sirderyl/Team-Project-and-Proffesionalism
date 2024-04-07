import { apiRoot } from "../settings"
import { useEffect, useState } from "react";
import { v4 } from 'uuid';

export default function Invites({ userId }) {

    const [invites, setInvites] = useState([]);

    useEffect(() => {
        const fetchInvites = async () => {
            try {
                const response = await fetch(`${apiRoot}/invites/${userId}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch activities');
                }
                const data = await response.json();
                setInvites(data);
            } catch (error) {
                console.error('Error fetching activities:', error);
            }
        };

        fetchInvites();
    }, [userId]);

    return (
        <main className="max-w-4xl mx-auto mt-8">
            <h1 className="text-3xl text-blue-700 font-bold mb-4">Invites</h1>
            {invites.map(invite => (
                <div className="rounded-lg shadow-md p-3 m-2 flex justify-between items-center" key={invite.id}>
                    <h2>{invite.name} has invited you to join their organization!</h2>
                </div>
            ))}
        </main>
    )
}