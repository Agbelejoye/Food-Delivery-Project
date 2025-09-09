<template>
  <div class="login-page">
    <div class="container-fluid vh-100">
      <div class="row h-100">
        <!-- Left Side - Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
          <div class="login-form-container w-100" style="max-width: 400px;">
            <div class="text-center mb-4">
              <router-link to="/" class="text-decoration-none">
                <h2 class="fw-bold text-primary">
                  <i class="bi bi-basket3-fill me-2"></i>
                  FoodExpress
                </h2>
              </router-link>
              <h3 class="fw-bold text-dark mt-3">Welcome Back!</h3>
              <p class="text-muted">Sign in to your account and start ordering delicious food from your favorite restaurants</p>
            </div>

            <!-- Error Alert -->
            <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i>
              {{ error }}
              <button type="button" class="btn-close" @click="error = null"></button>
            </div>

            <form @submit.prevent="handleLogin" class="login-form">
              <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                  </span>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email"
                    v-model="loginForm.email"
                    placeholder="Enter your email address"
                    required
                    :disabled="isLoading"
                  >
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                  </span>
                  <input 
                    :type="showPassword ? 'text' : 'password'" 
                    class="form-control" 
                    id="password"
                    v-model="loginForm.password"
                    placeholder="Enter your password"
                    required
                    :disabled="isLoading"
                  >
                  <button 
                    class="btn btn-outline-secondary" 
                    type="button"
                    @click="showPassword = !showPassword"
                    :disabled="isLoading"
                  >
                    <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
              </div>

              <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="rememberMe" v-model="loginForm.rememberMe" :disabled="isLoading">
                  <label class="form-check-label" for="rememberMe">
                    Remember me for 30 days
                  </label>
                </div>
                <a href="#" class="text-decoration-none" @click.prevent="handleForgotPassword">Forgot Password?</a>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2 mb-3" :disabled="isLoading">
                <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-box-arrow-in-right me-2"></i>
                {{ isLoading ? 'Signing In...' : 'Sign In' }}
              </button>

              <div class="text-center mb-3">
                <span class="text-muted">or continue with</span>
              </div>

              <div class="row g-2 mb-4">
                <div class="col-6">
                  <button type="button" class="btn btn-outline-danger w-100" @click="handleSocialLogin('google')" :disabled="isLoading">
                    <i class="bi bi-google me-2"></i>Google
                  </button>
                </div>
                <div class="col-6">
                  <button type="button" class="btn btn-outline-primary w-100" @click="handleSocialLogin('facebook')" :disabled="isLoading">
                    <i class="bi bi-facebook me-2"></i>Facebook
                  </button>
                </div>
              </div>

              <div class="text-center">
                <p class="mb-0">Don't have an account? 
                  <router-link to="/register" class="text-decoration-none fw-bold">Create Account</router-link>
                </p>
                <p class="mt-2 mb-0 small text-muted">
                  New to food delivery? <router-link to="/menu" class="text-decoration-none">Browse restaurants</router-link> without signing up
                </p>
              </div>
            </form>
          </div>
        </div>

        <!-- Right Side - Image/Branding -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-primary text-white">
          <div class="text-center">
            <img src="/api/placeholder/400/300" alt="Food delivery illustration" class="img-fluid mb-4 rounded">
            <h3 class="fw-bold mb-3">Delicious Food Awaits</h3>
            <p class="lead mb-4">Join thousands of satisfied customers who trust FoodExpress for fast, reliable food delivery</p>
            <div class="row text-center">
              <div class="col-4">
                <h4 class="fw-bold">500+</h4>
                <small>Restaurants</small>
              </div>
              <div class="col-4">
                <h4 class="fw-bold">50K+</h4>
                <small>Orders</small>
              </div>
              <div class="col-4">
                <h4 class="fw-bold">30min</h4>
                <small>Delivery</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Footer from '@/components/Footer.vue'

const router = useRouter()
const authStore = useAuthStore()

// Form data
const loginForm = ref({
  email: '',
  password: '',
  rememberMe: false
})

const showPassword = ref(false)
const isLoading = ref(false)
const error = ref('')

// Methods
const handleLogin = async () => {
  if (!loginForm.value.email || !loginForm.value.password) {
    error.value = 'Please enter both email and password'
    return
  }

  error.value = ''
  isLoading.value = true

  try {
    const result = await authStore.login({
      email: loginForm.value.email,
      password: loginForm.value.password,
      rememberMe: loginForm.value.rememberMe
    })

    if (result.success) {
      // Redirect based on user role
      const user = authStore.user
      if (user.role === 'admin') {
        router.push('/admin/dashboard')
      } else if (user.role === 'agent') {
        router.push('/agent/dashboard')
      } else if (user.role === 'restaurant') {
        router.push('/restaurant/dashboard')
      } else {
        router.push('/')
      }
    } else {
      error.value = result.message || 'Invalid email or password. Please try again.'
    }
  } catch (err) {
    error.value = 'Login failed. Please check your connection and try again.'
    console.error('Login error:', err)
  } finally {
    isLoading.value = false
  }
}

const handleSocialLogin = (provider) => {
  error.value = ''
  console.log(`Login with ${provider}`)
  // TODO: Implement social login
  error.value = `${provider} login is not available yet. Please use email/password.`
}

const handleForgotPassword = () => {
  router.push('/forgot-password')
}

onMounted(() => {
  // If user is already logged in, redirect to appropriate dashboard
  if (authStore.isAuthenticated) {
    const user = authStore.user
    if (user.role === 'admin') {
      router.push('/admin/dashboard')
    } else if (user.role === 'agent') {
      router.push('/agent/dashboard')
    } else if (user.role === 'restaurant') {
      router.push('/restaurant/dashboard')
    } else {
      router.push('/')
    }
  }
})
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  background: #f8f9fa;
}

.login-form-container {
  padding: 2rem;
  background: white;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  animation: slideUp 0.6s ease-out;
}

.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.input-group-text {
  background-color: #f8f9fa;
  border-color: #dee2e6;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  font-weight: 600;
}

.btn-primary:hover {
  background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  border-color: #dc3545;
  transform: translateY(-1px);
}

.btn-outline-primary:hover {
  background-color: #0d6efd;
  border-color: #0d6efd;
  transform: translateY(-1px);
}

.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .login-form-container {
    padding: 1.5rem;
    margin: 1rem;
  }
  
  .container-fluid {
    padding: 0;
  }
}

@media (max-width: 576px) {
  .login-form-container {
    padding: 1rem;
    margin: 0.5rem;
  }
  
  .row.g-2 {
    flex-direction: column;
  }
  
  .row.g-2 .col-6 {
    width: 100%;
    margin-bottom: 0.5rem;
  }
}
</style>
