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
import NeedsLogIn from './pages/NeedsLogIn'
import NotFound from './pages/NotFound'

/** @typedef {import('./types/UserData').UserData} UserData */

function App() {
  /**
   * @type {[UserData | null, function(UserData | null): void]}
   */
  const [userData, setUserData] = useState(JSON.parse(localStorage.getItem('user')))
  function handleLogin(data) {
    setUserData(data)
    localStorage.setItem('user', JSON.stringify(data))
  }

  function handleLogout() {
    setUserData(null)
    localStorage.removeItem('user')
  }

  const [availability, setAvailability] = useState([])
  const [isLoading, setIsLoading] = useState(true)

  const daysOfWeek = useMemo(() => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], [])

  const fetchAvailability = () => {
    if (userData === null) {
      setAvailability([])
      setIsLoading(false)
      return
    }

    // Change to `${apiRoot}/user/${userId}/availability` in production
    fetch(`https://w20010297.nuwebspace.co.uk/api/user/${userData.userId}/availability`)
      .then(response => handleResponse(response))
      .then(data => handleJSON(data))
      .catch(err => {
        console.error(err)
      })
  }

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
      setIsLoading(false)
    } else {
      throw new Error('Invalid JSON: ' + json);
    }
  }, [daysOfWeek])

  useEffect(fetchAvailability, [userData, handleJSON])

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
  const isLoggedIn = userData !== null

  /**
   * Routes for the app. Set navigable: false to hide a route from the NavMenu while keeping it in the app
   * @type {Array<import('react-router-dom').RouteProps & {navigable?: boolean}>}
   */
  const routes = [
    { path: '/', name: 'Home', element: <Home /> },
    { path: '/login', name: 'Login', element: <Login handleLogin={handleLogin} isLoggedIn={isLoggedIn} />, navigable: !isLoggedIn },
    { path: '/signup', name: 'Sign up', element: <SignUp handleLogin={handleLogin} isLoggedIn={isLoggedIn} />, navigable: !isLoggedIn },
    { path: '/account-details', name: 'Account Details', navigable: isLoggedIn,
      element: isLoggedIn ? <AccountDetails userId={userData.userId} availability={availability} setAvailability={setAvailability} isLoading={isLoading} /> : <NeedsLogIn />
    },
    { path: '/account-details/add-schedule-record', name: 'Add Schedule Record', navigable: isLoggedIn,
      element: isLoggedIn ? <AddScheduleRecord userId={userData.userId} availability={availability} /> : <NeedsLogIn />
    },
  ]
  return (
    <div className='App'>
      <NavMenu
        routes={routes.filter(route => route.navigable !== false)}
        isLoggedIn={userData !== null}
        handleLogout={handleLogout}
      />

      <Routes>
        <Route path='/feedback' element={<Feedback />} />
        <Route path='/InviteForm' element={<InviteForm />} />
        <Route path='/AssignedTasks' element={<AssignedTasks tasks={tasks} />} />
        <Route path='/scheduleApproval' element={<ScheduleApprovalPage taskRequests={taskRequests} />} />
        {routes.map((route, index) => (
          <Route key={index} path={route.path} element={route.element} />
        ))}
        <Route path='*' element={<NotFound />} />
      </Routes>
    </div>
  )
}

export default App
