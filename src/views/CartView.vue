<template>
  <div class="cart-view">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container py-5">
      <div class="row">
        <div class="col-lg-8">
          <!-- Cart Header -->
          <div class="cart-header mb-4">
            <h2 class="cart-title">
              <i class="bi bi-cart3 me-3"></i>Your Cart
            </h2>
            <p class="cart-subtitle" v-if="cartItems.length > 0">
              {{ cartItems.length }} item{{ cartItems.length !== 1 ? 's' : '' }} in your cart
            </p>
          </div>

          <!-- Empty Cart -->
          <div v-if="cartItems.length === 0" class="empty-cart text-center py-5">
            <div class="empty-cart-icon mb-4">
              <i class="bi bi-cart-x display-1 text-muted"></i>
            </div>
            <h3 class="empty-cart-title">Your cart is empty</h3>
            <p class="empty-cart-text">Add some delicious items to get started!</p>
            <router-link to="/" class="btn btn-primary btn-lg">
              <i class="bi bi-arrow-left me-2"></i>Continue Shopping
            </router-link>
          </div>

          <!-- Cart Items -->
          <div v-else class="cart-items">
            <div 
              v-for="item in cartItems" 
              :key="item.id" 
              class="cart-item mb-3"
            >
              <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                  <div class="row align-items-center">
                    <div class="col-md-2">
                      <img 
                        :src="item.image" 
                        :alt="item.name" 
                        class="cart-item-image img-fluid rounded"
                      >
                    </div>
                    <div class="col-md-4">
                      <h5 class="cart-item-name mb-1">{{ item.name }}</h5>
                      <p class="cart-item-restaurant text-muted mb-2">{{ item.restaurant }}</p>
                      <div class="cart-item-customizations" v-if="item.customizations">
                        <small class="text-muted">
                          {{ item.customizations.join(', ') }}
                        </small>
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="quantity-controls">
                        <button 
                          class="btn btn-outline-secondary btn-sm"
                          @click="decreaseQuantity(item.id)"
                          :disabled="item.quantity <= 1"
                        >
                          <i class="bi bi-dash"></i>
                        </button>
                        <span class="quantity-display mx-3">{{ item.quantity }}</span>
                        <button 
                          class="btn btn-outline-secondary btn-sm"
                          @click="increaseQuantity(item.id)"
                        >
                          <i class="bi bi-plus"></i>
                        </button>
                      </div>
                    </div>
                    <div class="col-md-2 text-center">
                      <div class="cart-item-price">
                        ${{ (item.price * item.quantity).toFixed(2) }}
                      </div>
                    </div>
                    <div class="col-md-2 text-end">
                      <button 
                        class="btn btn-outline-danger btn-sm"
                        @click="removeItem(item.id)"
                      >
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4" v-if="cartItems.length > 0">
          <div class="order-summary sticky-top">
            <div class="card border-0 shadow">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="bi bi-receipt me-2"></i>Order Summary
                </h5>
              </div>
              <div class="card-body">
                <div class="summary-line">
                  <span>Subtotal</span>
                  <span>${{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="summary-line">
                  <span>Delivery Fee</span>
                  <span>${{ deliveryFee.toFixed(2) }}</span>
                </div>
                <div class="summary-line">
                  <span>Service Fee</span>
                  <span>${{ serviceFee.toFixed(2) }}</span>
                </div>
                <div class="summary-line">
                  <span>Tax</span>
                  <span>${{ tax.toFixed(2) }}</span>
                </div>
                <hr>
                <div class="summary-line total-line">
                  <span class="fw-bold">Total</span>
                  <span class="fw-bold">${{ total.toFixed(2) }}</span>
                </div>

                <!-- Promo Code -->
                <div class="promo-section mt-4">
                  <div class="input-group">
                    <input 
                      type="text" 
                      class="form-control" 
                      placeholder="Promo code"
                      v-model="promoCode"
                    >
                    <button 
                      class="btn btn-outline-secondary" 
                      type="button"
                      @click="applyPromoCode"
                    >
                      Apply
                    </button>
                  </div>
                  <div v-if="promoMessage" class="promo-message mt-2">
                    <small :class="promoSuccess ? 'text-success' : 'text-danger'">
                      {{ promoMessage }}
                    </small>
                  </div>
                </div>

                <!-- Checkout Button -->
                <button 
                  class="btn btn-warning btn-lg w-100 mt-4"
                  @click="proceedToCheckout"
                >
                  <i class="bi bi-credit-card me-2"></i>
                  Proceed to Checkout
                </button>
              </div>
            </div>

            <!-- Delivery Info -->
            <div class="card border-0 shadow mt-4">
              <div class="card-body">
                <h6 class="card-title">
                  <i class="bi bi-truck me-2"></i>Delivery Information
                </h6>
                <div class="delivery-info">
                  <div class="delivery-time">
                    <i class="bi bi-clock me-2"></i>
                    Estimated delivery: <strong>25-35 mins</strong>
                  </div>
                  <div class="delivery-address mt-2">
                    <i class="bi bi-geo-alt me-2"></i>
                    Delivering to: <strong>123 Main St, City</strong>
                    <button class="btn btn-link btn-sm p-0 ms-2">Change</button>
                  </div>
                </div>
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
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import Footer from '@/components/Footer.vue'

const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()

const promoCode = ref('')
const promoMessage = ref('')
const promoSuccess = ref(false)
const promoDiscount = ref(0)

// Computed properties
const cartItems = computed(() => cartStore.items || [])

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const deliveryFee = computed(() => 2.99)
const serviceFee = computed(() => subtotal.value * 0.05)
const tax = computed(() => subtotal.value * 0.08)

const total = computed(() => {
  return subtotal.value + deliveryFee.value + serviceFee.value + tax.value - promoDiscount.value
})

// Methods
const increaseQuantity = async (itemId) => {
  const item = cartItems.value.find(item => item.id === itemId)
  if (item) {
    await cartStore.updateQuantity(itemId, item.quantity + 1)
  }
}

const decreaseQuantity = async (itemId) => {
  const item = cartItems.value.find(item => item.id === itemId)
  if (item && item.quantity > 1) {
    await cartStore.updateQuantity(itemId, item.quantity - 1)
  }
}

const removeItem = async (itemId) => {
  await cartStore.removeFromCart(itemId)
}

const applyPromoCode = () => {
  if (promoCode.value.toLowerCase() === 'save10') {
    promoDiscount.value = subtotal.value * 0.1
    promoMessage.value = '10% discount applied!'
    promoSuccess.value = true
  } else if (promoCode.value.toLowerCase() === 'free5') {
    promoDiscount.value = 5
    promoMessage.value = '$5 discount applied!'
    promoSuccess.value = true
  } else {
    promoMessage.value = 'Invalid promo code'
    promoSuccess.value = false
    promoDiscount.value = 0
  }
}

const proceedToCheckout = () => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  router.push('/checkout')
}

onMounted(() => {
  authStore.initializeAuth()
  if (authStore.isAuthenticated) {
    cartStore.fetchCart()
  } else {
    router.push('/login')
  }
})
</script>

<style scoped>
.navbar-spacer {
  height: 80px;
}

.cart-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
}

