import { useState } from 'react'
import { Link, useLocation } from 'react-router-dom'
import PropTypes from 'prop-types'
import { apiRoot } from '../settings'
import toast, { Toaster } from 'react-hot-toast'

export default function UpdateScheduleRecord({ user, availability, setAvailability }) {

    // *** For the time inputs, use the following state variables:
    const [timeStart, setTimeStart] = useState('')
    const [timeEnd, setTimeEnd] = useState('')
    // ***

    // *** For time insertion into the database, use the following state variables:
    const [timeStartDB, setTimeStartDB] = useState('')
    const [timeEndDB, setTimeEndDB] = useState('')
    // ***

    const location = useLocation()
    const day = location.state.day

    const handleTimeStartChange = time => {
        let timeArray = time.split(':')
        let hours = parseInt(timeArray[0])
        setTimeStart(time)
        setTimeStartDB(hours)
    }

    const handleTimeEndChange = time => {
        let timeArray = time.split(':')
        let hours = parseInt(timeArray[0])
        setTimeEnd(time)
        setTimeEndDB(hours)
    }

    const handleUpdateSubmit = () => {
        let formData = new FormData()
        formData.append('day', day)
        formData.append('start', timeStartDB)
        formData.append('end', timeEndDB)

        fetch(`${apiRoot}/user/${user.userId}/availability/update`,
            {
                method: 'POST',
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

    if(user.isManager) {
        return (
            <div>
                <h1 className="text-3xl font-bold mb-3 ml-5">You are not allowed to modify schedule records</h1>
                <Link to='/account-details'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-5">Account Details</button>
                </Link>
            </div>
        )
    } else {
        return (
            <div>
                <Toaster />
                <h1 className="text-3xl font-bold mb-3 ml-5">Update Schedule Record for {day}</h1>
                <div className="flex flex-row">
                    <div className="flex flex-col ml-5">
                        <label htmlFor='timeStartInput' className="text-lg mt-6">Time Start</label>
                        <input id='timeStartInput' type="time" className="border rounded-md p-2 mt-1" value={timeStart} onChange={e => handleTimeStartChange(e.target.value)} />
                    </div>
                    <div className="flex flex-col ml-5">
                        <label htmlFor='timeEndInput' className="text-lg mt-6">Time End</label>
                        <input id='timeEndInput' type="time" className="border rounded-md p-2 mt-1" value={timeEnd} onChange={e => handleTimeEndChange(e.target.value)} />
                    </div>
                </div>
                <div className="flex flex-row ml-5">
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5" onClick={handleUpdateSubmit}>Update Record</button>
                    <Link to='/account-details'>
                        <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-3">Go back</button>
                    </Link>
                </div>
            </div>
        )
    }
}

UpdateScheduleRecord.propTypes = {
    user: PropTypes.object.isRequired,
    availability: PropTypes.array.isRequired,
    setAvailability: PropTypes.func.isRequired
}