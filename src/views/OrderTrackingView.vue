<template>
  <div class="order-tracking-view">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container py-5">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <!-- Header -->
          <div class="tracking-header text-center mb-5">
            <h2 class="tracking-title">
              <i class="bi bi-truck me-3"></i>Track Your Order
            </h2>
            <p class="tracking-subtitle">Order #{{ orderId }}</p>
          </div>

          <!-- Order Status Timeline -->
          <div class="order-timeline mb-5">
            <div class="timeline-container">
              <div 
                v-for="(step, index) in orderSteps" 
                :key="step.id"
                class="timeline-step"
                :class="{ 
                  'completed': step.completed, 
                  'active': step.active,
                  'pending': !step.completed && !step.active 
                }"
              >
                <div class="timeline-icon">
                  <i :class="step.icon"></i>
                </div>
                <div class="timeline-content">
                  <h5 class="timeline-title">{{ step.title }}</h5>
                  <p class="timeline-description">{{ step.description }}</p>
                  <small v-if="step.time" class="timeline-time">{{ step.time }}</small>
                </div>
                <div v-if="index < orderSteps.length - 1" class="timeline-line"></div>
              </div>
            </div>
          </div>

          <!-- Live Map (Placeholder) -->
          <div class="map-section mb-5" v-if="currentStep >= 2">
            <div class="card border-0 shadow">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="bi bi-geo-alt me-2"></i>Live Tracking
                </h5>
              </div>
              <div class="card-body p-0">
                <div class="map-placeholder">
                  <div class="map-content">
                    <i class="bi bi-geo-alt-fill text-primary"></i>
                    <h4>Your order is on the way!</h4>
                    <p>Estimated arrival: {{ estimatedArrival }}</p>
                    <div class="delivery-progress">
                      <div class="progress mb-3">
                        <div 
                          class="progress-bar bg-success" 
                          :style="{ width: deliveryProgress + '%' }"
                        ></div>
                      </div>
                      <small class="text-muted">{{ deliveryProgress }}% of the way there</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Delivery Agent Info -->
          <div class="agent-info mb-5" v-if="currentStep >= 2">
            <div class="card border-0 shadow">
              <div class="card-body p-4">
                <div class="row align-items-center">
                  <div class="col-md-3 text-center">
                    <img 
                      :src="deliveryAgent.photo" 
                      :alt="deliveryAgent.name"
                      class="agent-photo rounded-circle mb-3"
                    >
                  </div>
                  <div class="col-md-6">
                    <h5 class="agent-name">{{ deliveryAgent.name }}</h5>
                    <div class="agent-rating mb-2">
                      <span class="badge bg-success">{{ deliveryAgent.rating }} ⭐</span>
                      <small class="text-muted ms-2">{{ deliveryAgent.deliveries }} deliveries</small>
                    </div>
                    <p class="agent-vehicle text-muted">
                      <i class="bi bi-bicycle me-1"></i>{{ deliveryAgent.vehicle }}
                    </p>
                  </div>
                  <div class="col-md-3 text-center">
                    <button class="btn btn-outline-primary btn-sm mb-2 w-100" @click="callAgent">
                      <i class="bi bi-telephone me-1"></i>Call
                    </button>
                    <button class="btn btn-outline-secondary btn-sm w-100" @click="messageAgent">
                      <i class="bi bi-chat-dots me-1"></i>Message
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Details -->
          <div class="order-details">
            <div class="card border-0 shadow">
              <div class="card-header bg-light">
                <h5 class="mb-0">
                  <i class="bi bi-receipt me-2"></i>Order Details
                </h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6">
                    <h6 class="text-muted">ITEMS ORDERED</h6>
                    <div class="order-items">
                      <div 
                        v-for="item in orderItems" 
                        :key="item.id"
                        class="order-item d-flex justify-content-between mb-2"
                      >
                        <div>
                          <span class="item-name">{{ item.name }}</span>
                          <span class="item-quantity text-muted"> x{{ item.quantity }}</span>
                        </div>
                        <span class="item-price">${{ (item.price * item.quantity).toFixed(2) }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h6 class="text-muted">DELIVERY ADDRESS</h6>
                    <div class="delivery-address mb-3">
                      <p class="mb-1">{{ deliveryAddress.name }}</p>
                      <p class="mb-1 text-muted">{{ deliveryAddress.street }}</p>
                      <p class="mb-1 text-muted">{{ deliveryAddress.city }}, {{ deliveryAddress.state }} {{ deliveryAddress.zip }}</p>
                      <p class="mb-0 text-muted">{{ deliveryAddress.phone }}</p>
                    </div>
                    
                    <h6 class="text-muted">ORDER SUMMARY</h6>
                    <div class="order-summary">
                      <div class="summary-line">
                        <span>Subtotal</span>
                        <span>${{ orderSummary.subtotal.toFixed(2) }}</span>
                      </div>
                      <div class="summary-line">
                        <span>Delivery Fee</span>
                        <span>${{ orderSummary.deliveryFee.toFixed(2) }}</span>
                      </div>
                      <div class="summary-line">
                        <span>Tax</span>
                        <span>${{ orderSummary.tax.toFixed(2) }}</span>
                      </div>
                      <hr>
                      <div class="summary-line fw-bold">
                        <span>Total</span>
                        <span>${{ orderSummary.total.toFixed(2) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="action-buttons text-center mt-4">
            <button class="btn btn-outline-danger me-3" @click="cancelOrder" v-if="currentStep < 2">
              <i class="bi bi-x-circle me-1"></i>Cancel Order
            </button>
            <button class="btn btn-primary" @click="reorder">
              <i class="bi bi-arrow-repeat me-1"></i>Reorder
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Order data
const orderId = ref('FD-2024-001234')
const currentStep = ref(2) // 0: confirmed, 1: preparing, 2: on the way, 3: delivered
const deliveryProgress = ref(65)
const estimatedArrival = ref('5-10 minutes')

const orderSteps = ref([
  {
    id: 1,
    title: 'Order Confirmed',
    description: 'Your order has been received and confirmed',
    icon: 'bi bi-check-circle-fill',
    completed: true,
    active: false,
    time: '2:30 PM'
  },
  {
    id: 2,
    title: 'Preparing Food',
    description: 'The restaurant is preparing your delicious meal',
    icon: 'bi bi-fire',
    completed: true,
    active: false,
    time: '2:45 PM'
  },
  {
    id: 3,
    title: 'On the Way',
    description: 'Your order is being delivered to you',
    icon: 'bi bi-truck',
    completed: false,
    active: true,
    time: '3:10 PM'
  },
  {
    id: 4,
    title: 'Delivered',
    description: 'Enjoy your meal!',
    icon: 'bi bi-house-check-fill',
    completed: false,
    active: false,
    time: null
  }
])

const deliveryAgent = ref({
  name: 'Alex Rodriguez',
  photo: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
  rating: 4.9,
  deliveries: 1247,
  vehicle: 'Blue Honda Motorcycle',
  phone: '+1 (555) 123-4567'
})

const orderItems = ref([
  {
    id: 1,
    name: 'Margherita Pizza',
    quantity: 2,
    price: 12.99
  },
  {
    id: 2,
    name: 'Classic Burger',
    quantity: 1,
    price: 9.99
  }
])

const deliveryAddress = ref({
  name: 'John Doe',
  street: '123 Main Street, Apt 4B',
  city: 'New York',
  state: 'NY',
  zip: '10001',
  phone: '+1 (555) 987-6543'
})

const orderSummary = ref({
  subtotal: 35.97,
  deliveryFee: 2.99,
  tax: 2.88,
  total: 41.84
})

// Methods
const callAgent = () => {
  window.open(`tel:${deliveryAgent.value.phone}`)
}

const messageAgent = () => {
  // Open messaging interface
  console.log('Opening message interface with agent')
}

const cancelOrder = () => {
  if (confirm('Are you sure you want to cancel this order?')) {
    console.log('Order cancelled')
    router.push('/')
  }
}

const reorder = () => {
  // Add items back to cart and redirect
  console.log('Reordering items')
  router.push('/cart')
}

// Simulate real-time updates
onMounted(() => {
  const interval = setInterval(() => {
    if (deliveryProgress.value < 100 && currentStep.value === 2) {
      deliveryProgress.value += 1
      
      if (deliveryProgress.value >= 100) {
        currentStep.value = 3
        orderSteps.value[2].completed = true
        orderSteps.value[2].active = false
        orderSteps.value[3].completed = true
        orderSteps.value[3].active = true
        orderSteps.value[3].time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        clearInterval(interval)
      }
    }
  }, 2000)
})
</script>

<style scoped>
.navbar-spacer {
  height: 80px;
}

.tracking-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-on-dark);
}

.tracking-subtitle {
  font-size: 1.2rem;
  color: #cfcfc9;
  font-weight: 500;
}

/* Timeline Styles */
.timeline-container {
  position: relative;
  padding-left: 2rem;
}

.timeline-step {
  position: relative;
  padding-bottom: 3rem;
}

.timeline-step:last-child {
  padding-bottom: 0;
}

.timeline-icon {
  position: absolute;
  left: -2rem;
  top: 0;
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  z-index: 2;
}

.timeline-step.completed .timeline-icon {
  background: #1f9d55; /* success on dark */
  color: var(--text-on-dark);
}

.timeline-step.active .timeline-icon {
  background: var(--gold-500);
  color: #111;
  animation: pulse 2s infinite;
}

.timeline-step.pending .timeline-icon {
  background: var(--black-800);
  color: #cfcfc9;
}

.timeline-line {
  position: absolute;
  left: -0.5rem;
  top: 3rem;
  width: 2px;
  height: 100%;
  background: #2a2b2f;
}

.timeline-step.completed .timeline-line {
  background: #1f9d55;
}

.timeline-content {
  margin-left: 1rem;
}

.timeline-title {
  font-weight: 600;
  color: var(--text-on-dark);
  margin-bottom: 0.5rem;
}

.timeline-description {
  color: #cfcfc9;
  margin-bottom: 0.5rem;
}

.timeline-time {
  color: var(--gold-500);
  font-weight: 500;
}

/* Map Placeholder */
.map-placeholder {
  height: 300px;
  background: linear-gradient(135deg, var(--black-900) 0%, var(--black-800) 70%, rgba(212,175,55,0.12) 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.map-content i {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: var(--gold-500);
}

.delivery-progress {
  max-width: 300px;
  margin: 0 auto;
}

/* Agent Info */
.agent-photo {
  width: 80px;
  height: 80px;
  object-fit: cover;
}

.agent-name {
  font-weight: 600;
  color: #333;
  margin-bottom: 0.5rem;
}

.agent-rating .badge {
  font-size: 0.8rem;
}

.agent-vehicle {
  font-size: 0.9rem;
}

/* Order Details */
.order-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #f8f9fa;
}

.order-item:last-child {
  border-bottom: none;
}

.item-name {
  font-weight: 500;
}

.item-quantity {
  font-size: 0.9rem;
}

.item-price {
  font-weight: 600;
  color: #007bff;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.delivery-address p {
  margin-bottom: 0.25rem;
}

/* Animations */
@keyframes pulse {
  0% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .tracking-title {
    font-size: 2rem;
  }
  
  .timeline-container {
    padding-left: 1.5rem;
  }
  
  .timeline-icon {
    left: -1.5rem;
    width: 2.5rem;
    height: 2.5rem;
    font-size: 1rem;
  }
  
  .timeline-line {
    left: -0.25rem;
  }
  
  .agent-photo {
    width: 60px;
    height: 60px;
  }
  
  .map-placeholder {
    height: 250px;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .tracking-title {
    font-size: 1.8rem;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .action-buttons .btn {
    margin-bottom: 0.5rem;
    width: 100%;
  }
  
  .action-buttons .me-3 {
    margin-right: 0 !important;
  }
}
</style>
