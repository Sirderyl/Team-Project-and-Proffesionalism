import { useState } from 'react'
import { buttonStyle } from '../utils/styles'
import FormField from '../components/FormField'

/**
 * Signup page
 * @author Kieran
 */
export default function SignUp() {
    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const [confirmPassword, setConfirmPassword] = useState('')
    const [phoneNumber, setPhoneNumber] = useState('')
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
                <FormField
                    label='Email'
                    type='email'
                    value={email}
                    setValue={setEmail}
                    reason='You will use this to log in and receive notifications.'
                />

                <FormField
                    label='Password'
                    type='password'
                    value={password}
                    setValue={setPassword}
                />

                <FormField
                    label='Confirm password'
                    type='password'
                    value={confirmPassword}
                    setValue={setConfirmPassword}
                    required
                />

                <FormField
                    label='Phone Number'
                    type='tel'
                    value={phoneNumber}
                    setValue={setPhoneNumber}
                    reason='For managers to contact you if are needed at short notice.'
                    required
                />

                <button className={buttonStyle} type='submit'>Sign up</button>
            </form>

            {error && <p>{error}</p>}
        </main>
    )
}
