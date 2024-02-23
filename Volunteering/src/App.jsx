import { Routes, Route } from 'react-router-dom'
import Home from './pages/Home'
import Login from './pages/Login'
import SignUp from './pages/SignUp'
import NavMenu from './components/NavMenu'

function App() {
  const routes = [
    { path: '/', name: 'Home', element: <Home /> },
    { path: '/login', name: 'Login', element: <Login /> },
    { path: '/signup', name: 'Sign up', element: <SignUp /> }
  ]

  return (
    <div className='App'>
      <NavMenu routes={routes} />

      <Routes>
        {routes.map((route, index) => (
          <Route key={index} path={route.path} element={route.element} />
        ))}
       </Routes>
    </div>
  )
}

export default App
