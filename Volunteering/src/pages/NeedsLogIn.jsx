import { Link } from 'react-router-dom';

/**
 * Page for when the user needs to log in to access another page.
 */
export default function NeedsLogIn() {
  return (
    <main>
        <h1>Page requires login</h1>
        <p>You must be logged in to view this page. <Link to='/login'>Log in</Link></p>
    </main>
  );
}
