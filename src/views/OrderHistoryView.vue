<template>
  <div class="order-history-view">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container py-5">
      <!-- Header -->
      <div class="order-history-header mb-5">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="page-title">
              <i class="bi bi-clock-history me-3"></i>Order History
            </h2>
            <p class="page-subtitle">Track your past orders and reorder your favorites</p>
          </div>
          <div class="col-md-4 text-md-end">
            <div class="filter-dropdown">
              <select class="form-select" v-model="statusFilter">
                <option value="all">All Orders</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
                <option value="pending">Pending</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders List -->
      <div class="orders-container">
        <div v-if="filteredOrders.length === 0" class="empty-orders text-center py-5">
          <div class="empty-icon mb-4">
            <i class="bi bi-inbox display-1 text-muted"></i>
          </div>
          <h4 class="empty-title">No orders found</h4>
          <p class="empty-text text-muted">You haven't placed any orders yet. Start exploring our menu!</p>
          <router-link to="/menu" class="btn btn-primary btn-lg">
            <i class="bi bi-card-list me-2"></i>Browse Menu
          </router-link>
        </div>

        <div v-else class="orders-list">
          <div 
            v-for="order in paginatedOrders" 
            :key="order.id"
            class="order-card mb-4"
          >
            <div class="card border-0 shadow-sm">
              <div class="card-body p-4">
                <!-- Order Header -->
                <div class="order-header mb-3">
                  <div class="row align-items-center">
                    <div class="col-md-6">
                      <div class="order-info">
                        <h5 class="order-id mb-1">#{{ order.id }}</h5>
                        <div class="order-meta">
                          <span class="order-date text-muted">{{ formatDate(order.date) }}</span>
                          <span class="order-restaurant ms-3">
                            <i class="bi bi-shop me-1"></i>{{ order.restaurant }}
                          </span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                      <div class="order-status-price">
                        <span class="badge" :class="getStatusBadgeClass(order.status)">
                          {{ order.status }}
                        </span>
                        <div class="order-total mt-1">
                          <strong>${{ order.total.toFixed(2) }}</strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Order Items -->
                <div class="order-items mb-3">
                  <div class="items-summary">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="items-list">
                          <div 
                            v-for="(item, index) in order.items.slice(0, 3)" 
                            :key="index"
                            class="item-summary"
                          >
                            <span class="item-name">{{ item.name }}</span>
                            <span class="item-quantity text-muted"> x{{ item.quantity }}</span>
                          </div>
                          <div v-if="order.items.length > 3" class="more-items text-muted">
                            +{{ order.items.length - 3 }} more items
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4 text-md-end">
                        <div class="delivery-info">
                          <small class="text-muted">
                            <i class="bi bi-geo-alt me-1"></i>{{ order.deliveryAddress }}
                          </small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Order Actions -->
                <div class="order-actions">
                  <div class="row align-items-center">
                    <div class="col-md-6">
                      <div class="action-buttons">
                        <button 
                          class="btn btn-outline-primary btn-sm me-2"
                          @click="viewOrderDetails(order)"
                        >
                          <i class="bi bi-eye me-1"></i>View Details
                        </button>
                        <button 
                          v-if="order.status === 'delivered'"
                          class="btn btn-success btn-sm me-2"
                          @click="reorder(order)"
                        >
                          <i class="bi bi-arrow-repeat me-1"></i>Reorder
                        </button>
                        <button 
                          v-if="order.status === 'delivered'"
                          class="btn btn-outline-warning btn-sm"
                          @click="rateOrder(order)"
                        >
                          <i class="bi bi-star me-1"></i>Rate
                        </button>
                      </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                      <div class="order-progress" v-if="order.status !== 'delivered' && order.status !== 'cancelled'">
                        <small class="text-muted">
                          <i class="bi bi-clock me-1"></i>Estimated delivery: {{ order.estimatedDelivery }}
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-section mt-4" v-if="totalPages > 1">
          <nav>
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <button class="page-link" @click="changePage(currentPage - 1)">
                  <i class="bi bi-chevron-left"></i>
                </button>
              </li>
              <li 
                v-for="page in visiblePages" 
                :key="page"
                class="page-item" 
                :class="{ active: page === currentPage }"
              >
                <button class="page-link" @click="changePage(page)">{{ page }}</button>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <button class="page-link" @click="changePage(currentPage + 1)">
                  <i class="bi bi-chevron-right"></i>
                </button>
              </li>
            </ul>
          </nav>
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
                <h6 class="section-title">Order Information</h6>
                <div class="order-details-info">
                  <p><strong>Order ID:</strong> #{{ selectedOrder.id }}</p>
                  <p><strong>Date:</strong> {{ formatDate(selectedOrder.date) }}</p>
                  <p><strong>Restaurant:</strong> {{ selectedOrder.restaurant }}</p>
                  <p><strong>Status:</strong> 
                    <span class="badge" :class="getStatusBadgeClass(selectedOrder.status)">
                      {{ selectedOrder.status }}
                    </span>
                  </p>
                  <p><strong>Delivery Address:</strong> {{ selectedOrder.deliveryAddress }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <h6 class="section-title">Items Ordered</h6>
                <div class="ordered-items">
                  <div v-for="item in selectedOrder.items" :key="item.id" class="item-row mb-2">
                    <div class="d-flex justify-content-between">
                      <span>{{ item.name }} x{{ item.quantity }}</span>
                      <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
                    </div>
                  </div>
                  <hr>
                  <div class="order-summary">
                    <div class="d-flex justify-content-between mb-1">
                      <span>Subtotal:</span>
                      <span>${{ selectedOrder.subtotal.toFixed(2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                      <span>Delivery Fee:</span>
                      <span>${{ selectedOrder.deliveryFee.toFixed(2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                      <span>Tax:</span>
                      <span>${{ selectedOrder.tax.toFixed(2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                      <span>Total:</span>
                      <span>${{ selectedOrder.total.toFixed(2) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" @click="reorder(selectedOrder)">
              <i class="bi bi-arrow-repeat me-1"></i>Reorder
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Data
const statusFilter = ref('all')
const currentPage = ref(1)
const ordersPerPage = 5
const selectedOrder = ref(null)

const orders = ref([
  {
    id: 'FD001234',
    date: new Date('2024-01-15'),
    restaurant: 'Bella Italia',
    status: 'delivered',
    total: 35.99,
    subtotal: 28.99,
    deliveryFee: 2.99,
    tax: 4.01,
    deliveryAddress: '123 Main St, Apt 4B',
    estimatedDelivery: '25-35 mins',
    items: [
      { id: 1, name: 'Margherita Pizza', quantity: 1, price: 12.99 },
      { id: 2, name: 'Caesar Salad', quantity: 1, price: 8.99 },
      { id: 3, name: 'Garlic Bread', quantity: 2, price: 4.00 }
    ]
  },
  {
    id: 'FD001235',
    date: new Date('2024-01-12'),
    restaurant: 'Burger Palace',
    status: 'delivered',
    total: 28.50,
    subtotal: 22.98,
    deliveryFee: 1.99,
    tax: 3.53,
    deliveryAddress: '123 Main St, Apt 4B',
    estimatedDelivery: '20-30 mins',
    items: [
      { id: 1, name: 'Classic Burger', quantity: 1, price: 11.99 },
      { id: 2, name: 'French Fries', quantity: 1, price: 5.99 },
      { id: 3, name: 'Coke', quantity: 1, price: 2.99 }
    ]
  },
  {
    id: 'FD001236',
    date: new Date('2024-01-10'),
    restaurant: 'Sakura Sushi',
    status: 'cancelled',
    total: 52.75,
    subtotal: 45.97,
    deliveryFee: 3.99,
    tax: 2.79,
    deliveryAddress: '123 Main St, Apt 4B',
    estimatedDelivery: '30-40 mins',
    items: [
      { id: 1, name: 'Salmon Roll', quantity: 2, price: 15.99 },
      { id: 2, name: 'Tuna Sashimi', quantity: 1, price: 13.99 }
    ]
  },
  {
    id: 'FD001237',
    date: new Date('2024-01-08'),
    restaurant: 'El Mariachi',
    status: 'delivered',
    total: 19.99,
    subtotal: 16.99,
    deliveryFee: 1.99,
    tax: 1.01,
    deliveryAddress: '123 Main St, Apt 4B',
    estimatedDelivery: '25-35 mins',
    items: [
      { id: 1, name: 'Chicken Tacos', quantity: 3, price: 5.66 }
    ]
  }
])

// Computed properties
const filteredOrders = computed(() => {
  if (statusFilter.value === 'all') {
    return orders.value
  }
  return orders.value.filter(order => order.status === statusFilter.value)
})

const totalPages = computed(() => Math.ceil(filteredOrders.value.length / ordersPerPage))

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * ordersPerPage
  const end = start + ordersPerPage
  return filteredOrders.value.slice(start, end)
})

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, start + 4)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// Methods
const formatDate = (date) => {
  return new Intl.DateTimeFormat('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const getStatusBadgeClass = (status) => {
  const statusClasses = {
    'delivered': 'bg-success text-white',
    'cancelled': 'bg-danger text-white',
    'pending': 'bg-warning text-dark',
    'preparing': 'bg-info text-white',
    'on_the_way': 'bg-primary text-white'
  }
  return statusClasses[status] || 'bg-secondary'
}

const viewOrderDetails = (order) => {
  selectedOrder.value = order
  const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'))
  modal.show()
}

const reorder = (order) => {
  console.log('Reordering:', order.id)
  // Add items to cart and redirect
  router.push('/cart')
}

const rateOrder = (order) => {
  console.log('Rating order:', order.id)
  // Open rating modal or redirect to rating page
}

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}
</script>

<style scoped>
.navbar-spacer {
  height: 80px;
}

.page-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
}

.page-subtitle {
  color: #666;
  font-size: 1.1rem;
}

.filter-dropdown .form-select {
  width: auto;
  min-width: 150px;
}

.empty-orders {
  background: white;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  padding: 3rem;
}

.empty-title {
  color: #333;
  margin-bottom: 1rem;
}

.empty-text {
  margin-bottom: 2rem;
}

.order-card {
  transition: transform 0.3s ease;
}

.order-card:hover {
  transform: translateY(-2px);
}

.order-id {
  font-weight: 700;
  color: #333;
}

.order-meta {
  font-size: 0.9rem;
}

.order-date {
  font-weight: 500;
}

.order-restaurant {
  font-weight: 500;
  color: #666;
}

.order-total {
  font-size: 1.2rem;
  color: #007bff;
}

.items-summary {
  background-color: #f8f9fa;
  border-radius: 8px;
  padding: 1rem;
}

.item-summary {
  margin-bottom: 0.5rem;
}

.item-name {
  font-weight: 500;
}

.item-quantity {
  font-size: 0.9rem;
}

.more-items {
  font-size: 0.9rem;
  font-style: italic;
}

.delivery-info {
  font-size: 0.9rem;
}

.action-buttons .btn {
  margin-bottom: 0.5rem;
}

.order-progress {
  font-size: 0.9rem;
}

.section-title {
  font-weight: 600;
  color: #333;
  margin-bottom: 1rem;
}

.order-details-info p {
  margin-bottom: 0.5rem;
}

.item-row {
  padding: 0.5rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.item-row:last-child {
  border-bottom: none;
}

.order-summary {
  background-color: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  margin-top: 1rem;
}

.pagination .page-link {
  border-radius: 8px;
  margin: 0 2px;
  border: 1px solid #dee2e6;
}

.pagination .page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-title {
    font-size: 2rem;
  }
  
  .filter-dropdown {
    margin-top: 1rem;
  }
  
  .order-header .row,
  .order-actions .row {
    flex-direction: column;
  }
  
  .order-status-price,
  .order-progress {
    text-align: center;
    margin-top: 1rem;
  }
  
  .action-buttons {
    text-align: center;
    margin-bottom: 1rem;
  }
  
  .action-buttons .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .items-summary .row {
    flex-direction: column;
  }
  
  .delivery-info {
    text-align: center;
    margin-top: 1rem;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .page-title {
    font-size: 1.8rem;
  }
  
  .card-body {
    padding: 1.5rem;
  }
  
  .items-summary {
    padding: 0.75rem;
  }
  
  .action-buttons .btn {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
  }
  
  .pagination {
    font-size: 0.9rem;
  }
}
</style>
