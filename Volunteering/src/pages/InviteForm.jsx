
import { useState } from 'react'; 
import { MailIcon } from '@heroicons/react/outline'; 
import PropTypes from 'prop-types';
//
const volunteersData = [
    { id: 1, name: 'Volunteer 1', email: 'volunteer1@example.com' },
    { id: 2, name: 'Volunteer 2', email: 'volunteer2@example.com' },
];

const sendInvitations = async (userId,organizationId) => {
    try {
     
        // Make an HTTP POST request to your PHP API endpoint
        const response = await fetch(`https://w21010679.nuwebspace.co.uk/api/organization/${organizationId}/user/${userId}/status?status=Invited`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        // Handle response
        if (!response.ok) {
            throw new Error('Failed to send invitations');
        }
        console.log('Invitations sent successfully');

        // Reset state or perform any necessary actions after sending invitations
    } catch (error) {
        console.error('Error sending invitations:', error.message);
    }
};

const InviteForm = (props) => {
    const [selectedVolunteers, setSelectedVolunteers] = useState([]);
    const [invitationMessage, setInvitationMessage] = useState('');

    const handleVolunteerToggle = (volunteer) => {
        setSelectedVolunteers((prevSelected) =>
            prevSelected.includes(volunteer)
                ? prevSelected.filter((v) => v !== volunteer)
                : [...prevSelected, volunteer]
        );
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
                onClick={() => {
                    for (const volunteer of selectedVolunteers) {
                        sendInvitations(volunteer.id,props.organizationId);
                    }
                }}
            >
                <MailIcon className="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                Send Invitations
            </button>
        </div>
    );
};
InviteForm.propTypes = {
    organizationId: PropTypes.number.isRequired,
}

export default InviteForm;