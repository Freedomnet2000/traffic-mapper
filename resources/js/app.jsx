// File: resources/js/app.jsx
import React from 'react'
import { createRoot } from 'react-dom/client'
import Dashboard from './components/Dashboard.js'

const container = document.getElementById('app')
const root = createRoot(container)
root.render(<Dashboard />)
