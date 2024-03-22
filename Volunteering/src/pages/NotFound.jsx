import { Link } from 'react-router-dom';

/**
 * 404 page for the application.
 */
export default function NotFound() {
  return (
    <main>
      <h1>Page not found</h1>
      <p>The page you are looking for does not exist. <Link to='/'>Return to the home page</Link></p>
    </main>
  )
}
