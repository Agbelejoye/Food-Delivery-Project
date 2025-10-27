<template>
  <div class="register-view">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="register-card">
            <div class="card border-0 shadow-lg">
              <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-4">
                  <div class="register-icon mb-3">
                    <i class="bi bi-person-plus-fill text-success"></i>
                  </div>
                  <h2 class="register-title">Create Account</h2>
                  <p class="register-subtitle text-muted">Join FoodExpress and start ordering!</p>
                </div>

                <!-- Registration Form -->
                <form @submit.prevent="handleRegister">
                  <!-- Account Type Selection -->
                  <div class="mb-4">
                    <label class="form-label fw-bold">I want to register as:</label>
                    <div class="account-type-selection">
                      <div class="row g-2">
                        <div class="col-4">
                          <input 
                            type="radio" 
                            class="btn-check" 
                            name="accountType" 
                            id="customer"
                            value="customer"
                            v-model="registerForm.accountType"
                          >
                          <label class="btn btn-outline-primary w-100" for="customer">
                            <i class="bi bi-person d-block mb-1"></i>
                            <small>Customer</small>
                          </label>
                        </div>
                        <div class="col-4">
                          <input 
                            type="radio" 
                            class="btn-check" 
                            name="accountType" 
                            id="restaurant"
                            value="restaurant"
                            v-model="registerForm.accountType"
                          >
                          <label class="btn btn-outline-success w-100" for="restaurant">
                            <i class="bi bi-shop d-block mb-1"></i>
                            <small>Restaurant</small>
                          </label>
                        </div>
                        <div class="col-4">
                          <input 
                            type="radio" 
                            class="btn-check" 
                            name="accountType" 
                            id="agent"
                            value="agent"
                            v-model="registerForm.accountType"
                          >
                          <label class="btn btn-outline-warning w-100" for="agent">
                            <i class="bi bi-bicycle d-block mb-1"></i>
                            <small>Delivery Agent</small>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Personal Information -->
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">First Name</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="registerForm.firstName"
                        placeholder="Enter first name"
                        required
                      >
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">Last Name</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="registerForm.lastName"
                        placeholder="Enter last name"
                        required
                      >
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Email Address</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light">
                        <i class="bi bi-envelope text-muted"></i>
                      </span>
                      <input 
                        type="email" 
                        class="form-control" 
                        v-model="registerForm.email"
                        placeholder="Enter your email"
                        required
                      >
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light">
                        <i class="bi bi-telephone text-muted"></i>
                      </span>
                      <input 
                        type="tel" 
                        class="form-control" 
                        v-model="registerForm.phone"
                        placeholder="Enter your phone number"
                        required
                      >
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light">
                        <i class="bi bi-lock text-muted"></i>
                      </span>
                      <input 
                        :type="showPassword ? 'text' : 'password'" 
                        class="form-control" 
                        v-model="registerForm.password"
                        placeholder="Create a password"
                        required
                      >
                      <button 
                        class="btn btn-outline-secondary" 
                        type="button"
                        @click="togglePassword"
                      >
                        <i :class="showPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                      </button>
                    </div>
                    <div class="password-strength mt-2">
                      <div class="progress" style="height: 4px;">
                        <div 
                          class="progress-bar" 
                          :class="passwordStrengthClass"
                          :style="{ width: passwordStrength + '%' }"
                        ></div>
                      </div>
                      <small class="text-muted">{{ passwordStrengthText }}</small>
                    </div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Confirm Password</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light">
                        <i class="bi bi-lock-fill text-muted"></i>
                      </span>
                      <input 
                        type="password" 
                        class="form-control" 
                        v-model="registerForm.confirmPassword"
                        placeholder="Confirm your password"
                        required
                      >
                    </div>
                    <div v-if="registerForm.confirmPassword && !passwordsMatch" class="text-danger mt-1">
                      <small><i class="bi bi-exclamation-triangle me-1"></i>Passwords do not match</small>
                    </div>
                  </div>

                  <!-- Address (for customers and restaurants) -->
                  <div v-if="registerForm.accountType !== 'agent'" class="mb-3">
                    <label class="form-label fw-bold">Address</label>
                    <textarea 
                      class="form-control" 
                      rows="3"
                      v-model="registerForm.address"
                      placeholder="Enter your full address"
                      required
                    ></textarea>
                  </div>

                  <!-- Restaurant specific fields -->
                  <div v-if="registerForm.accountType === 'restaurant'">
                    <div class="mb-3">
                      <label class="form-label fw-bold">Restaurant Name</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="registerForm.restaurantName"
                        placeholder="Enter restaurant name"
                        required
                      >
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Cuisine Type</label>
                      <select class="form-select" v-model="registerForm.cuisineType" required>
                        <option value="">Select cuisine type</option>
                        <option value="italian">Italian</option>
                        <option value="chinese">Chinese</option>
                        <option value="mexican">Mexican</option>
                        <option value="indian">Indian</option>
                        <option value="american">American</option>
                        <option value="japanese">Japanese</option>
                        <option value="thai">Thai</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                  </div>

                  <!-- Agent specific fields -->
                  <div v-if="registerForm.accountType === 'agent'">
                    <div class="mb-3">
                      <label class="form-label fw-bold">Vehicle Type</label>
                      <select class="form-select" v-model="registerForm.vehicleType" required>
                        <option value="">Select vehicle type</option>
                        <option value="bicycle">Bicycle</option>
                        <option value="motorcycle">Motorcycle</option>
                        <option value="car">Car</option>
                        <option value="scooter">Scooter</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">License Number</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="registerForm.licenseNumber"
                        placeholder="Enter license number"
                        required
                      >
                    </div>
                  </div>

                  <!-- Terms and Conditions -->
                  <div class="mb-4">
                    <div class="form-check">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        id="agreeTerms"
                        v-model="registerForm.agreeTerms"
                        required
                      >
                      <label class="form-check-label" for="agreeTerms">
                        I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> 
                        and <a href="#" class="text-decoration-none">Privacy Policy</a>
                      </label>
                    </div>
                  </div>

                  <!-- Error Message -->
                  <div v-if="errorMessage" class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ errorMessage }}
                  </div>

                  <!-- Register Button -->
                  <button 
                    type="submit" 
                    class="btn btn-success btn-lg w-100 mb-3"
                    :disabled="isLoading || !isFormValid"
                  >
                    <span v-if="isLoading">
                      <span class="spinner-border spinner-border-sm me-2"></span>
                      Creating Account...
                    </span>
                    <span v-else>
                      <i class="bi bi-person-plus me-2"></i>
                      Create Account
                    </span>
                  </button>

                  <!-- Login Link -->
                  <div class="text-center">
                    <p class="mb-0">
                      Already have an account? 
                      <router-link to="/login" class="text-decoration-none fw-bold">
                        Sign in here
                      </router-link>
                    </p>
                    <p class="mt-2 mb-0 small text-muted">
                      Want to explore first? <router-link to="/menu" class="text-decoration-none">Browse restaurants</router-link> without signing up
                    </p>
                  </div>
                </form>
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
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Footer from '@/components/Footer.vue'

