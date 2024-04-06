import { useState } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';
import { apiRoot } from '../settings';
import Button from '../components/Button';
import FormField from '../components/FormField';
import useConditionalRedirect from '../hooks/useConditionalRedirect';

/**
 * Login page
 * @author Kieran
 */
export default function Login({
    // Function to handle the login and set the token
    handleLogin,
    isLoggedIn
}) {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    useConditionalRedirect(isLoggedIn, '/account-details');

    async function handleSubmit(e) {
        e.preventDefault();
        setError('') // Clear any previous errors

        try {
            const response = await fetch(`${apiRoot}/user/login`, {
                headers: { 'Authorization': `Basic ${btoa(`${email}:${password}`)}` },
                method: 'POST'
            });
            const data = await response.json();

            if (!response.ok) throw new Error(data.error);

            handleLogin(data);
            setEmail('');
        } catch (error) {
            console.error(error);
            setError(error.message);
        } finally {
            setPassword('');
        }
    }

    return (
        <main className="max-w-md mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
            <h1 className="text-3xl font-bold mb-4 text-blue-700">Login</h1>
            <form onSubmit={handleSubmit}>
                <FormField
                    label='Email'
                    type='email'
                    value={email}
                    setValue={setEmail}
                    required
                    className="mb-4"
                />

                <FormField
                    label='Password'
                    type='password'
                    value={password}
                    setValue={setPassword}
                    required
                    className="mb-4"
                />

                <Button type='submit' className="bg-blue-600 text-white hover:bg-blue-700 hover:text-white transition duration-300 ease-in-out">Log in</Button>
            </form>

            {error && <p className="text-red-700 mt-4">{error}</p>}

            <p className="mt-4">New user? <Link to='/signup' className="text-blue-600 hover:underline">Sign up here</Link></p>
        </main>
    );
}

Login.propTypes = {
    handleLogin: PropTypes.func.isRequired,
    isLoggedIn: PropTypes.bool.isRequired
};
