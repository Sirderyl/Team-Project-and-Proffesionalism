import { Link } from 'react-router-dom'
import PropTypes from 'prop-types'
import { apiRoot } from '../settings'
import toast, { Toaster } from 'react-hot-toast'

export default function AccountDetails({ userId, availability, setAvailability, isLoading }) {


    const handleDeleteRecord = day => {
        fetch(`https://w20010297.nuwebspace.co.uk/api/user/${userId}/availability/${day}`,
            {
                method: 'DELETE'
            })
            .then(response => {
                if ((response.status === 200 || response.status === 204)) {
                    setAvailability(availability.filter(item => item.day !== day))
                    toast.success('Record deleted successfully')
                } else {
                    toast.error('Error deleting record')
                    throw new Error('Error deleting record: ' + response.status)
                }
            })
            .catch(err => console.error(err))
    }

    let scheduleTable = availability.map(item => {
        return (
            <tr key={item.userId + item.day}>
                <td className='border px-4 py-2'>{item.day}</td>
                <td className='border px-4 py-2'>{item.time.start} - {item.time.end}</td>
                <td className='border px-4 py-2'>
                    <button className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onClick={() => handleDeleteRecord(item.day)}>Delete</button>
                </td>
            </tr>
        )
    })

    return (
        <div>
            <Toaster />
            <h1 className="text-3xl font-bold mb-3 ml-5">Account Details</h1>
            <div className="flex flex-row">
                <div className="flex flex-col ml-5">
                    <img src={`${apiRoot}/user/${userId}/profilepicture`} className="w-40 h-40 rounded-full" />
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
                            <th className='px-4 py-2'>Week Day</th>
                            <th className='px-4 py-2'>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        {!isLoading && scheduleTable}
                    </tbody>
                </table>
                {isLoading && <p className="ml-5 text-slate-700">Loading...</p>}

                <Link to='/account-details/add-schedule-record'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5">Add Record</button>
                </Link>
            </div>
        </div>
    )
}

AccountDetails.propTypes = {
    userId: PropTypes.number.isRequired,
    availability: PropTypes.arrayOf(PropTypes.shape({
        userId: PropTypes.number.isRequired,
        day: PropTypes.string.isRequired,
        time: PropTypes.shape({
            start: PropTypes.number.isRequired,
            end: PropTypes.number.isRequired
        })
    })),
    setAvailability: PropTypes.func.isRequired,
    isLoading: PropTypes.bool.isRequired
}
