import { useState } from 'react'
import { Link } from 'react-router-dom'
import PropTypes from 'prop-types'

export default function AddScheduleRecord(props) {

    const [date, setDate] = useState('')
    const [timeStart, setTimeStart] = useState('')
    const [timeEnd, setTimeEnd] = useState('')

    const handleDateChange = date => {
        setDate(date)
        console.log(date)
    }

    const handleTimeStartChange = time => {
        setTimeStart(time)
        console.log(time)
    }

    const handleTimeEndChange = time => {
        setTimeEnd(time)
        console.log(time)
    }

    const handleAddRecord = () => {
        props.scheduleRecords.push({
            ID: props.scheduleRecords.length,
            date: new Date(date),
            time_range: [timeStart, timeEnd]
        })

        /*
        let newRecord = {
            ID: props.scheduleRecords.length,
            date: new Date(date),
            time_range: [timeStart, timeEnd]
        }
        let newScheduleRecords = [...props.scheduleRecords, newRecord]
        props.setScheduleRecords(newScheduleRecords)
        */
    }

    return (
        <div>
            <h1 className="text-3xl font-bold mb-3 ml-5">Add Schedule Record</h1>
            <div className="flex flex-row">
                <div className="flex flex-col ml-5">
                    <label htmlFor='dateInput' className="text-lg mt-6">Date</label>
                    <input id='dateInput' type="date" className="border rounded-md p-2 mt-1" value={date} onChange={e => handleDateChange(e.target.value)} />
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

AddScheduleRecord.propTypes = {
    scheduleRecords: PropTypes.array.isRequired
}