const router = useRouter()
const authStore = useAuthStore()

// Form data
const registerForm = ref({
  accountType: 'customer',
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  address: '',
  restaurantName: '',
  cuisineType: '',
  vehicleType: '',
  licenseNumber: '',
  agreeTerms: false
})

const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')

// Computed properties
const passwordsMatch = computed(() => {
  return registerForm.value.password === registerForm.value.confirmPassword
})

const passwordStrength = computed(() => {
  const password = registerForm.value.password
  let strength = 0
  
  if (password.length >= 8) strength += 25
  if (/[A-Z]/.test(password)) strength += 25
  if (/[0-9]/.test(password)) strength += 25
  if (/[^A-Za-z0-9]/.test(password)) strength += 25
  
  return strength
})

const passwordStrengthClass = computed(() => {
  if (passwordStrength.value < 25) return 'bg-danger'
  if (passwordStrength.value < 50) return 'bg-warning'
  if (passwordStrength.value < 75) return 'bg-info'
  return 'bg-success'
})

const passwordStrengthText = computed(() => {
  if (passwordStrength.value < 25) return 'Weak password'
  if (passwordStrength.value < 50) return 'Fair password'
  if (passwordStrength.value < 75) return 'Good password'
  return 'Strong password'
})

const isFormValid = computed(() => {
  const baseValid = registerForm.value.firstName && 
                   registerForm.value.lastName && 
                   registerForm.value.email && 
                   registerForm.value.phone && 
                   registerForm.value.password && 
                   passwordsMatch.value && 
                   registerForm.value.agreeTerms

  if (registerForm.value.accountType === 'restaurant') {
    return baseValid && registerForm.value.restaurantName && registerForm.value.cuisineType && registerForm.value.address
  }
  
  if (registerForm.value.accountType === 'agent') {
    return baseValid && registerForm.value.vehicleType && registerForm.value.licenseNumber
  }
  
  return baseValid && registerForm.value.address
})

