import { Routes, Route } from 'react-router-dom'
import { useCallback, useEffect, useMemo, useState } from 'react'
import Home from './pages/Home'
import AccountDetails from './pages/AccountDetails'
import AddScheduleRecord from './pages/AddScheduleRecord'
import Feedback from './pages/Feedback'
import InviteForm from './pages/InviteForm'
import AssignedTasks from './pages/AssignedTasks'
import ScheduleApprovalPage from './pages/ScheduleApprovalPage'
import Login from './pages/Login'
import SignUp from './pages/SignUp'
import NavMenu from './components/NavMenu'

function App() {
  const [user, setUser] = useState({})
  const [userId] = useState(1)
  const [token, setToken] = useState(localStorage.getItem('token'))
  const [availability, setAvailability] = useState([])
  const [availabilityLoading, setAvailabilityLoading] = useState(true)
  const [userLoading, setUserLoading] = useState(true)

  function handleLogin(token) {
    setToken(token)
    localStorage.setItem('token', token)
  }

  function handleLogout() {
    setToken(null)
    localStorage.removeItem('token')
  }

  const daysOfWeek = useMemo(() => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], [])

  const handleResponse = response => {
    if (response.ok) {
      return response.json()
    } else {
      throw new Error('Error fetching availability: ' + response.status)
    }
  }

  const handleJSON = useCallback((json) => {
    if (json.constructor === Array) {
      json.sort((a, b) => daysOfWeek.indexOf(a.day) - daysOfWeek.indexOf(b.day))
      setAvailability(json)
      setAvailabilityLoading(false)
    } else {
      throw new Error('Invalid JSON: ' + json);
    }
  }, [daysOfWeek])

  const fetchUser = useCallback(() => {
    // Change to `${apiRoot}/user/${userId} in production
    fetch(`https://w20010297.nuwebspace.co.uk/api/user/${userId}`)
      .then(response => handleResponse(response))
      .then(data => {
        setUser(data)
        setUserLoading(false)
      })
  }, [userId])

  const fetchAvailability = useCallback(() => {
    if(user.userId) {
      // Change to `${apiRoot}/user/${userId}/availability` in production
      fetch(`https://w20010297.nuwebspace.co.uk/api/user/${user.userId}/availability`)
        .then(response => handleResponse(response))
        .then(data => handleJSON(data))
        .catch(err => console.error(err))
    }
  }, [user.userId, handleJSON])

  useEffect(() => {
    fetchUser()
    fetchAvailability()
  }, [fetchUser, fetchAvailability])

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
  const isLoggedIn = token !== null

  /**
   * Routes for the app. Set navigable: false to hide a route from the NavMenu while keeping it in the app
   * @type {Array<import('react-router-dom').RouteProps & {navigable?: boolean}>}
   */
  const routes = [
    { path: '/', name: 'Home', element: <Home /> },
    { path: '/login', name: 'Login', element: <Login handleLogin={handleLogin} isLoggedIn={isLoggedIn} />, navigable: !isLoggedIn },
    { path: '/signup', name: 'Sign up', element: <SignUp handleLogin={handleLogin} isLoggedIn={isLoggedIn} />, navigable: !isLoggedIn },
    { path: '/account-details', name: 'Account Details', element: <AccountDetails user={user} availability={availability} setAvailability={setAvailability} isLoading={availabilityLoading} /> },
    { path: '/account-details/add-schedule-record', name: 'Add Schedule Record', element: <AddScheduleRecord userId={user.userId} availability={availability} /> },
  ]

  return (
    <div className='App'>
      <NavMenu
        routes={routes.filter(route => route.navigable !== false)}
        isLoggedIn={token !== null}
        handleLogout={handleLogout}
      />

      <Routes>
        <Route path='/' element={<Home />} />
        {!availabilityLoading && !userLoading && <Route path='/account-details' element={<AccountDetails user={user} availability={availability} setAvailability={setAvailability} isLoading={availabilityLoading} />} />}
        {!userLoading && <Route path='/account-details/add-schedule-record' element={<AddScheduleRecord userId={user.userId} availability={availability} />} />}
        <Route path='/feedback' element={<Feedback />} />
        <Route path='/InviteForm' element={<InviteForm />} />
        <Route path='/AssignedTasks' element={<AssignedTasks tasks={tasks} />} />
        <Route path='/scheduleApproval' element={<ScheduleApprovalPage taskRequests={taskRequests} />} />
        {routes.map((route, index) => (
          <Route key={index} path={route.path} element={route.element} />
        ))}
      </Routes>
    </div>
  )
}

export default App
