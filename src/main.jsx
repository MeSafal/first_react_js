import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
// import './index.css'
import './bordio-premium.css'
import './SaaS_Override.css'
import App from './App'

createRoot(document.getElementById('root')).render(
  <StrictMode>
    <App />
  </StrictMode>,
)
