import { useState } from 'react'
import { DemoContainer } from '@mui/x-date-pickers/internals/demo'
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider'
import { DateTimePicker } from '@mui/x-date-pickers/DateTimePicker'
import TimeRangePicker from '@wojtekmaj/react-timerange-picker'
import '@wojtekmaj/react-timerange-picker/dist/TimeRangePicker.css'
import 'react-clock/dist/Clock.css'
import { Link } from 'react-router-dom'
import PropTypes from 'prop-types'
import { apiRoot } from '../settings'

export default function AccountDetails(props) {

    const [timeRange, setTimeRange] = useState(['00:00', '23:59'])

    const handleTimeChange = time => {
        setTimeRange(time)
        console.log(time)
    }

    let scheduleTable = props.scheduleRecords.map(item => {
        console.log(item)
        return (
            <tr key={item.ID}>
                <td className='border px-4 py-2'>{item.date.toLocaleDateString("en-GB")}</td>
                <td className='border px-4 py-2'>{item.time_range[0]} - {item.time_range[1]}</td>
            </tr>
        )
    })

    return (
        <div>
            <h1 className="text-3xl font-bold mb-3 ml-5">Account Details</h1>
            <div className="flex flex-row">
                <div className="flex flex-col ml-5">
                    <img src={`${apiRoot}/user/${props.userId}/profilepicture`} className="w-40 h-40 rounded-full" />
                </div>
                <div className="flex flex-col ml-5">
                    <p className="text-lg mt-6"><strong>Name: </strong>John Doe</p>
                    <p className="text-lg"><strong>Email: </strong>johndoe@example.com</p>
                    <p className="text-lg"><strong>Phone: </strong>123-456-7890</p>
                    <p className="text-lg"><strong>Address: </strong>1234 Example St.</p>
                </div>
            </div>

            <div className='mt-10 ml-5'>
                <h1 className='font-bold mb-5'>Availability Schedule</h1>
                <table className='table-auto'>
                    <thead>
                        <tr>
                            <th className='px-4 py-2'>Date</th>
                            <th className='px-4 py-2'>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        {scheduleTable}
                    </tbody>
                </table>

                <Link to='/account-details/add-schedule-record'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5">Add Record</button>
                </Link>
            </div>

            <div className="mt-3 ml-5" style={{ width: '300px' }}>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                    <DemoContainer components={['DateTimePicker']}>
                        <DateTimePicker label="Basic date time picker" />
                    </DemoContainer>
                </LocalizationProvider>
            </div>
            <div className='mt-3 ml-5'>
                <TimeRangePicker onChange={handleTimeChange} value={timeRange} />
            </div>
        </div>
    )
}

AccountDetails.propTypes = {
    scheduleRecords: PropTypes.array.isRequired,
    userId: PropTypes.number.isRequired
}
