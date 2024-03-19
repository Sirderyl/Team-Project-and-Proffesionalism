
import { useState } from 'react';
import { v4 as uuidv4 } from 'uuid'; 
import { MailIcon } from '@heroicons/react/outline'; 

const volunteersData = [
    { id: uuidv4(), name: 'Volunteer 1', email: 'volunteer1@example.com' },
    { id: uuidv4(), name: 'Volunteer 2', email: 'volunteer2@example.com' },
];

const InviteForm = () => {
    const [selectedVolunteers, setSelectedVolunteers] = useState([]);
    const [invitationMessage, setInvitationMessage] = useState('');

    const handleVolunteerToggle = (volunteer) => {
        setSelectedVolunteers((prevSelected) =>
            prevSelected.includes(volunteer)
                ? prevSelected.filter((v) => v !== volunteer)
                : [...prevSelected, volunteer]
        );
    };

    const sendInvitations = () => {
        console.log('Sending invitations to:', selectedVolunteers);
        console.log('Invitation Message:', invitationMessage);
    };

    return (
        <div className="w-full max-w-lg mx-auto">
            <h1 className="text-2xl font-semibold mb-4">Send Invitations</h1>
            <div className="mb-4">
                <label htmlFor="message" className="block text-sm font-medium text-gray-700 mb-1">
                    Invitation Message
                </label>
                <textarea
                    id="message"
                    className="w-full h-24 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    value={invitationMessage}
                    onChange={(e) => setInvitationMessage(e.target.value)}
                    placeholder="Enter your invitation message here..."
                ></textarea>
            </div>
            <div className="mb-4">
                <label className="block text-sm font-medium text-gray-700 mb-1">Select Volunteers</label>
                <div className="space-y-2">
                    {volunteersData.map((volunteer) => (
                        <div key={volunteer.id} className="flex items-center">
                            <input
                                type="checkbox"
                                id={`volunteer-${volunteer.id}`}
                                className="form-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                checked={selectedVolunteers.includes(volunteer)}
                                onChange={() => handleVolunteerToggle(volunteer)}
                            />
                            <label htmlFor={`volunteer-${volunteer.id}`} className="ml-2 text-sm text-gray-700">
                                {volunteer.name} - {volunteer.email}
                            </label>
                        </div>
                    ))}
                </div>
            </div>
            <button
                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                onClick={sendInvitations}
            >
                <MailIcon className="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                Send Invitations
            </button>
        </div>
    );
};

export default InviteForm;