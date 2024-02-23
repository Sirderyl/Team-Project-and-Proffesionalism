import { Routes, Route } from 'react-router-dom'
import Home from './pages/Home'
import AccountDetails from './pages/AccountDetails'

function App() {

  return (
    <div className='App'>
      <Routes>
        <Route path='/' element={<Home />} />
        <Route path='/account-details' element={<AccountDetails />} />
      </Routes>
    </div>
  )
}

export default App
