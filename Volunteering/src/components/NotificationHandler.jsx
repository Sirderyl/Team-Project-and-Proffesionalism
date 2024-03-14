import { useState } from 'react';
import { v4 } from 'uuid';
import Notification from './Notification';
import bellIcon from '../../public/bell.png';

export default function NotificationHandler() {
    const [notifications, setNotifications] = useState([
        { id: v4(), message: "Low Priority Notification", priority: "low" },
        { id: v4(), message: "Medium Priority Notification", priority: "medium" },
        { id: v4(), message: "High Priority Notification", priority: "high", link:"/account-details" },
    ]);
    const [showNotifications, setShowNotifications] = useState(false);
    const numberOfNotifications = notifications.length

    /*
    const addNotification = (message, priority, link) => {
        const newNotification = {
            id: v4(),
            message,
            priority,
            link
        }
        setNotifications([...notifications, newNotification])
    }
    */

    const removeNotification = (id) => {
        setNotifications(notifications.filter(notification => notification.id !== id))
    }

    const toggleShowNotifications = () => {
        setShowNotifications(!showNotifications)
    };

    return (
        <div className="relative flex items-center">
            <img src={bellIcon} alt="Notification Bell" className="h-10 w-10 cursor-pointer" onClick={toggleShowNotifications} />
            {showNotifications && (
                <div className="absolute top-full right-0 mt-2 w-64 bg-white shadow-md rounded-md p-2 max-h-64 overflow-y-scroll">
                    <div>Notifications</div>
                    {notifications.map(notification => (
                        <Notification
                            key={notification.id}
                            message={notification.message}
                            priority={notification.priority}
                            link ={notification.link}
                            close={() => removeNotification(notification.id)}
                        />
                    ))}
                    {numberOfNotifications === 0 && (
                        <div>No Notifications to Display</div>
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
