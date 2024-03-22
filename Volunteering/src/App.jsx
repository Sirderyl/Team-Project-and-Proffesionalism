import { Routes, Route } from 'react-router-dom'
import { useCallback, useEffect, useMemo, useState } from 'react'
import Home from './pages/Home'
import AccountDetails from './pages/AccountDetails'
import AddScheduleRecord from './pages/AddScheduleRecord'
import Feedback from './pages/Feedback'
import InviteForm from './pages/InviteForm'
import AssignedTasks from './pages/AssignedTasks'
// import ScheduleApprovalPage from './pages/ScheduleApprovalPage'
import Login from './pages/Login'
import SignUp from './pages/SignUp'
import NavMenu from './components/NavMenu'
import NeedsLogIn from './pages/NeedsLogIn'
import NotFound from './pages/NotFound'
import { apiRoot } from './settings'

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

  const daysOfWeek = useMemo(() => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], [])

  const fetchAvailability = () => {
    if (userData === null) {
      setAvailability([])
      return
    }

    // Change to `${apiRoot}/user/${userId}/availability` in production
    fetch(`${apiRoot}/user/${userData.userId}/availability`)
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
    } else {
      throw new Error('Invalid JSON: ' + json);
    }
  }, [daysOfWeek])

  useEffect(fetchAvailability, [userData, handleJSON])


  const isLoggedIn = userData !== null

  const [tasks, setTasks] = useState([]);

  useEffect(() => {
    const fetchTasks = async () => {
      try {
        const response = await fetch('https://w21017158.nuwebspace.co.uk/api/userSchedule/1');
        if (!response.ok) {
          throw new Error('Failed to fetch tasks');
        }
        const data = await response.json();
        setTasks(data);
      } catch (error) {
        console.error('Error fetching tasks:', error);
      }
    };

    fetchTasks();
  }, []);
  /**
   * Routes for the app. Set navigable: false to hide a route from the NavMenu while keeping it in the app
   * @type {Array<import('react-router-dom').RouteProps & {navigable?: boolean}>}
   */
  const routes = [
    { path: '/', name: 'Home', element: <Home /> },
    { path: '/login', name: 'Login', element: <Login handleLogin={handleLogin} isLoggedIn={isLoggedIn} />, navigable: !isLoggedIn },
    { path: '/signup', name: 'Sign up', element: <SignUp handleLogin={handleLogin} isLoggedIn={isLoggedIn} />, navigable: !isLoggedIn },
    { path: '/account-details', name: 'Account Details', navigable: isLoggedIn,
      element: isLoggedIn ? <AccountDetails userData={userData} availability={availability} setAvailability={setAvailability} /> : <NeedsLogIn />
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
        <Route path='/InviteForm' element={<InviteForm userId={userData?.userId} />} />
        <Route path="/AssignedTasks" element={<AssignedTasks tasks={tasks} />} />
        {/* <Route path='/scheduleApproval' element={<ScheduleApprovalPage taskRequests={taskRequests} />} /> */}
        {routes.map((route, index) => (
          <Route key={index} path={route.path} element={route.element} />
        ))}
        <Route path='*' element={<NotFound />} />
      </Routes>
    </div>
  )
}

export default App
