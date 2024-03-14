import PropTypes from "prop-types";
import { Link } from "react-router-dom";
import NotificationHandler from "./NotificationHandler";

/**
 * Navigation menu
 * @author Kieran
 */
export default function NavMenu({
    routes,
    isLoggedIn,
    handleLogout
}) {
    return (
        <nav>
            <ul className="flex flex-col md:flex-row justify-evenly">
            {routes.map((route, index) => (
                <li key={index}><Link to={route.path}>{route.name}</Link></li>
            ))}
            {isLoggedIn && <li><button onClick={handleLogout}>Log Out</button></li>}
            <li><NotificationHandler/></li>
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
    handleLogout: PropTypes.func.isRequired
}
