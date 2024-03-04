import { Routes, Route } from 'react-router-dom'
import { useState } from 'react'
import Home from './pages/Home'
import AccountDetails from './pages/AccountDetails'
import AddScheduleRecord from './pages/AddScheduleRecord'
import Login from './pages/Login'
import SignUp from './pages/SignUp'
import NavMenu from './components/NavMenu'

function App() {
  const [token, setToken] = useState(localStorage.getItem('token'))
  function handleLogin(token) {
    setToken(token)
    localStorage.setItem('token', token)
  }

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

  // Routes for the app. Set navigable: false to hide a route from the NavMenu while keeping it in the app
  const routes = [
    { path: '/', name: 'Home', element: <Home /> },
    { path: '/login', name: 'Login', element: <Login handleLogin={handleLogin} />, navigable: token === null },
    { path: '/signup', name: 'Sign up', element: <SignUp handleLogin={handleLogin} />, navigable: token === null },
    { path: '/account-details', name: 'Account Details', element: <AccountDetails scheduleRecords={scheduleRecords} /> },
    { path: '/account-details/add-schedule-record', name: 'Add Schedule Record', element: <AddScheduleRecord scheduleRecords={scheduleRecords} /> },
  ]

  return (
    <div className='App'>
      <NavMenu routes={routes.filter(route => route.navigable !== false)} />

      <Routes>
        {routes.map((route, index) => (
          <Route key={index} path={route.path} element={route.element} />
        ))}
       </Routes>
    </div>
  )
}

export default App
