import { useEffect } from 'react'
import { useNavigate } from 'react-router-dom'

/**
 * Hook to redirect if a condition is met
 */
export default function useConditionalRedirect(condition, path) {
    const navigate = useNavigate()
    useEffect(() => {
        if (condition) navigate(path)
    }, [condition, navigate, path])
}
