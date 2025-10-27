<template>
  <div class="restaurant-dashboard">
    <!-- Header -->
    <div class="dashboard-header bg-primary text-white py-4">
      <div class="container-fluid">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h1 class="h3 mb-0">
              <i class="bi bi-shop me-2"></i>Restaurant Dashboard
            </h1>
            <p class="mb-0 opacity-75">Manage your restaurant operations</p>
          </div>
          <div class="col-md-6 text-md-end">
            <div class="d-flex align-items-center justify-content-md-end gap-3">
              <div class="restaurant-status">
                <span class="badge" :class="isOpen ? 'bg-success' : 'bg-danger'">
                  {{ isOpen ? 'Open' : 'Closed' }}
                </span>
              </div>
              <button 
                class="btn btn-outline-light btn-sm"
                @click="toggleRestaurantStatus"
              >
                {{ isOpen ? 'Close Restaurant' : 'Open Restaurant' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid py-4">
      <div class="row g-4">
        <!-- Stats Cards -->
        <div class="col-lg-3 col-md-6">
          <div class="stats-card">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stats-icon bg-primary text-white rounded-circle me-3">
                    <i class="bi bi-receipt"></i>
                  </div>
                  <div>
                    <h3 class="mb-0">{{ stats.todayOrders }}</h3>
                    <p class="text-muted mb-0">Today's Orders</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="stats-card">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stats-icon bg-success text-white rounded-circle me-3">
                    <i class="bi bi-currency-dollar"></i>
                  </div>
                  <div>
                    <h3 class="mb-0">${{ stats.todayRevenue }}</h3>
                    <p class="text-muted mb-0">Today's Revenue</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="stats-card">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stats-icon bg-warning text-white rounded-circle me-3">
                    <i class="bi bi-clock"></i>
                  </div>
                  <div>
                    <h3 class="mb-0">{{ stats.pendingOrders }}</h3>
                    <p class="text-muted mb-0">Pending Orders</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <div class="stats-card">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="stats-icon bg-info text-white rounded-circle me-3">
                    <i class="bi bi-star"></i>
                  </div>
                  <div>
                    <h3 class="mb-0">{{ stats.averageRating }}</h3>
                    <p class="text-muted mb-0">Average Rating</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="bi bi-list-ul me-2"></i>Recent Orders
                </h5>
                <button class="btn btn-outline-primary btn-sm" @click="refreshOrders">
                  <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Order ID</th>
                      <th>Customer</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                      <th>Time</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in recentOrders" :key="order.id">
                      <td>
                        <span class="fw-bold">#{{ order.id }}</span>
                      </td>
                      <td>{{ order.customerName }}</td>
                      <td>
                        <small class="text-muted">{{ order.items }} items</small>
                      </td>
                      <td>
                        <span class="fw-bold">${{ order.total.toFixed(2) }}</span>
                      </td>
                      <td>
                        <span class="badge" :class="getStatusClass(order.status)">
                          {{ order.status }}
                        </span>
                      </td>
                      <td>
                        <small class="text-muted">{{ formatTime(order.time) }}</small>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button 
                            class="btn btn-outline-primary"
                            @click="viewOrder(order.id)"
                          >
                            <i class="bi bi-eye"></i>
                          </button>
                          <button 
                            v-if="order.status === 'pending'"
                            class="btn btn-outline-success"
                            @click="acceptOrder(order.id)"
                          >
                            <i class="bi bi-check"></i>
                          </button>
                          <button 
                            v-if="order.status === 'preparing'"
                            class="btn btn-outline-info"
                            @click="markReady(order.id)"
                          >
                            <i class="bi bi-check2-all"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h5 class="mb-0">
                <i class="bi bi-lightning me-2"></i>Quick Actions
              </h5>
            </div>
            <div class="card-body">
              <div class="d-grid gap-2">
                <button class="btn btn-primary" @click="addMenuItem">
                  <i class="bi bi-plus-circle me-2"></i>Add Menu Item
                </button>
                <button class="btn btn-outline-primary" @click="manageMenu">
                  <i class="bi bi-menu-button-wide me-2"></i>Manage Menu
                </button>
                <button class="btn btn-outline-success" @click="viewReports">
                  <i class="bi bi-graph-up me-2"></i>View Reports
                </button>
                <button class="btn btn-outline-info" @click="manageProfile">
                  <i class="bi bi-gear me-2"></i>Restaurant Settings
                </button>
              </div>
            </div>
          </div>

          <!-- Recent Reviews -->
          <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom">
              <h5 class="mb-0">
                <i class="bi bi-chat-quote me-2"></i>Recent Reviews
              </h5>
            </div>
            <div class="card-body">
              <div v-for="review in recentReviews" :key="review.id" class="review-item mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <h6 class="mb-1">{{ review.customerName }}</h6>
                    <div class="rating">
                      <span v-for="star in 5" :key="star" class="star">
                        <i :class="star <= review.rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
                      </span>
                    </div>
                  </div>
                  <small class="text-muted">{{ formatDate(review.date) }}</small>
                </div>
                <p class="text-muted mb-0 small">{{ review.comment }}</p>
                <hr v-if="review.id !== recentReviews[recentReviews.length - 1].id">
              </div>
            </div>
          </div>
        </div>

        <!-- Sales Chart -->
        <div class="col-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
              <h5 class="mb-0">
                <i class="bi bi-bar-chart me-2"></i>Sales Overview
              </h5>
            </div>
            <div class="card-body">
              <div class="chart-container">
                <canvas id="salesChart" width="400" height="100"></canvas>
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
import Footer from '@/components/Footer.vue'

const router = useRouter()

const isOpen = ref(true)

const stats = ref({
  todayOrders: 47,
  todayRevenue: 1247.50,
  pendingOrders: 8,
  averageRating: 4.7
})

const recentOrders = ref([
  {
    id: 1001,
    customerName: 'John Smith',
    items: 3,
    total: 24.99,
    status: 'pending',
    time: new Date(Date.now() - 5 * 60000) // 5 minutes ago
  },
  {
    id: 1002,
    customerName: 'Sarah Johnson',
    items: 2,
    total: 18.50,
    status: 'preparing',
    time: new Date(Date.now() - 12 * 60000) // 12 minutes ago
  },
  {
    id: 1003,
    customerName: 'Mike Chen',
    items: 4,
    total: 32.75,
    status: 'ready',
    time: new Date(Date.now() - 18 * 60000) // 18 minutes ago
  },
  {
    id: 1004,
    customerName: 'Emily Rodriguez',
    items: 1,
    total: 12.99,
    status: 'delivered',
    time: new Date(Date.now() - 25 * 60000) // 25 minutes ago
  }
])

const recentReviews = ref([
  {
    id: 1,
    customerName: 'Alice Brown',
    rating: 5,
    comment: 'Amazing food! The pasta was perfectly cooked.',
    date: '2024-01-15'
  },
  {
    id: 2,
    customerName: 'David Wilson',
    rating: 4,
    comment: 'Great service and delicious pizza.',
    date: '2024-01-14'
  },
  {
    id: 3,
    customerName: 'Lisa Garcia',
    rating: 5,
    comment: 'Best Italian restaurant in town!',
    date: '2024-01-13'
  }
])

// Methods
const toggleRestaurantStatus = () => {
  isOpen.value = !isOpen.value
  console.log(`Restaurant is now ${isOpen.value ? 'open' : 'closed'}`)
}

const getStatusClass = (status) => {
  const classes = {
    'pending': 'bg-warning',
    'preparing': 'bg-info',
    'ready': 'bg-success',
    'delivered': 'bg-secondary',
    'cancelled': 'bg-danger'
  }
  return classes[status] || 'bg-secondary'
}

const formatTime = (date) => {
  const now = new Date()
  const diff = Math.floor((now - date) / 60000) // difference in minutes
  
  if (diff < 1) return 'Just now'
  if (diff < 60) return `${diff}m ago`
  if (diff < 1440) return `${Math.floor(diff / 60)}h ago`
  return date.toLocaleDateString()
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric' 
  })
}

