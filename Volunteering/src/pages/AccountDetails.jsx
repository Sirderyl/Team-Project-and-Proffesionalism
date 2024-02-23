import { DemoContainer } from '@mui/x-date-pickers/internals/demo'
import { AdapterDayjs } from '@mui/x-date-pickers/AdapterDayjs'
import { LocalizationProvider } from '@mui/x-date-pickers/LocalizationProvider'
import { DateTimePicker } from '@mui/x-date-pickers/DateTimePicker'
import profilePicture from '../assets/profile_picture.jpeg'

function AccountDetails() {

    return (
        <div>
            <h1 className="text-3xl font-bold mb-3 ml-5">Account Details</h1>
            <div className="flex flex-row">
                <div className="flex flex-col ml-5">
                    <img src={profilePicture} className="w-40 h-40 rounded-full" />
                </div>
                <div className="flex flex-col ml-5">
                    <p className="text-lg mt-6"><strong>Name: </strong>John Doe</p>
                    <p className="text-lg"><strong>Email: </strong>johndoe@example.com</p>
                    <p className="text-lg"><strong>Phone: </strong>123-456-7890</p>
                    <p className="text-lg"><strong>Address: </strong>1234 Example St.</p>
                </div>
            </div>

            <div className="mt-3 ml-5" style={{ width: '300px' }}>
                <LocalizationProvider dateAdapter={AdapterDayjs}>
                    <DemoContainer components={['DateTimePicker']}>
                        <DateTimePicker label="Basic date time picker" />
                    </DemoContainer>
                </LocalizationProvider>
            </div>
        </div>
    )
}

export default AccountDetails