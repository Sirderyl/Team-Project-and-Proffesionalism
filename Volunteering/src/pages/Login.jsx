import { useState } from 'react'
import { Link } from 'react-router-dom'

import { inputStyle } from '../utils/styles'

/**
 * Login page
 * @author Kieran
 */
export default function Login() {
    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    // TODO: Clear this when changing credentials, or use a pop-up
    const [error, setError] = useState()

    async function handleSubmit(e) {
        e.preventDefault()

        try {
            const response = await fetch('/api/login', {
                headers: { 'Authorization': `Bearer ${btoa(`${email}:${password}`)}` }
            })
            const data = await response.json()

            if (!response.ok) throw new Error(data.message)

            // TODO: Store token in local storage
            console.log(response)

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
                <label>Email: <input
                    className={inputStyle}
                    type='email'
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    required
                /></label><br />

                <label>Password: <input
                    className={inputStyle}
                    type='password'
                    value={password}
                    onChange={e => setPassword(e.target.value)}
                    required
                /></label><br />

                <button type='submit'>Log in</button>
            </form>

            {error && <p>{error}</p>}

            <p>New user? <Link to='/signup'>Sign up here</Link></p>
        </main>
    )
}
