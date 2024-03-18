import { useState } from 'react'
import PropTypes from 'prop-types'

import { apiRoot } from '../settings'
import Button from '../components/Button'
import FormField from '../components/FormField'
import useConditionalRedirect from '../hooks/useConditionalRedirect'

/**
 * Signup page
 * @author Kieran
 */
export default function SignUp({
    handleLogin,
    isLoggedIn
}) {
    const [name, setName] = useState('')
    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const [confirmPassword, setConfirmPassword] = useState('')
    const [phoneNumber, setPhoneNumber] = useState('')
    // TODO: Use the same error handling as in Login. Probably a component to reuse
    const [error, setError] = useState()

    useConditionalRedirect(isLoggedIn, '/account-details')

    async function handleSubmit(e) {
        e.preventDefault()
        setError('') // Clear any previous errors

        if (password !== confirmPassword) {
            setError('Passwords do not match')
            return
        }

        try {
            const response = await fetch(`${apiRoot}/user/register`, {
                method: 'POST',
                body: JSON.stringify({ name, email, password, phone: phoneNumber })
            })
            const data = await response.json()

            if (!response.ok) throw new Error(data.error)

            handleLogin(data)

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
                    label='Name'
                    value={name}
                    setValue={setName}
                    type='text'
                    reason='Your full name, as it will appear to others.'
                    required
                />

                <FormField
                    label='Email'
                    type='email'
                    value={email}
                    setValue={setEmail}
                    reason='You will use this to log in and receive notifications.'
                    required
                />

                <FormField
                    label='Password'
                    type='password'
                    value={password}
                    setValue={setPassword}
                    required
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

                <Button type='submit'>Sign up</Button>
            </form>

            {error && <p className='text-red-700'>{error}</p>}
        </main>
    )
}
SignUp.propTypes = {
    handleLogin: PropTypes.func.isRequired,
    isLoggedIn: PropTypes.bool.isRequired
}
