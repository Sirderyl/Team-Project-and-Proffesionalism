import { useState } from 'react'
import { buttonStyle, inputStyle } from '../utils/styles'

/**
 * Signup page
 * @author Kieran
 */
export default function SignUp() {
    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const [confirmPassword, setConfirmPassword] = useState('')
    // TODO: Use the same error handling as in Login. Probably a component to reuse
    const [error, setError] = useState()

    async function handleSubmit(e) {
        e.preventDefault()

        if (password !== confirmPassword) {
            setError('Passwords do not match')
            return
        }

        try {
            const response = await fetch('/api/signup', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            })
            const data = await response.json()

            if (!response.ok) throw new Error(data.message)

        } catch (error) {
            console.error(error)
            setError(error.message)
        } // No finally block, we want to let the user try again if there was an error
    }

    return (
        <main>
            <h1>Sign up</h1>
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

                <label>Confirm password: <input
                    className={inputStyle}
                    type='password'
                    value={confirmPassword}
                    onChange={e => setConfirmPassword(e.target.value)}
                    required
                /></label><br />

                <button className={buttonStyle} type='submit'>Sign up</button>
            </form>

            {error && <p>{error}</p>}
        </main>
    )
}