// Methods
const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleRegister = async () => {
  errorMessage.value = ''
  isLoading.value = true

  try {
    const result = await authStore.register({
      firstName: registerForm.value.firstName,
      lastName: registerForm.value.lastName,
      email: registerForm.value.email,
      phone: registerForm.value.phone,
      password: registerForm.value.password,
      role: registerForm.value.accountType,
      address: registerForm.value.address,
      restaurantName: registerForm.value.restaurantName,
      cuisineType: registerForm.value.cuisineType,
      vehicleType: registerForm.value.vehicleType,
      licenseNumber: registerForm.value.licenseNumber
    })

    if (result.success) {
      // Redirect based on account type
      if (registerForm.value.accountType === 'restaurant') {
        router.push('/restaurant/dashboard')
      } else if (registerForm.value.accountType === 'agent') {
        router.push('/agent/dashboard')
      } else {
        router.push('/login')
      }
    } else {
      errorMessage.value = result.message || 'Registration failed. Please try again.'
    }
  } catch (error) {
    errorMessage.value = 'Registration failed. Please check your connection and try again.'
    console.error('Registration error:', error)
  } finally {
    isLoading.value = false
  }
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
.navbar-spacer {
  height: 80px;
}

.register-view {
  background: linear-gradient(135deg, var(--black-900) 0%, var(--black-800) 100%);
  min-height: 100vh;
  padding: 2rem 0;
}

.register-card {
  animation: slideUp 0.6s ease-out;
}

.register-icon {
  font-size: 4rem;
}

.register-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-on-dark);
  margin-bottom: 0.5rem;
}

.register-subtitle {
  font-size: 1rem;
}

.account-type-selection .btn-check:checked + .btn {
  background-color: var(--bs-primary);
  border-color: var(--bs-primary);
  color: white;
}

.account-type-selection .btn {
  padding: 1rem 0.5rem;
  text-align: center;
}

.input-group-text {
  border: 1px solid #2a2b2f;
  background: var(--black-800);
}

.form-control, .form-select {
  border: 1px solid #2a2b2f;
  background: var(--black-800);
  color: var(--text-on-dark);
}

.form-control:focus, .form-select:focus {
  border-color: var(--gold-500);
  box-shadow: 0 0 0 0.2rem rgba(201,162,39,0.25);
}

.password-strength .progress {
  border-radius: 10px;
}

.password-strength .progress-bar {
  border-radius: 10px;
  transition: all 0.3s ease;
}

/* Card surface to dark */
.register-card .card {
  background: var(--black-800);
  color: var(--text-on-dark);
  box-shadow: 0 10px 30px rgba(0,0,0,0.45);
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
  .register-view {
    padding: 1rem;
  }
  
  .card-body {
    padding: 2rem !important;
  }
  
  .register-title {
    font-size: 1.75rem;
  }
  
  .register-icon {
    font-size: 3rem;
  }
  
  .account-type-selection .btn {
    padding: 0.75rem 0.25rem;
    font-size: 0.9rem;
  }
}

@media (max-width: 576px) {
  .card-body {
    padding: 1.5rem !important;
  }
  
  .register-title {
    font-size: 1.5rem;
  }
  
  .account-type-selection .row {
    flex-direction: column;
  }
  
  .account-type-selection .col-4 {
    width: 100%;
    margin-bottom: 0.5rem;
  }
}
</style>
