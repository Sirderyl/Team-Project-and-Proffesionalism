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
        //let minutes = parseInt(timeArray[1])
        //let timeInMinutes = hours * 60 + minutes
        setTimeStart(time)
        setTimeStartDB(hours)
        //console.log(timeInMinutes)
    }

    const handleTimeEndChange = time => {
        let timeArray = time.split(':')
        let hours = parseInt(timeArray[0])
        //let minutes = parseInt(timeArray[1])
        //let timeInMinutes = hours * 60 + minutes
        setTimeEnd(time)
        setTimeEndDB(hours)
        //console.log(timeInMinutes)
    }

    const handleAddRecord = () => {
        /*
        props.scheduleRecords.push({
            ID: props.scheduleRecords.length,
            date: new Date(date),
            time_range: [timeStart, timeEnd]
        })
        */

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
            <div>
                <h1 className="text-3xl font-bold mb-3 ml-5">You are not allowed to add schedule records</h1>
                <Link to='/account-details'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5 ml-5">Account Details</button>
                </Link>
            </div>
        )
    } else {
        return (
            <div>
                <Toaster />
                <h1 className="text-3xl font-bold mb-3 ml-5">Add Schedule Record</h1>
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