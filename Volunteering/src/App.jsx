import { Routes, Route } from 'react-router-dom'
import { useState } from 'react'
import Home from './pages/Home'
import AccountDetails from './pages/AccountDetails'
import AddScheduleRecord from './pages/AddScheduleRecord'
import Feedback from './pages/Feedback'

function App() {

  const [scheduleRecords] = useState([
    {
      ID: 0,
      date: new Date('2024-02-26'),
      time_range: ['13:00', '15:00']
    },
    {
      ID: 1,
      date: new Date('2024-02-27'),
      time_range: ['10:00', '12:00']
    }
  ])

  return (
    <div className='App'>
      <Routes>
        <Route path='/' element={<Home />} />
        <Route path='/account-details' element={<AccountDetails scheduleRecords={scheduleRecords} />} />
        <Route path='/account-details/add-schedule-record' element={<AddScheduleRecord scheduleRecords={scheduleRecords} />} />
        <Route path='/feedback' element={<Feedback />} />
      </Routes>
    </div>
  )
}

export default App
