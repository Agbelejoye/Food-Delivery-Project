import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/cart',
      name: 'cart',
      component: () => import('../views/CartView.vue'),
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: () => import('../views/CheckoutView.vue'),
    },
    {
      path: '/order-tracking/:id?',
      name: 'order-tracking',
      component: () => import('../views/OrderTrackingView.vue'),
    },
    {
      path: '/agent/dashboard',
      name: 'agent-dashboard',
      component: () => import('../views/AgentDashboard.vue'),
    },
    {
      path: '/admin/dashboard',
      name: 'admin-dashboard',
      component: () => import('../views/AdminDashboard.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegisterView.vue'),
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue'),
    },
    {
      path: '/menu',
      name: 'menu',
      component: () => import('../views/MenuView.vue'),
    },
    {
      path: '/orders',
      name: 'orders',
      component: () => import('../views/OrderHistoryView.vue'),
    },
    {
      path: '/restaurants',
      name: 'restaurants',
      component: () => import('../views/RestaurantsView.vue'),
    },
    {
      path: '/restaurants/:id',
      name: 'restaurant-detail',
      component: () => import('../views/RestaurantDetailView.vue'),
    },
    {
      path: '/restaurant/dashboard',
      name: 'restaurant-dashboard',
      component: () => import('../views/RestaurantDashboard.vue'),
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('../views/ForgotPasswordView.vue'),
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/contact',
      name: 'contact',
      component: () => import('../views/ContactView.vue'),
    },
  ],
})

export default router
