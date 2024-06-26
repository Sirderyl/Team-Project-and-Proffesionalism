import { Link, useNavigate } from 'react-router-dom'
import { useState } from 'react'
import PropTypes from 'prop-types'
import { apiRoot } from '../settings'
import toast, { Toaster } from 'react-hot-toast'

export default function AccountDetails({ user, userLoading, availability, setAvailability }) {

    const [isChecked, setIsChecked] = useState(false);
    const navigate = useNavigate()

    const handleDeleteRecord = day => {
        fetch(`${apiRoot}/user/${user.userId}/availability/${day}`,
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

    const handleUpdateRecord = day => {
        navigate('/account-details/update-schedule-record', { state: { day: day } })
    }

    const handleOptInOut = () => {
        setIsChecked(!isChecked);
        if (isChecked) {
            toast.success('You have successfully opted in to leaderboard statistics.');
        } else {
            toast.success('You have successfully opted out of leaderboard statistics.');
        }
    };

    let formattedPhoneNumber = user.phoneNumber
        ? user.phoneNumber.replace(/(\+\d{2})(\d{4})(\d{6})/, '$1 $2 $3')
        : '';

    let scheduleTable = availability.map(item => {
        return (
            <tr key={item.userId + item.day}>
                <td className='border px-4 py-2'>{item.day}</td>
                <td className='border px-4 py-2'>{item.time.start} - {item.time.end}</td>
                <td className='border px-4 py-2'>
                    <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2" onClick={() => handleUpdateRecord(item.day)}>Update</button>
                    <button className="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onClick={() => handleDeleteRecord(item.day)}>Delete</button>
                </td>
            </tr>
        )
    })

    return (
        <div className='max-w-4xl mx-3 mt-8'>
            <Toaster />
            <h1 className="text-3xl font-bold mb-4 ml-3 text-blue-700">Account Details</h1>
            <div className="flex flex-row">
                <div className="flex flex-col ml-5">
                    {!userLoading && <img src={`${apiRoot}/user/${user.userId}/profilepicture`} className="w-40 h-40 rounded-full" />}
                </div>
                <div className="flex flex-col ml-5">
                    <p className="text-lg mt-10"><strong className='text-blue-700'>Name: </strong>{!userLoading && user.userName}</p>
                    <p className="text-lg"><strong className='text-blue-700'>Email: </strong>{!userLoading && user.email}</p>
                    <p className="text-lg"><strong className='text-blue-700'>Phone: </strong>{!userLoading && formattedPhoneNumber}</p>
                </div>
            </div>

            {!userLoading && !user.isManager && (
                <div className='mt-10 ml-5'>
                    <h1 className='font-bold mb-5 text-blue-700'>Availability Schedule</h1>
                    <table className='table-auto'>
                        <thead>
                            <tr>
                                <th className='px-4 py-2 text-blue-700'>Week Day</th>
                                <th className='px-4 py-2 text-blue-700'>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            {scheduleTable}
                        </tbody>
                    </table>

                    <Link to='/account-details/add-schedule-record'>
                        <button className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-5">Add Record</button>
                    </Link>
                    <div className='mt-5'>
                        <input type="checkbox" id="optOut" name="optOut" checked={isChecked} onChange={handleOptInOut} />
                        <label htmlFor="optOut"> Opt out of leaderboard statistics</label>
                    </div>
                </div>
            )}
        </div>
    )
}

AccountDetails.propTypes = {
    user: PropTypes.object.isRequired,
    userLoading: PropTypes.bool.isRequired,
    availability: PropTypes.arrayOf(PropTypes.shape({
        userId: PropTypes.number.isRequired,
        day: PropTypes.string.isRequired,
        time: PropTypes.shape({
            start: PropTypes.number.isRequired,
            end: PropTypes.number.isRequired
        })
    })),
    setAvailability: PropTypes.func.isRequired
}
