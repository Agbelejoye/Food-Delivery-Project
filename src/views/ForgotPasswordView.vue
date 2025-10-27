<template>
  <div class="forgot-password-page">
    <div class="container-fluid vh-100">
      <div class="row h-100">
        <!-- Left Side - Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
          <div class="forgot-password-form-container w-100" style="max-width: 400px;">
            <div class="text-center mb-4">
              <router-link to="/" class="text-decoration-none">
                <h2 class="fw-bold text-primary">
                  <i class="bi bi-basket3-fill me-2"></i>
                  FoodExpress
                </h2>
              </router-link>
              <h3 class="fw-bold text-dark mt-3">Forgot Password?</h3>
              <p class="text-muted">Enter your email address and we'll send you a link to reset your password</p>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle me-2"></i>
              {{ successMessage }}
              <button type="button" class="btn-close" @click="successMessage = ''"></button>
            </div>

            <!-- Error Alert -->
            <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle me-2"></i>
              {{ error }}
              <button type="button" class="btn-close" @click="error = ''"></button>
            </div>

            <form @submit.prevent="handleForgotPassword" class="forgot-password-form">
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
                    v-model="email"
                    placeholder="Enter your email address"
                    required
                    :disabled="isLoading"
                  >
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2 mb-3" :disabled="isLoading">
                <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                <i v-else class="bi bi-arrow-clockwise me-2"></i>
                {{ isLoading ? 'Sending Reset Link...' : 'Send Reset Link' }}
              </button>

              <div class="text-center">
                <p class="mb-0">Remember your password? 
                  <router-link to="/login" class="text-decoration-none fw-bold">Sign In</router-link>
                </p>
                <p class="mt-2 mb-0 small text-muted">
                  Don't have an account? <router-link to="/register" class="text-decoration-none">Create Account</router-link>
                </p>
              </div>
            </form>
          </div>
        </div>

        <!-- Right Side - Image/Info -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-primary text-white">
          <div class="text-center">
            <img src="/api/placeholder/400/300" alt="Password reset illustration" class="img-fluid mb-4 rounded">
            <h3 class="fw-bold mb-3">Secure Password Reset</h3>
            <p class="lead mb-4">We'll help you get back to ordering your favorite meals in no time</p>
            <div class="row text-center">
              <div class="col-4">
                <h4 class="fw-bold">🔒</h4>
                <small>Secure</small>
              </div>
              <div class="col-4">
                <h4 class="fw-bold">⚡</h4>
                <small>Fast</small>
              </div>
              <div class="col-4">
                <h4 class="fw-bold">✉️</h4>
                <small>Email Link</small>
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
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import Footer from '@/components/Footer.vue'

const router = useRouter()

const email = ref('')
const isLoading = ref(false)
const error = ref('')
const successMessage = ref('')

const handleForgotPassword = async () => {
  if (!email.value) {
    error.value = 'Please enter your email address'
    return
  }

  error.value = ''
  successMessage.value = ''
  isLoading.value = true

  try {
    // Simulate API call for password reset
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Mock success response
    successMessage.value = `Password reset link has been sent to ${email.value}. Please check your inbox and follow the instructions.`
    email.value = ''
    
  } catch (err) {
    error.value = 'Failed to send reset link. Please try again later.'
    console.error('Forgot password error:', err)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.forgot-password-page {
  min-height: 100vh;
  background: var(--black-900);
}

.forgot-password-form-container {
  padding: 2rem;
  background: var(--black-800);
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
  animation: slideUp 0.6s ease-out;
}

.form-control:focus {
  border-color: var(--gold-500);
  box-shadow: 0 0 0 0.2rem rgba(201, 162, 39, 0.25);
}

.input-group-text {
  background-color: var(--black-800);
  border-color: #2a2b2f;
}

.btn-primary {
  background: linear-gradient(135deg, var(--gold-500) 0%, var(--gold-600) 100%);
  border: none;
  font-weight: 600;
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--gold-600) 0%, var(--gold-700) 100%);
  transform: translateY(-1px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.bg-primary {
  background: linear-gradient(135deg, var(--black-900) 0%, var(--black-800) 100%) !important;
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
  .forgot-password-form-container {
    padding: 1.5rem;
    margin: 1rem;
  }
  
  .container-fluid {
    padding: 0;
  }
}

@media (max-width: 576px) {
  .forgot-password-form-container {
    padding: 1rem;
    margin: 0.5rem;
  }
}
</style>
