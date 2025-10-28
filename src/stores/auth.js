import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))
  const isLoading = ref(false)
  const error = ref(null)

  // Configurable API base URL
  const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isCustomer = computed(() => user.value?.role === 'customer')
  const isAgent = computed(() => user.value?.role === 'agent')
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isRestaurant = computed(() => user.value?.role === 'restaurant')

  const login = async (credentials) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await fetch(`${API_BASE}/api/auth/login`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(credentials)
      })
      
      if (!response.ok) {
        // Try to read JSON error, otherwise throw generic
        let errMsg = 'Login failed'
        try { const j = await response.json(); errMsg = j.message || errMsg } catch {}
        throw new Error(errMsg)
      }
      const data = await response.json()
      
      if (data.success) {
        token.value = data.data.token
        user.value = data.data.user
        localStorage.setItem('token', data.data.token)
        localStorage.setItem('user', JSON.stringify(data.data.user))
        return { success: true }
      } else {
        error.value = data.message
        return { success: false, message: data.message }
      }
    } catch (err) {
      error.value = err?.message || 'Network error. Please try again.'
      return { success: false, message: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const register = async (userData) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await fetch(`${API_BASE}/api/auth/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData)
      })
      
      if (!response.ok) {
        let errPayload = { message: 'Registration failed' }
        try { errPayload = await response.json() } catch {}
        const msg = errPayload.message || 'Registration failed'
        throw Object.assign(new Error(msg), { payload: errPayload })
      }
      const data = await response.json()
      
      if (data.success) {
        token.value = data.data.token
        user.value = data.data.user
        localStorage.setItem('token', data.data.token)
        localStorage.setItem('user', JSON.stringify(data.data.user))
        return { success: true }
      } else {
        error.value = data.message
        return { success: false, message: data.message, errors: data.errors }
      }
    } catch (err) {
      // Surface server validation errors if present
      const msg = err?.message || 'Network error. Please try again.'
      const payload = err?.payload
      error.value = msg
      return { success: false, message: msg, errors: payload?.errors }
    } finally {
      isLoading.value = false
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  const initializeAuth = () => {
    const storedToken = localStorage.getItem('token')
    const storedUser = localStorage.getItem('user')
    
    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  const getAuthHeaders = () => {
    return {
      'Authorization': `Bearer ${token.value}`,
      'Content-Type': 'application/json'
    }
  }

  return {
    user,
    token,
    isLoading,
    error,
    isAuthenticated,
    isCustomer,
    isAgent,
    isAdmin,
    isRestaurant,
    login,
    register,
    logout,
    initializeAuth,
    getAuthHeaders
  }
})
