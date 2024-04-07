import { useState, useEffect } from 'react';
import { MailIcon } from '@heroicons/react/outline';
import PropTypes from 'prop-types';
import { useNavigate } from 'react-router-dom';

const sendInvitations = async (userId, organizationId, setError) => {
    try {
        const response = await fetch(`https://w21010679.nuwebspace.co.uk/api/organization/${organizationId}/user/${userId}/status?status=Invited`, {
            method: 'POST',
        });
//
        if (!response.ok) {
            throw new Error('Failed to send invitations');
        }
        console.log('Invitations sent successfully');
    } catch (error) {
        console.error('Error sending invitations:', error.message);
        setError('Failed to send invitations. Please try again.');
    }
};

const InviteForm = (props) => {
    const [organizations, setOrganizations] = useState([]);
    const [selectedOrganization, setSelectedOrganization] = useState('');
    const [selectedVolunteers, setSelectedVolunteers] = useState([]);
    const [invitationMessage, setInvitationMessage] = useState('');
    const [volunteersData, setVolunteersData] = useState([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();


    useEffect(() => {
        async function fetchOrganizations() {
            try {
                const response = await fetch(`https://w20013000.nuwebspace.co.uk/api/user/${props.userId}/organizations`);
                if (!response.ok) {
                    throw new Error('Failed to fetch organizations');
                }
                const data = await response.json();
                setOrganizations(data.filter(entry => entry.status === 'Manager'));
            } catch (error) {
                console.error('Error fetching organizations:', error.message);
                setError('Failed to fetch organizations. Please try again.');
            }
        }
        fetchOrganizations();
    }, [props.userId]);

    useEffect(() => {
        const fetchVolunteers = async () => {
            try {
                setLoading(true);
                const response = await fetch(`https://w21010679.nuwebspace.co.uk/api/user/all`);
                if (!response.ok) {
                    throw new Error('Failed to fetch volunteers');
                }
                const data = await response.json();
                setVolunteersData(data);
                setLoading(false);
            } catch (error) {
                console.error('Error fetching volunteers:', error.message);
                setError('Failed to fetch volunteers. Please try again.');
                setLoading(false);
            }
        };
        fetchVolunteers();
    }, []);

    const handleVolunteerToggle = (volunteer) => {
        setSelectedVolunteers((prevSelected) =>
            prevSelected.includes(volunteer)
                ? prevSelected.filter((v) => v !== volunteer)
                : [...prevSelected, volunteer]
        );
    };

    const handleSendInvitations = async () => {
        if (!selectedOrganization) {
            setError('Please select an organization.');
            return;
        }

        if (selectedVolunteers.length === 0) {
            setError('Please select at least one volunteer.');
            return;
        }

        if (!invitationMessage.trim()) {
            setError('Please enter an invitation message.');
            return;
        }

        for (const volunteer of selectedVolunteers) {
            await sendInvitations(volunteer.userId, selectedOrganization, setError);
        }
        navigate('/')

    };


    // Filter volunteers based on search term
    const filteredVolunteers = volunteersData.filter(
        (volunteer) =>
            volunteer.userName.toLowerCase().includes(searchTerm.toLowerCase()) ||
            volunteer.email.toLowerCase().includes(searchTerm.toLowerCase())
    );

    return (
        <div className="w-full max-w-lg mx-auto">
            <h1 className="text-2xl font-semibold mb-4">Send Invitations</h1>
            {error && <div className="bg-red-100 text-red-700 p-3 mb-4">{error}</div>}
            <div className="mb-4">
                <label htmlFor="organization" className="block text-sm font-medium text-gray-700 mb-1">
                    Select Organization
                </label>
                <select
                    id="organization"
                    className="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    value={selectedOrganization}
                    onChange={(e) => setSelectedOrganization(e.target.value)}
                >
                    <option value="">Select an organization</option>
                    {organizations.map((organization) => (
                        <option key={organization.id} value={organization.id}>
                            {organization.name}
                        </option>
                    ))}
                </select>
            </div>
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
                <label className="block text-sm font-medium text-gray-700 mb-1">Search Volunteers</label>
                <input
                    type="text"
                    className="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Search volunteers..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                />
            </div>
            <div className="mb-4">
                <label className="block text-sm font-medium text-gray-700 mb-1">Select Volunteers</label>
                <div className="space-y-2">
                    {loading && <div>Loading...</div>}
                    {filteredVolunteers.map((volunteer) => (
                        <div key={volunteer.userId} className="flex items-center">
                            <input
                                type="checkbox"
                                id={`volunteer-${volunteer.userId}`}
                                className="form-checkbox h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                checked={selectedVolunteers.includes(volunteer)}
                                onChange={() => handleVolunteerToggle(volunteer)}
                            />
                            <label htmlFor={`volunteer-${volunteer.userId}`} className="ml-2 text-sm text-gray-700">
                                {volunteer.userName} - {volunteer.email}
                            </label>
                        </div>
                    ))}
                </div>
            </div>
            <button
                className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                onClick={handleSendInvitations}
                disabled={!selectedOrganization || selectedVolunteers.length === 0 || !invitationMessage.trim()}
            >
                <MailIcon className="-ml-1 mr-2 h-5 w-5" aria-hidden="true" />
                Send Invitations
            </button>
        </div>
    );
};

InviteForm.propTypes = {
    userId: PropTypes.number.isRequired,
};

export default InviteForm;
