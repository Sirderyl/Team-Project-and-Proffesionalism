import { Link } from 'react-router-dom'

export default function Notification({ message, close, priority, link }) {
    const notificationPriority = {
        low: "bg-green-500",
        medium: "bg-yellow-400",
        high: "bg-red-500"
    };

    return (
        <div className="flex-grow notification shadow-md rounded-md p-2 m-1 relative">
            <div className={`absolute top-0 right-0 h-full ${notificationPriority[priority]} w-2 rounded-tr-md rounded-br-md`}></div>
            <p>{message}</p>
            <button className={"bg-red-500 hover:bg-red-700 text-white mr-2 rounded-md p-1"} onClick={close}>Dismiss</button>
            {link && (
                <Link to={link}>
                    <button className={"bg-green-500 hover:bg-green-700 text-white rounded-md p-1"}>View</button>
                </Link>
            )}
        </div>
    );
}
