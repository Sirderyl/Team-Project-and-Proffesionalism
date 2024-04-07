import { useState, useEffect, useCallback } from 'react';
import { v4 } from 'uuid';
import PropTypes from 'prop-types';
import Notification from './Notification';
import bellIcon from '../assets/bell.png';
import { apiRoot } from '../settings';

export default function NotificationHandler({ tasks, userId }) {
    const [notifications, setNotifications] = useState([]);
    const [showNotifications, setShowNotifications] = useState(false);
    const [invites, setInvites] = useState([]);
    const numberOfNotifications = notifications.length

    const addNotification = useCallback((message, priority, link, dismissible) => {
        setNotifications(prevNotifications => [
            ...prevNotifications, { 
                id: v4(), 
                message, 
                priority, 
                link,
                dismissible
            }
        ]);
    }, [])

    const removeNotification = (id) => {
        setNotifications(notifications.filter(notification => notification.id !== id))
    }

    const toggleShowNotifications = () => {
        setShowNotifications(!showNotifications)
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

    useEffect(() => {
        const pendingTasksNotification = notifications.find(notification => notification.message === "You have been assigned tasks!");
        const pendingInviteNotification = notifications.find(notification => notification.message === "You have pending a invite!");
        // fix: This notification gets duplicated on login, it is fixed if you refresh the page
        // todo: make sure this notification doesn't appear if tasks have already happened
        if (tasks.length !== 0 && !pendingTasksNotification) {
            addNotification("You have been assigned tasks!", "high", "/", false);
        }
        if (invites.length !== 0 && !pendingInviteNotification) {
            addNotification("You have pending a invite!", "low", "/invites", false)
        }
    }, [tasks, addNotification, notifications, invites]);

    return (
        <div className="relative flex items-center">
            <img src={bellIcon} alt="Notification Bell" className="h-10 w-10 cursor-pointer" onClick={toggleShowNotifications} />
            {showNotifications && (
                <div className="border absolute top-full right-0 mt-2 w-64 bg-white shadow-md rounded-md p-2 max-h-64 overflow-y-scroll">
                    <div className="text-blue-700 font-bold">Notifications</div>
                    {notifications.map(notification => (
                        <Notification
                            key={notification.id}
                            message={notification.message}
                            priority={notification.priority}
                            link={notification.link}
                            dismissible={notification.dismissible}
                            close={() => removeNotification(notification.id)}
                        />
                    ))}
                    {numberOfNotifications === 0 && (
                        <div className="text-gray-700">No Notifications to Display</div>
                    )}
                </div>
            )}
            {numberOfNotifications !== 0 && (
                <div className="z-10 absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/3 flex items-center justify-center h-6 w-6 rounded-full bg-red-500 font-bold text-white">{numberOfNotifications}</div>
            )}
            {numberOfNotifications === 0 && (
                <div className="z-10 absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/3 flex items-center justify-center h-6 w-6"></div>
            )}
        </div>
    )
}

NotificationHandler.propTypes = {
    tasks: PropTypes.array.isRequired,
    userId: PropTypes.number.isRequired
};
