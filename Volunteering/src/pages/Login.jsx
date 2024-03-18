import { useState } from 'react'
import { Link } from 'react-router-dom'

import PropTypes from 'prop-types'
import { apiRoot } from '../settings'
import Button from '../components/Button'
import FormField from '../components/FormField'
import useConditionalRedirect from '../hooks/useConditionalRedirect'

/**
 * Login page
 * @author Kieran
 */
export default function Login({
    // Function to handle the login and set the token
    handleLogin,
    isLoggedIn
}) {
    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    // TODO: Clear this when changing credentials, or use a pop-up
    const [error, setError] = useState()

    useConditionalRedirect(isLoggedIn, '/account-details')

    async function handleSubmit(e) {
        e.preventDefault()
        setError('') // Clear any previous errors

        try {
            const response = await fetch(`${apiRoot}/user/login`, {
                headers: { 'Authorization': `Basic ${btoa(`${email}:${password}`)}` },
                method: 'POST'
            })
            const data = await response.json()

            if (!response.ok) throw new Error(data.message)

            handleLogin(data)

            // Only clear the email if the login was successful
            setEmail('')
        } catch (error) {
            console.error(error)
            setError(error.message)
        } finally {
            // Always clear the password
            setPassword('')
        }
    }

    return (
        <main>
            <h1>Login</h1>
            <form onSubmit={handleSubmit}>
                <FormField
                    label='Email'
                    type='email'
                    value={email}
                    setValue={setEmail}
                    required
                />

                <FormField
                    label='Password'
                    type='password'
                    value={password}
                    setValue={setPassword}
                    required
                />

                <Button type='submit'>Log in</Button>
            </form>

            {error && <p className='text-red-700'>{error}</p>}

            <p>New user? <Link to='/signup'>Sign up here</Link></p>
        </main>
    )
}
Login.propTypes = {
    handleLogin: PropTypes.func.isRequired,
    isLoggedIn: PropTypes.bool.isRequired
}
