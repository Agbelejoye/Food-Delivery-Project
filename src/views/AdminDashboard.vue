<template>
  <div class="admin-dashboard">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container-fluid py-4">
      <!-- Dashboard Header -->
      <div class="dashboard-header mb-4">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="dashboard-title">
              <i class="bi bi-shield-check me-3"></i>Admin Dashboard
            </h2>
            <p class="dashboard-subtitle">Manage your food delivery platform</p>
          </div>
          <div class="col-md-4 text-md-end">
            <div class="quick-actions">
              <button class="btn btn-primary me-2" @click="addNewRestaurant">
                <i class="bi bi-plus-circle me-1"></i>Add Restaurant
              </button>
              <button class="btn btn-success" @click="viewReports">
                <i class="bi bi-graph-up me-1"></i>Reports
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Overview Stats -->
      <div class="stats-section mb-4">
        <div class="row g-3">
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-primary text-white">
              <div class="stat-icon">
                <i class="bi bi-cart-check"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">{{ overviewStats.totalOrders }}</h3>
                <p class="stat-label">Total Orders Today</p>
                <small class="stat-change text-success">
                  <i class="bi bi-arrow-up"></i>+12% from yesterday
                </small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-success text-white">
              <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">${{ overviewStats.totalRevenue.toLocaleString() }}</h3>
                <p class="stat-label">Total Revenue</p>
                <small class="stat-change text-success">
                  <i class="bi bi-arrow-up"></i>+8% from last week
                </small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-warning text-white">
              <div class="stat-icon">
                <i class="bi bi-shop"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">{{ overviewStats.activeRestaurants }}</h3>
                <p class="stat-label">Active Restaurants</p>
                <small class="stat-change text-info">
                  <i class="bi bi-arrow-right"></i>2 pending approval
                </small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-info text-white">
              <div class="stat-icon">
                <i class="bi bi-people"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">{{ overviewStats.activeAgents }}</h3>
                <p class="stat-label">Active Delivery Agents</p>
                <small class="stat-change text-success">
                  <i class="bi bi-arrow-up"></i>5 online now
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
          <!-- Recent Orders -->
          <div class="card border-0 shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="bi bi-clock-history me-2"></i>Recent Orders
              </h5>
              <div class="order-filters">
                <select class="form-select form-select-sm bg-light text-dark" v-model="orderFilter">
                  <option value="all">All Orders</option>
                  <option value="pending">Pending</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="delivered">Delivered</option>
                </select>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Order ID</th>
                      <th>Customer</th>
                      <th>Restaurant</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Time</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="order in filteredOrders" :key="order.id">
                      <td class="fw-bold">#{{ order.id }}</td>
                      <td>{{ order.customerName }}</td>
                      <td>{{ order.restaurant }}</td>
                      <td class="text-success fw-bold">${{ order.amount.toFixed(2) }}</td>
                      <td>
                        <span class="badge" :class="getOrderStatusClass(order.status)">
                          {{ order.status }}
                        </span>
                      </td>
                      <td>{{ order.time }}</td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-outline-primary" @click="viewOrderDetails(order)">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button class="btn btn-outline-secondary" @click="editOrder(order)">
                            <i class="bi bi-pencil"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Restaurant Management -->
          <div class="card border-0 shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
              <h5 class="mb-0">
                <i class="bi bi-shop me-2"></i>Restaurant Management
              </h5>
              <button class="btn btn-light btn-sm" @click="addNewRestaurant">
                <i class="bi bi-plus me-1"></i>Add New
              </button>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Restaurant</th>
                      <th>Owner</th>
                      <th>Status</th>
                      <th>Orders Today</th>
                      <th>Rating</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="restaurant in restaurants" :key="restaurant.id">
                      <td>
                        <div class="d-flex align-items-center">
                          <img :src="restaurant.logo" :alt="restaurant.name" class="restaurant-logo me-2">
                          <div>
                            <div class="fw-bold">{{ restaurant.name }}</div>
                            <small class="text-muted">{{ restaurant.cuisine }}</small>
                          </div>
                        </div>
                      </td>
                      <td>{{ restaurant.owner }}</td>
                      <td>
                        <span class="badge" :class="getRestaurantStatusClass(restaurant.status)">
                          {{ restaurant.status }}
                        </span>
                      </td>
                      <td>{{ restaurant.ordersToday }}</td>
                      <td>
                        <div class="rating">
                          {{ restaurant.rating }} ⭐
                        </div>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-outline-primary" @click="viewRestaurant(restaurant)">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button class="btn btn-outline-warning" @click="editRestaurant(restaurant)">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-outline-danger" @click="toggleRestaurantStatus(restaurant)">
                            <i class="bi bi-power"></i>
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

        <!-- Sidebar -->
        <div class="col-lg-4">
          <!-- Quick Stats -->
          <div class="card border-0 shadow mb-4">
            <div class="card-header bg-warning text-dark">
              <h6 class="mb-0">
                <i class="bi bi-speedometer2 me-2"></i>Quick Stats
              </h6>
            </div>
            <div class="card-body">
              <div class="quick-stat-item">
                <div class="d-flex justify-content-between mb-2">
                  <span>Pending Orders</span>
                  <span class="badge bg-warning">{{ quickStats.pendingOrders }}</span>
                </div>
                <div class="progress mb-3" style="height: 6px;">
                  <div class="progress-bar bg-warning" style="width: 75%"></div>
                </div>
              </div>
              <div class="quick-stat-item">
                <div class="d-flex justify-content-between mb-2">
                  <span>Active Deliveries</span>
                  <span class="badge bg-info">{{ quickStats.activeDeliveries }}</span>
                </div>
                <div class="progress mb-3" style="height: 6px;">
                  <div class="progress-bar bg-info" style="width: 60%"></div>
                </div>
              </div>
              <div class="quick-stat-item">
                <div class="d-flex justify-content-between mb-2">
                  <span>Customer Satisfaction</span>
                  <span class="badge bg-success">{{ quickStats.customerSatisfaction }}%</span>
                </div>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-success" :style="{ width: quickStats.customerSatisfaction + '%' }"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Activities -->
          <div class="card border-0 shadow mb-4">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0">
                <i class="bi bi-activity me-2"></i>Recent Activities
              </h6>
            </div>
            <div class="card-body">
              <div class="activity-list">
                <div v-for="activity in recentActivities" :key="activity.id" class="activity-item mb-3">
                  <div class="d-flex">
                    <div class="activity-icon me-3">
                      <i :class="activity.icon" :style="{ color: activity.color }"></i>
                    </div>
                    <div class="activity-content">
                      <p class="activity-text mb-1">{{ activity.text }}</p>
                      <small class="activity-time text-muted">{{ activity.time }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- System Health -->
          <div class="card border-0 shadow">
            <div class="card-header bg-dark text-white">
              <h6 class="mb-0">
                <i class="bi bi-cpu me-2"></i>System Health
              </h6>
            </div>
            <div class="card-body">
              <div class="system-metric mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <small>Server Load</small>
                  <small>{{ systemHealth.serverLoad }}%</small>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-success" :style="{ width: systemHealth.serverLoad + '%' }"></div>
                </div>
              </div>
              <div class="system-metric mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <small>Database</small>
                  <small>{{ systemHealth.database }}%</small>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-info" :style="{ width: systemHealth.database + '%' }"></div>
                </div>
              </div>
              <div class="system-metric">
                <div class="d-flex justify-content-between mb-1">
                  <small>API Response</small>
                  <small>{{ systemHealth.apiResponse }}ms</small>
                </div>
                <div class="progress" style="height: 8px;">
                  <div class="progress-bar bg-warning" style="width: 25%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Dashboard data
const orderFilter = ref('all')

const overviewStats = ref({
  totalOrders: 1247,
  totalRevenue: 45680,
  activeRestaurants: 156,
  activeAgents: 89
})

const quickStats = ref({
  pendingOrders: 23,
  activeDeliveries: 45,
  customerSatisfaction: 94
})

const systemHealth = ref({
  serverLoad: 68,
  database: 82,
  apiResponse: 145
})

const orders = ref([
  {
    id: 'FD001234',
    customerName: 'John Doe',
    restaurant: 'Bella Italia',
    amount: 35.99,
    status: 'delivered',
    time: '2:30 PM'
  },
  {
    id: 'FD001235',
    customerName: 'Sarah Johnson',
    restaurant: 'Burger Palace',
    amount: 28.50,
    status: 'confirmed',
    time: '2:45 PM'
  },
  {
    id: 'FD001236',
    customerName: 'Mike Chen',
    restaurant: 'Sakura Sushi',
    amount: 52.75,
    status: 'pending',
    time: '3:10 PM'
  },
  {
    id: 'FD001237',
    customerName: 'Lisa Wang',
    restaurant: 'El Mariachi',
    amount: 19.99,
    status: 'delivered',
    time: '3:25 PM'
  }
])

const restaurants = ref([
  {
    id: 1,
    name: 'Bella Italia',
    owner: 'Marco Rossi',
    cuisine: 'Italian',
    status: 'active',
    ordersToday: 45,
    rating: 4.8,
    logo: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=50&q=80'
  },
  {
    id: 2,
    name: 'Burger Palace',
    owner: 'John Smith',
    cuisine: 'American',
    status: 'active',
    ordersToday: 67,
    rating: 4.6,
    logo: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-4.0.3&auto=format&fit=crop&w=50&q=80'
  },
  {
    id: 3,
    name: 'Sakura Sushi',
    owner: 'Takeshi Yamamoto',
    cuisine: 'Japanese',
    status: 'pending',
    ordersToday: 23,
    rating: 4.9,
    logo: 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?ixlib=rb-4.0.3&auto=format&fit=crop&w=50&q=80'
  }
])

const recentActivities = ref([
  {
    id: 1,
    text: 'New restaurant "Taco Bell" registered',
    time: '5 minutes ago',
    icon: 'bi bi-shop',
    color: '#28a745'
  },
  {
    id: 2,
    text: 'Order #FD001234 was delivered successfully',
    time: '12 minutes ago',
    icon: 'bi bi-check-circle',
    color: '#007bff'
  },
  {
    id: 3,
    text: 'Agent Alex Rodriguez went online',
    time: '25 minutes ago',
    icon: 'bi bi-person-check',
    color: '#ffc107'
  },
  {
    id: 4,
    text: 'System backup completed',
    time: '1 hour ago',
    icon: 'bi bi-shield-check',
    color: '#6c757d'
  }
])

// Computed properties
const filteredOrders = computed(() => {
  if (orderFilter.value === 'all') {
    return orders.value
  }
  return orders.value.filter(order => order.status === orderFilter.value)
})

// Methods
const getOrderStatusClass = (status) => {
  const statusClasses = {
    'pending': 'bg-warning text-dark',
    'confirmed': 'bg-info text-white',
    'delivered': 'bg-success text-white',
    'cancelled': 'bg-danger text-white'
  }
  return statusClasses[status] || 'bg-secondary'
}

const getRestaurantStatusClass = (status) => {
  const statusClasses = {
    'active': 'bg-success text-white',
    'pending': 'bg-warning text-dark',
    'suspended': 'bg-danger text-white'
  }
  return statusClasses[status] || 'bg-secondary'
}

const viewOrderDetails = (order) => {
  console.log('Viewing order details:', order.id)
}

const editOrder = (order) => {
  console.log('Editing order:', order.id)
}

const addNewRestaurant = () => {
  console.log('Adding new restaurant')
}

const viewRestaurant = (restaurant) => {
  console.log('Viewing restaurant:', restaurant.name)
}

const editRestaurant = (restaurant) => {
  console.log('Editing restaurant:', restaurant.name)
}

const toggleRestaurantStatus = (restaurant) => {
  const newStatus = restaurant.status === 'active' ? 'suspended' : 'active'
  restaurant.status = newStatus
  console.log('Restaurant status changed:', restaurant.name, newStatus)
}

const viewReports = () => {
  console.log('Viewing reports')
}
</script>

<style scoped>
.navbar-spacer {
  height: 80px;
}

.dashboard-title {
  font-size: 2.2rem;
  font-weight: 700;
  color: #333;
}

.dashboard-subtitle {
  color: #666;
  font-size: 1.1rem;
}

/* Stats Cards */
.stat-card {
  border-radius: 15px;
  padding: 1.5rem;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  opacity: 0.8;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  opacity: 0.9;
}

.stat-change {
  font-size: 0.8rem;
  font-weight: 500;
}

/* Tables */
.table th {
  font-weight: 600;
  border-bottom: 2px solid #dee2e6;
}

.table td {
  vertical-align: middle;
}

.restaurant-logo {
  width: 40px;
  height: 40px;
  object-fit: cover;
  border-radius: 8px;
}

/* Quick Stats */
.quick-stat-item {
  margin-bottom: 1rem;
}

.quick-stat-item:last-child {
  margin-bottom: 0;
}

/* Activities */
.activity-item {
  padding-bottom: 1rem;
  border-bottom: 1px solid #f8f9fa;
}

.activity-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.activity-icon {
  width: 30px;
  text-align: center;
  font-size: 1.2rem;
}

.activity-text {
  font-size: 0.9rem;
  line-height: 1.4;
  margin-bottom: 0.25rem;
}

.activity-time {
  font-size: 0.8rem;
}

/* System Health */
.system-metric .progress {
  border-radius: 10px;
}

.system-metric .progress-bar {
  border-radius: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .dashboard-title {
    font-size: 1.8rem;
  }
  
  .quick-actions {
    text-align: center;
    margin-top: 1rem;
  }
  
  .quick-actions .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .quick-actions .me-2 {
    margin-right: 0 !important;
  }
  
  .stat-card {
    margin-bottom: 1rem;
    padding: 1rem;
  }
  
  .stat-icon {
    font-size: 2rem;
  }
  
  .stat-number {
    font-size: 1.5rem;
  }
  
  .table-responsive {
    font-size: 0.9rem;
  }
  
  .btn-group-sm .btn {
    padding: 0.25rem 0.4rem;
  }
}

@media (max-width: 576px) {
  .container-fluid {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .dashboard-header .row {
    text-align: center;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .restaurant-logo {
    width: 30px;
    height: 30px;
  }
  
  .activity-icon {
    width: 25px;
    font-size: 1rem;
  }
  
  .table {
    font-size: 0.8rem;
  }
  
  .stat-card {
    text-align: center;
  }
}
</style>
