import { Link } from "react-router-dom";

/**
 * Placeholder page for when the user is not logged in.
 * @author Kieran
 */
export default function NeedsLogin() {
    return (
        <main>
            <h1>Login Required</h1>
            <p>You must be logged in to view this page.</p>
            <Link to="/login">Login</Link>
        </main>
    )
}
