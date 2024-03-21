import { Link } from "react-router-dom";

/**
 * 404 page
 * @author Kieran
 */
export default function NotFound() {
    return (
        <main>
            <h1>404 Not Found</h1>
            <p>The page you are looking for does not exist.</p>
            <Link to="/">Return to the home page</Link>
        </main>
    )
}