.cart-subtitle {
  color: #666;
  font-size: 1.1rem;
}

.empty-cart {
  background: white;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  padding: 3rem;
}

.empty-cart-icon {
  font-size: 4rem;
}

.empty-cart-title {
  color: #333;
  margin-bottom: 1rem;
}

.empty-cart-text {
  color: #666;
  margin-bottom: 2rem;
}

.cart-item {
  transition: all 0.3s ease;
}

.cart-item:hover {
  transform: translateY(-2px);
}

.cart-item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
}

.cart-item-name {
  font-weight: 600;
  color: #333;
}

.cart-item-restaurant {
  font-size: 0.9rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  justify-content: center;
}

.quantity-display {
  font-weight: 600;
  font-size: 1.1rem;
}

.cart-item-price {
  font-size: 1.2rem;
  font-weight: 700;
  color: #007bff;
}

.order-summary {
  top: 100px;
}

.summary-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.total-line {
  font-size: 1.2rem;
  border-top: 2px solid #dee2e6;
  padding-top: 1rem;
  margin-top: 1rem;
}

.promo-message {
  font-size: 0.9rem;
}

.delivery-info {
  font-size: 0.9rem;
}

.delivery-time,
.delivery-address {
  color: #666;
}

/* Responsive Design */
@media (max-width: 768px) {
  .cart-title {
    font-size: 2rem;
  }
  
  .cart-item .row > div {
    margin-bottom: 1rem;
  }
  
  .cart-item-image {
    width: 60px;
    height: 60px;
  }
  
  .quantity-controls {
    justify-content: flex-start;
  }
  
  .order-summary {
    position: static;
    margin-top: 2rem;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .cart-item .card-body {
    padding: 1.5rem;
  }
  
  .quantity-controls .btn {
    padding: 0.25rem 0.5rem;
  }
  
  .quantity-display {
    margin: 0 1rem;
  }
}
</style>
