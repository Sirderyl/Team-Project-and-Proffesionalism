import { Link } from 'react-router-dom'
import PropTypes from 'prop-types'
import { apiRoot } from '../settings'
import toast, { Toaster } from 'react-hot-toast'

export default function UpdateScheduleRecord({ day, user, availability, setAvailability }) {

    const handleUpdateSubmit = () => {
        let formData = new FormData()
        formData.append('userId', user.userId)
        formData.append('day', day)
        formData.append('start', timeStartDB)
        formData.append('end', timeEndDB)

        fetch(`${apiRoot}/user/${user.userId}/availability/${day}`,
            {
                method: 'PUT',
                body: formData
            })
            .then(response => {
                if (response.status === 200) {
                    setAvailability(availability.map(item => {
                        if (item.day === day) {
                            return {
                                userId: user.userId,
                                day: day,
                                time: {
                                    start: timeStartDB,
                                    end: timeEndDB
                                }
                            }
                        }
                        return item
                    }))
                    toast.success('Record updated successfully')
                } else {
                    toast.error('Error updating record')
                    throw new Error('Error updating record: ' + response.status)
                }
            })
            .catch(err => console.error(err))
    }
}