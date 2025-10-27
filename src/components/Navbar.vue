<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow">
    <div class="container">
      <router-link class="navbar-brand fw-bold fs-3" to="/">
        <i class="bi bi-basket3-fill me-2"></i>
        FoodExpress
      </router-link>
      
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/" active-class="active">
              <i class="bi bi-house-door me-1"></i>Home
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/menu" active-class="active">
              <i class="bi bi-grid me-1"></i>Browse Menu
            </router-link>
          </li>
          
          <!-- Show different nav items based on authentication -->
          <template v-if="authStore.isAuthenticated">
            <li class="nav-item">
              <router-link class="nav-link position-relative" to="/cart" active-class="active">
                <i class="bi bi-cart3 me-1"></i>Cart
                <span v-if="cartStore.totalItems > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                  {{ cartStore.totalItems }}
                  <span class="visually-hidden">items in cart</span>
                </span>
              </router-link>
            </li>
            
            <!-- Role-specific navigation -->
            <li v-if="authStore.isAgent" class="nav-item">
              <router-link class="nav-link" to="/agent/dashboard" active-class="active">
                <i class="bi bi-truck me-1"></i>Agent Dashboard
              </router-link>
            </li>
            
            <li v-if="authStore.isAdmin" class="nav-item">
              <router-link class="nav-link" to="/admin/dashboard" active-class="active">
                <i class="bi bi-gear me-1"></i>Admin Panel
              </router-link>
            </li>
            
            <li v-if="authStore.isRestaurant" class="nav-item">
              <router-link class="nav-link" to="/restaurant/dashboard" active-class="active">
                <i class="bi bi-shop me-1"></i>Restaurant Dashboard
              </router-link>
            </li>
            
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle me-1"></i>{{ authStore.user?.first_name || 'Account' }}
              </a>
              <ul class="dropdown-menu">
                <li><router-link class="dropdown-item" to="/profile">
                  <i class="bi bi-person me-2"></i>My Profile
                </router-link></li>
                <li><router-link class="dropdown-item" to="/orders">
                  <i class="bi bi-clock-history me-2"></i>Order History
                </router-link></li>
                <li v-if="authStore.isAgent"><router-link class="dropdown-item" to="/agent-earnings">
                  <i class="bi bi-currency-dollar me-2"></i>My Earnings
                </router-link></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" @click="handleLogout">
                  <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a></li>
              </ul>
            </li>
          </template>
          
          <!-- Show login/register for non-authenticated users -->
          <template v-else>
            <li class="nav-item">
              <router-link class="nav-link" to="/login" active-class="active">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link btn btn-outline-light ms-2" to="/register">
                <i class="bi bi-person-plus me-1"></i>Sign Up
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router = useRouter()

const handleLogout = () => {
  authStore.logout()
  cartStore.clearCart()
  router.push('/')
}

onMounted(() => {
  authStore.initializeAuth()
  if (authStore.isAuthenticated) {
    cartStore.fetchCart()
  }
})
</script>

<style scoped>
.navbar {
  backdrop-filter: blur(10px);
  background: linear-gradient(135deg, var(--black-900) 0%, var(--black-800) 100%) !important;
}

.navbar-brand {
  font-size: 1.8rem;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.nav-link {
  font-weight: 500;
  transition: all 0.3s ease;
  border-radius: 8px;
  margin: 0 2px;
}

.nav-link:hover {
  background-color: rgba(201,162,39,0.12);
  transform: translateY(-2px);
}

.nav-link.active {
  background-color: rgba(201,162,39,0.18);
  font-weight: 600;
}

.navbar-toggler {
  padding: 0.25rem 0.5rem;
}

.navbar-toggler:focus {
  box-shadow: 0 0 0 0.25rem rgba(201,162,39,0.25);
}

.dropdown-menu {
  border: none;
  box-shadow: 0 10px 30px rgba(0,0,0,0.45);
  border-radius: 12px;
}

.dropdown-item {
  padding: 0.7rem 1.5rem;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background-color: rgba(201,162,39,0.12);
  transform: translateX(5px);
}

@media (max-width: 991.98px) {
  .navbar-nav {
    padding-top: 1rem;
  }
  
  .nav-item {
    margin: 0.2rem 0;
  }
  
  .dropdown-menu {
    border: 1px solid rgba(201,162,39,0.15);
    background-color: rgba(11,11,12,0.95);
  }
}
</style>
