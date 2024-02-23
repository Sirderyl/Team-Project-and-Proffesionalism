import { useState } from 'react'

/**
 * Login page
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
                    type='email'
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    required
                /></label>

                <label>Password: <input
                    type='password'
                    value={password}
                    onChange={e => setPassword(e.target.value)}
                    required
                /></label>

                <button type='submit'>Log in</button>
            </form>

            {error && <p>{error}</p>}
        </main>
    )
}
