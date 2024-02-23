import PropTypes from "prop-types";
import { Link } from "react-router-dom";

/**
 * Navigation menu
 */
export default function NavMenu({ routes }) {
    return (
        <nav>
            <ul>
            {routes.map((route, index) => (
                <li key={index}><Link to={route.path}>{route.name}</Link></li>
            ))}
            </ul>
        </nav>
    )
}
NavMenu.propTypes = {
    routes: PropTypes.arrayOf(PropTypes.shape({
        path: PropTypes.string.isRequired,
        name: PropTypes.string.isRequired
    })).isRequired
}
