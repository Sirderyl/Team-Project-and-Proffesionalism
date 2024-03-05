import { Routes, Route } from 'react-router-dom'
import { useState } from 'react'
import Home from './pages/Home'
import AccountDetails from './pages/AccountDetails'
import AddScheduleRecord from './pages/AddScheduleRecord'
import InviteForm from './pages/InviteForm'
import AssignedTasks from './pages/AssignedTasks'
import ScheduleApprovalPage from './pages/ScheduleApprovalPage'
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
  const [tasks] = useState([
    {
      id: 0,
      title: "Sample Task",
      description: "Descriptions",
      volunteers: [{ id: 0, name: "Nihal Kejman" }],
      deadline: "2024-03-10",
    },
  ]);
  const [taskRequests] = useState([
    {
      id: 1,
      title: "dog walk",
      description: "Pll.",
      deadline: "2024-03-15",
      requester: "John Doe"
    },
    {
      id: 2,
      title: "Babysit",
      description: "jjj.",
      deadline: "2024-03-20",
      requester: "Jane Smith"
    },
    {
      id: 3,
      title: "Dog walk",
      description: "........",
      deadline: "2024-03-25",
      requester: "Alice Johnson"
    },
  ]);
  return (
    <div className='App'>
      <Routes>
        <Route path='/' element={<Home />} />
        <Route path='/account-details' element={<AccountDetails scheduleRecords={scheduleRecords} />} />
        <Route path='/account-details/add-schedule-record' element={<AddScheduleRecord scheduleRecords={scheduleRecords} />} />
        <Route path='/InviteForm' element={<InviteForm />} /> 
        <Route path='/AssignedTasks' element={<AssignedTasks tasks={tasks} />} />
        <Route path='/scheduleApproval' element={<ScheduleApprovalPage taskRequests={taskRequests} />} />

      </Routes>
    </div>
  )
}

export default App