const refreshOrders = () => {
  console.log('Refreshing orders...')
  // In a real app, this would fetch fresh data from the API
}

const viewOrder = (orderId) => {
  console.log('Viewing order:', orderId)
  // Navigate to order details
}

const acceptOrder = (orderId) => {
  const order = recentOrders.value.find(o => o.id === orderId)
  if (order) {
    order.status = 'preparing'
    console.log('Order accepted:', orderId)
  }
}

const markReady = (orderId) => {
  const order = recentOrders.value.find(o => o.id === orderId)
  if (order) {
    order.status = 'ready'
    console.log('Order marked as ready:', orderId)
  }
}

const addMenuItem = () => {
  console.log('Adding new menu item...')
  // Navigate to add menu item page
}

const manageMenu = () => {
  console.log('Managing menu...')
  // Navigate to menu management page
}

const viewReports = () => {
  console.log('Viewing reports...')
  // Navigate to reports page
}

const manageProfile = () => {
  console.log('Managing restaurant profile...')
  // Navigate to restaurant settings
}

onMounted(() => {
  // Initialize dashboard data
  console.log('Restaurant dashboard loaded')
})
</script>

<style scoped>
.dashboard-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card {
  transition: transform 0.3s ease;
}

.stats-card:hover {
  transform: translateY(-2px);
}

.stats-icon {
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.table th {
  font-weight: 600;
  color: #495057;
  border-bottom: 2px solid #dee2e6;
}

.table td {
  vertical-align: middle;
}

.review-item {
  border-bottom: 1px solid #f0f0f0;
  padding-bottom: 1rem;
}

.review-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.star {
  font-size: 0.8rem;
}

.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}

.btn-group-sm .btn {
  padding: 0.25rem 0.5rem;
  font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .dashboard-header .col-md-6 {
    text-align: center;
    margin-bottom: 1rem;
  }
  
  .dashboard-header .d-flex {
    justify-content: center !important;
  }
  
  .stats-card {
    margin-bottom: 1rem;
  }
  
  .table-responsive {
    font-size: 0.9rem;
  }
  
  .btn-group-sm .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
  }
}

@media (max-width: 576px) {
  .container-fluid {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .stats-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }
  
  .table {
    font-size: 0.8rem;
  }
  
  .btn-group {
    flex-direction: column;
  }
  
  .btn-group .btn {
    margin-bottom: 0.25rem;
  }
}
</style>
