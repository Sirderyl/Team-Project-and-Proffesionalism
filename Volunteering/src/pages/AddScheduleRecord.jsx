import { useState } from 'react'
import { Link } from 'react-router-dom'
import { apiRoot } from '../settings'
import toast, { Toaster } from 'react-hot-toast'
import PropTypes from 'prop-types'

export default function AddScheduleRecord({ user, availability }) {

    // *** For the time inputs, use the following state variables:
    const [timeStart, setTimeStart] = useState('')
    const [timeEnd, setTimeEnd] = useState('')
    // ***

    // *** For time insertion into the database, use the following state variables:
    const [timeStartDB, setTimeStartDB] = useState('')
    const [timeEndDB, setTimeEndDB] = useState('')
    // ***

    const [selectListValue, setSelectListValue] = useState('Monday')

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

    const handleAddRecord = () => {
        // Check if the selected day already exists in the user's availability
        const dayExists = availability.find(item => item.day === selectListValue && item.userId === user.userId)
        if (dayExists) {
            toast.error('You already have a schedule record for this day')
            return
        }

        let formData = new FormData()
        formData.append('userId', user.userId)
        formData.append('day', selectListValue)
        formData.append('start', timeStartDB)
        formData.append('end', timeEndDB)

        fetch(`${apiRoot}/user/${user.userId}/availability`,
            {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!(response.status === 200 || response.status === 201 || response.status === 204)) {
                    toast.error('Error adding record')
                    throw new Error('Error adding record: ' + response.status)
                } else {
                    availability.push({
                        userId: user.userId,
                        day: selectListValue,
                        time: {
                            start: timeStartDB,
                            end: timeEndDB
                        }
                    })
                    toast.success('Record added successfully')
                }
            })
            .catch(err => console.error(err))
    }

    if(user.isManager) {
        return (
            <div className='max-w-4xl mx-3 mt-8'>
                <h1 className="text-3xl font-bold mb-3 ml-5">You are not allowed to add schedule records</h1>
                <Link to='/account-details'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-5">Account Details</button>
                </Link>
            </div>
        )
    } else {
        return (
            <div className='max-w-4xl mx-3 mt-8'>
                <Toaster />
                <h1 className="text-3xl font-bold mb-4 ml-5 text-blue-700">Add Schedule Record</h1>
                <div className="flex flex-row">
                    <div className='flex flex-col ml-5'>
                        <label htmlFor='daySelect' className="text-lg mt-6">Week Day</label>
                        <select className='border rounded-md p-2 mt-1' value={selectListValue} onChange={e => setSelectListValue(e.target.value)}>
                            <option value='Monday'>Monday</option>
                            <option value='Tuesday'>Tuesday</option>
                            <option value='Wednesday'>Wednesday</option>
                            <option value='Thursday'>Thursday</option>
                            <option value='Friday'>Friday</option>
                            <option value='Saturday'>Saturday</option>
                            <option value='Sunday'>Sunday</option>
                        </select>
                    </div>
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
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5" onClick={handleAddRecord}>Add Record</button>
                    <Link to='/account-details'>
                        <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-3">Go back</button>
                    </Link>
                </div>
            </div>
        )
    }

}

AddScheduleRecord.propTypes = {
    user: PropTypes.object.isRequired,
    availability: PropTypes.arrayOf(PropTypes.shape({
        userId: PropTypes.number.isRequired,
        day: PropTypes.string.isRequired,
        time: PropTypes.shape({
            start: PropTypes.number.isRequired,
            end: PropTypes.number.isRequired
        })
    }))
}