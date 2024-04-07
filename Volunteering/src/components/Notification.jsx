import { Link } from 'react-router-dom'
import PropTypes from 'prop-types'

export default function Notification({ message, close, priority, link, dismissible }) {
    const notificationPriority = {
        low: "bg-green-500",
        medium: "bg-yellow-400",
        high: "bg-red-400"
    };

    return (
        <div className="border text-gray-700 bg-white flex-grow shadow-md rounded-md p-2 m-1 relative">
            <div className={`absolute top-0 right-0 h-full ${notificationPriority[priority]} w-2 rounded-tr-md rounded-br-md`}></div>
            <p>{message}</p>
            {dismissible &&
                <button className={"bg-red-500 hover:bg-red-700 text-white mr-2 rounded-md p-0.5 pr-2 pl-2 transition duration-300 ease-in-out font-bold"} onClick={close}>Dismiss</button>
            }
            {link && (
                <Link to={link}>
                    <button className={"bg-blue-500 hover:bg-blue-700 text-white rounded-md p-0.5 pr-2 pl-2 transition duration-300 ease-in-out font-bold"}>View</button>
                </Link>
            )}
        </div>
    );
}

Notification.propTypes = {
    message: PropTypes.string.isRequired,
    close: PropTypes.func.isRequired,
    priority: PropTypes.string.isRequired,
    link: PropTypes.string,
    dismissible: PropTypes.bool
}
