import PropTypes from "prop-types";
import { Link } from "react-router-dom";
import NotificationHandler from './NotificationHandler';

/**
 * Navigation menu
 * @author Kieran
 */
export default function NavMenu({
    routes,
    isLoggedIn,
    handleLogout,
    tasks,
    userId
}) {
    return (
        <nav>
            <ul className="bg-blue-500 flex md:flex-row justify-evenly text-white text-center items-center p-2">
            {routes.map((route, index) => (
                <li className="hover:bg-blue-700 rounded p-2 transition duration-300 ease-in-out" key={index}><Link to={route.path}>{route.name}</Link></li>
            ))}
            {isLoggedIn && (
            <>
                <li><button className="hover:bg-blue-700 rounded p-2 transition duration-300 ease-in-out" onClick={handleLogout}>Log Out</button></li>
                <li className="text-left"><NotificationHandler tasks={tasks} userId={userId}/></li>
            </>
            )}
            </ul>
        </nav>
    )
}
NavMenu.propTypes = {
    routes: PropTypes.arrayOf(PropTypes.shape({
        path: PropTypes.string.isRequired,
        name: PropTypes.string.isRequired
    })).isRequired,
    isLoggedIn: PropTypes.bool.isRequired,
    handleLogout: PropTypes.func.isRequired,
    tasks: PropTypes.array.isRequired,
    userId: PropTypes.number.isRequired
}
