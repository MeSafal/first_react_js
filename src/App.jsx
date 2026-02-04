import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import './App.css'

function App() {
  const [count, setCount] = useState(0)

  return (
    <>

      <h1>Hello world, This is Er. Gokul Subedi</h1>
      <div className="card">
        <button onClick={() => setCount((count) => count + 1)}>
          Try to click {count}
        </button>
        <p>
          Started my React Journey. 
        </p>
        <p>
          I can still click on the count button.
        </p>
      </div>
      <p className="read-the-docs">
        Let's see how it goes
      </p>
    </>
  )
}

export default App
