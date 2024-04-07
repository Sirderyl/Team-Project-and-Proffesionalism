import { apiRoot } from "../settings"
import { useEffect, useState } from "react";
import PropTypes from 'prop-types'

export default function Invites({ userId }) {

    const [invites, setInvites] = useState([]);

    const handleAccept = async (userId, organizationId) => {
        try {
            const response = await fetch(`${apiRoot}/organization/${organizationId}/user/${userId}/status?status=Member`, {
                method: 'POST',
            });
            if (!response.ok) {
                throw new Error('Failed to set status');
            }
        } catch (error) {
            console.error('Error accepting invitation:', error.message);
        }
    };

    const handleDecline = async (userId, organizationId) => {
        try {
            const response = await fetch(`${apiRoot}/organization/${organizationId}/user/${userId}/status?status=None`, {
                method: 'POST',
            });
            if (!response.ok) {
                throw new Error('Failed to set status')
            }
        } catch (error) {
            console.error('Error declining invitation:', error.message)
        }
    };

    useEffect(() => {
        const fetchInvites = async () => {
            try {
                const response = await fetch(`${apiRoot}/invites/${userId}`)
                if (!response.ok) {
                    throw new Error('Failed to fetch invites')
                }
                const data = await response.json()
                setInvites(data);
            } catch (error) {
                console.error('Error fetching invites:', error)
            }
        };

        fetchInvites();
    }, [userId]);

    return (
        <main className="max-w-4xl mx-auto mt-8">
            <h1 className="text-3xl text-blue-700 font-bold mb-4">Invites</h1>
            {invites.length === 0 ? (
                <p className="text-lg text-gray-600">You have no pending invites.</p>
            ) : (
                invites.map(invite => (
                    <div className="rounded-lg shadow-md p-3 m-2 items-center text-center" key={invite.id}>
                        <h2 className="text-blue-700 text-lg font-semibold text-center">{invite.name} has invited you to join their organization!</h2>
                        <p>We want you to join our organization!</p>
                        <div className="flex justify-evenly">
                            <button onClick={() => handleAccept(userId, invite.id)} className="bg-blue-600 hover:bg-blue-700 text-white rounded-md first-letter:transition duration-300 ease-in-out font-bold px-4 py-2 mt-2">Accept</button>
                            <button onClick={() => handleDecline(userId, invite.id)} className="bg-red-500 hover:bg-red-700 text-white rounded-md first-letter:transition duration-300 ease-in-out font-bold px-4 py-2 mt-2">Decline</button>
                        </div>
                    </div>
                ))
            )}
        </main>
    )
}

Invites.propTypes = {
    userId: PropTypes.number.isRequired,
}