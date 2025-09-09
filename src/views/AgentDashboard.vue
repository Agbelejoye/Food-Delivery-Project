<template>
  <div class="agent-dashboard">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container-fluid py-4">
      <!-- Dashboard Header -->
      <div class="dashboard-header mb-4">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h2 class="dashboard-title">
              <i class="bi bi-speedometer2 me-3"></i>Agent Dashboard
            </h2>
            <p class="dashboard-subtitle">Welcome back, {{ agentName }}!</p>
          </div>
          <div class="col-md-6 text-md-end">
            <div class="availability-toggle">
              <div class="form-check form-switch">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  id="availabilitySwitch"
                  v-model="isAvailable"
                  @change="toggleAvailability"
                >
                <label class="form-check-label fw-bold" for="availabilitySwitch">
                  <span :class="isAvailable ? 'text-success' : 'text-danger'">
                    {{ isAvailable ? 'Available' : 'Offline' }}
                  </span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-section mb-4">
        <div class="row g-3">
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-primary text-white">
              <div class="stat-icon">
                <i class="bi bi-truck"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">{{ todayStats.deliveries }}</h3>
                <p class="stat-label">Today's Deliveries</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-success text-white">
              <div class="stat-icon">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">${{ todayStats.earnings.toFixed(2) }}</h3>
                <p class="stat-label">Today's Earnings</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-warning text-white">
              <div class="stat-icon">
                <i class="bi bi-star-fill"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">{{ agentRating }}</h3>
                <p class="stat-label">Current Rating</p>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <div class="stat-card bg-info text-white">
              <div class="stat-icon">
                <i class="bi bi-clock"></i>
              </div>
              <div class="stat-content">
                <h3 class="stat-number">{{ todayStats.activeHours }}h</h3>
                <p class="stat-label">Active Hours</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- Active Orders -->
        <div class="col-lg-8">
          <div class="card border-0 shadow mb-4">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="bi bi-list-task me-2"></i>Active Orders
                <span class="badge bg-light text-primary ms-2">{{ activeOrders.length }}</span>
              </h5>
            </div>
            <div class="card-body p-0">
              <div v-if="activeOrders.length === 0" class="empty-orders text-center py-5">
                <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                <h5 class="text-muted">No active orders</h5>
                <p class="text-muted">New orders will appear here when available</p>
              </div>
              <div v-else class="orders-list">
                <div 
                  v-for="order in activeOrders" 
                  :key="order.id"
                  class="order-item p-4 border-bottom"
                >
                  <div class="row align-items-center">
                    <div class="col-md-3">
                      <div class="order-info">
                        <h6 class="order-id mb-1">#{{ order.id }}</h6>
                        <span class="badge" :class="getStatusBadgeClass(order.status)">
                          {{ order.status }}
                        </span>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="customer-info">
                        <h6 class="customer-name mb-1">{{ order.customerName }}</h6>
                        <p class="delivery-address text-muted mb-0">
                          <i class="bi bi-geo-alt me-1"></i>{{ order.deliveryAddress }}
                        </p>
                      </div>
                    </div>
                    <div class="col-md-2 text-center">
                      <div class="order-value">
                        <h6 class="text-success mb-0">${{ order.total.toFixed(2) }}</h6>
                        <small class="text-muted">{{ order.items }} items</small>
                      </div>
                    </div>
                    <div class="col-md-3 text-end">
                      <div class="order-actions">
                        <button 
                          v-if="order.status === 'ready_for_pickup'"
                          class="btn btn-success btn-sm me-2"
                          @click="pickupOrder(order.id)"
                        >
                          <i class="bi bi-check-circle me-1"></i>Pickup
                        </button>
                        <button 
                          v-if="order.status === 'picked_up'"
                          class="btn btn-primary btn-sm me-2"
                          @click="deliverOrder(order.id)"
                        >
                          <i class="bi bi-house-check me-1"></i>Deliver
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" @click="viewOrderDetails(order)">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
          <!-- Weekly Earnings -->
          <div class="card border-0 shadow mb-4">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="bi bi-graph-up me-2"></i>Weekly Earnings
              </h6>
            </div>
            <div class="card-body">
              <div class="earnings-chart">
                <div class="weekly-total text-center mb-3">
                  <h3 class="text-success">${{ weeklyEarnings.total.toFixed(2) }}</h3>
                  <small class="text-muted">This Week</small>
                </div>
                <div class="daily-breakdown">
                  <div 
                    v-for="day in weeklyEarnings.daily" 
                    :key="day.day"
                    class="day-earning d-flex justify-content-between mb-2"
                  >
                    <span class="day-name">{{ day.day }}</span>
                    <span class="day-amount">${{ day.amount.toFixed(2) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Reviews -->
          <div class="card border-0 shadow mb-4">
            <div class="card-header bg-warning text-dark">
              <h6 class="mb-0">
                <i class="bi bi-star me-2"></i>Recent Reviews
              </h6>
            </div>
            <div class="card-body">
              <div class="reviews-list">
                <div 
                  v-for="review in recentReviews" 
                  :key="review.id"
                  class="review-item mb-3 pb-3 border-bottom"
                >
                  <div class="review-header d-flex justify-content-between mb-2">
                    <span class="customer-name fw-bold">{{ review.customerName }}</span>
                    <div class="rating">
                      <span v-for="star in 5" :key="star" class="star">
                        <i :class="star <= review.rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
                      </span>
                    </div>
                  </div>
                  <p class="review-text text-muted mb-1">{{ review.comment }}</p>
                  <small class="review-date text-muted">{{ review.date }}</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="card border-0 shadow">
            <div class="card-header bg-info text-white">
              <h6 class="mb-0">
                <i class="bi bi-lightning me-2"></i>Quick Actions
              </h6>
            </div>
            <div class="card-body">
              <div class="d-grid gap-2">
                <button class="btn btn-outline-primary" @click="viewEarningsReport">
                  <i class="bi bi-file-earmark-text me-2"></i>View Earnings Report
                </button>
                <button class="btn btn-outline-success" @click="updateLocation">
                  <i class="bi bi-geo-alt me-2"></i>Update Location
                </button>
                <button class="btn btn-outline-warning" @click="contactSupport">
                  <i class="bi bi-headset me-2"></i>Contact Support
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Order Details - #{{ selectedOrder?.id }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" v-if="selectedOrder">
            <div class="row">
              <div class="col-md-6">
                <h6>Customer Information</h6>
                <p><strong>Name:</strong> {{ selectedOrder.customerName }}</p>
                <p><strong>Phone:</strong> {{ selectedOrder.customerPhone }}</p>
                <p><strong>Address:</strong> {{ selectedOrder.deliveryAddress }}</p>
              </div>
              <div class="col-md-6">
                <h6>Order Items</h6>
                <div v-for="item in selectedOrder.orderItems" :key="item.id" class="mb-2">
                  <div class="d-flex justify-content-between">
                    <span>{{ item.name }} x{{ item.quantity }}</span>
                    <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
                  </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                  <span>Total:</span>
                  <span>${{ selectedOrder.total.toFixed(2) }}</span>
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
import { ref, onMounted } from 'vue'

// Agent data
const agentName = ref('Alex Rodriguez')
const isAvailable = ref(true)
const agentRating = ref(4.9)

const todayStats = ref({
  deliveries: 12,
  earnings: 156.75,
  activeHours: 6.5
})

const activeOrders = ref([
  {
    id: 'FD001234',
    customerName: 'John Doe',
    customerPhone: '+1 (555) 123-4567',
    deliveryAddress: '123 Main St, Apt 4B',
    status: 'ready_for_pickup',
    total: 35.99,
    items: 3,
    orderItems: [
      { id: 1, name: 'Margherita Pizza', quantity: 1, price: 12.99 },
      { id: 2, name: 'Caesar Salad', quantity: 1, price: 8.99 },
      { id: 3, name: 'Garlic Bread', quantity: 2, price: 4.50 }
    ]
  },
  {
    id: 'FD001235',
    customerName: 'Sarah Johnson',
    customerPhone: '+1 (555) 987-6543',
    deliveryAddress: '456 Oak Avenue',
    status: 'picked_up',
    total: 28.50,
    items: 2,
    orderItems: [
      { id: 1, name: 'Chicken Burger', quantity: 1, price: 11.99 },
      { id: 2, name: 'French Fries', quantity: 1, price: 5.99 }
    ]
  }
])

const weeklyEarnings = ref({
  total: 892.45,
  daily: [
    { day: 'Mon', amount: 125.30 },
    { day: 'Tue', amount: 98.75 },
    { day: 'Wed', amount: 156.20 },
    { day: 'Thu', amount: 142.85 },
    { day: 'Fri', amount: 189.60 },
    { day: 'Sat', amount: 179.75 },
    { day: 'Sun', amount: 0.00 }
  ]
})

const recentReviews = ref([
  {
    id: 1,
    customerName: 'Mike Chen',
    rating: 5,
    comment: 'Super fast delivery! Food arrived hot and fresh.',
    date: '2 hours ago'
  },
  {
    id: 2,
    customerName: 'Lisa Wang',
    rating: 4,
    comment: 'Good service, very polite delivery agent.',
    date: '1 day ago'
  },
  {
    id: 3,
    customerName: 'David Smith',
    rating: 5,
    comment: 'Always on time and professional. Highly recommend!',
    date: '2 days ago'
  }
])

const selectedOrder = ref(null)

// Methods
const toggleAvailability = () => {
  console.log('Availability toggled:', isAvailable.value)
}

const getStatusBadgeClass = (status) => {
  const statusClasses = {
    'ready_for_pickup': 'bg-warning text-dark',
    'picked_up': 'bg-info text-white',
    'delivered': 'bg-success text-white'
  }
  return statusClasses[status] || 'bg-secondary'
}

const pickupOrder = (orderId) => {
  const order = activeOrders.value.find(o => o.id === orderId)
  if (order) {
    order.status = 'picked_up'
    console.log('Order picked up:', orderId)
  }
}

const deliverOrder = (orderId) => {
  const order = activeOrders.value.find(o => o.id === orderId)
  if (order) {
    order.status = 'delivered'
    // Remove from active orders after a delay
    setTimeout(() => {
      const index = activeOrders.value.findIndex(o => o.id === orderId)
      if (index > -1) {
        activeOrders.value.splice(index, 1)
        todayStats.value.deliveries++
        todayStats.value.earnings += order.total * 0.15 // 15% commission
      }
    }, 2000)
    console.log('Order delivered:', orderId)
  }
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
  const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'))
  modal.show()
}

const viewEarningsReport = () => {
  console.log('Viewing earnings report')
}

const updateLocation = () => {
  console.log('Updating location')
}

const contactSupport = () => {
  console.log('Contacting support')
}

onMounted(() => {
  // Initialize any needed components
})
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

.availability-toggle .form-check-input {
  width: 3rem;
  height: 1.5rem;
}

.availability-toggle .form-check-label {
  font-size: 1.1rem;
  margin-left: 0.5rem;
}

/* Stats Cards */
.stat-card {
  border-radius: 15px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-icon {
  font-size: 2.5rem;
  margin-right: 1rem;
  opacity: 0.8;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.9rem;
  margin-bottom: 0;
  opacity: 0.9;
}

/* Orders */
.order-item {
  transition: background-color 0.3s ease;
}

.order-item:hover {
  background-color: #f8f9fa;
}

.order-id {
  font-weight: 600;
  color: #333;
}

.customer-name {
  font-weight: 600;
  color: #333;
}

.delivery-address {
  font-size: 0.9rem;
}

.order-value h6 {
  font-size: 1.1rem;
}

.order-actions .btn {
  margin-bottom: 0.25rem;
}

/* Earnings Chart */
.weekly-total h3 {
  font-size: 2.5rem;
  font-weight: 700;
}

.day-earning {
  padding: 0.5rem 0;
  border-bottom: 1px solid #f8f9fa;
}

.day-earning:last-child {
  border-bottom: none;
}

.day-name {
  font-weight: 500;
}

.day-amount {
  font-weight: 600;
  color: #28a745;
}

/* Reviews */
.review-item:last-child {
  border-bottom: none !important;
  margin-bottom: 0 !important;
  padding-bottom: 0 !important;
}

.star {
  font-size: 0.8rem;
}

.review-text {
  font-size: 0.9rem;
  line-height: 1.4;
}

/* Empty State */
.empty-orders {
  color: #6c757d;
}

/* Responsive Design */
@media (max-width: 768px) {
  .dashboard-title {
    font-size: 1.8rem;
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
  
  .order-item .row > div {
    margin-bottom: 1rem;
  }
  
  .order-actions {
    text-align: center;
  }
  
  .order-actions .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
}

@media (max-width: 576px) {
  .container-fluid {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .dashboard-header .col-md-6 {
    text-align: center;
    margin-bottom: 1rem;
  }
  
  .availability-toggle {
    text-align: center;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .order-item {
    padding: 1.5rem;
  }
}
</style>
