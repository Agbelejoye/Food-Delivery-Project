<template>
  <div class="checkout-view">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container py-5">
      <div class="row">
        <div class="col-lg-8">
          <!-- Checkout Header -->
          <div class="checkout-header mb-4">
            <h2 class="checkout-title">
              <i class="bi bi-credit-card me-3"></i>Checkout
            </h2>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><router-link to="/cart">Cart</router-link></li>
                <li class="breadcrumb-item active">Checkout</li>
              </ol>
            </nav>
          </div>

          <!-- Delivery Information -->
          <div class="checkout-section mb-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="bi bi-geo-alt me-2"></i>Delivery Information
                </h5>
              </div>
              <div class="card-body p-4">
                <form @submit.prevent="saveDeliveryInfo">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Full Name *</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="deliveryInfo.fullName"
                        required
                      >
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Phone Number *</label>
                      <input 
                        type="tel" 
                        class="form-control" 
                        v-model="deliveryInfo.phone"
                        required
                      >
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Street Address *</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      v-model="deliveryInfo.address"
                      placeholder="123 Main Street"
                      required
                    >
                  </div>
                  <div class="row">
                    <div class="col-md-4 mb-3">
                      <label class="form-label">City *</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="deliveryInfo.city"
                        required
                      >
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label">State *</label>
                      <select class="form-select" v-model="deliveryInfo.state" required>
                        <option value="">Select State</option>
                        <option value="CA">California</option>
                        <option value="NY">New York</option>
                        <option value="TX">Texas</option>
                        <option value="FL">Florida</option>
                      </select>
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label">ZIP Code *</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="deliveryInfo.zipCode"
                        required
                      >
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Delivery Instructions (Optional)</label>
                    <textarea 
                      class="form-control" 
                      rows="3" 
                      v-model="deliveryInfo.instructions"
                      placeholder="e.g., Leave at door, Ring doorbell, etc."
                    ></textarea>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="checkout-section mb-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                  <i class="bi bi-credit-card-2-front me-2"></i>Payment Method
                </h5>
              </div>
              <div class="card-body p-4">
                <!-- Payment Options -->
                <div class="payment-options mb-4">
                  <div class="form-check mb-3">
                    <input 
                      class="form-check-input" 
                      type="radio" 
                      name="paymentMethod" 
                      id="creditCard"
                      value="credit"
                      v-model="paymentMethod"
                    >
                    <label class="form-check-label" for="creditCard">
                      <i class="bi bi-credit-card me-2"></i>Credit/Debit Card
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input 
                      class="form-check-input" 
                      type="radio" 
                      name="paymentMethod" 
                      id="paypal"
                      value="paypal"
                      v-model="paymentMethod"
                    >
                    <label class="form-check-label" for="paypal">
                      <i class="bi bi-paypal me-2"></i>PayPal
                    </label>
                  </div>
                  <div class="form-check mb-3">
                    <input 
                      class="form-check-input" 
                      type="radio" 
                      name="paymentMethod" 
                      id="cash"
                      value="cash"
                      v-model="paymentMethod"
                    >
                    <label class="form-check-label" for="cash">
                      <i class="bi bi-cash me-2"></i>Cash on Delivery
                    </label>
                  </div>
                </div>

                <!-- Credit Card Form -->
                <div v-if="paymentMethod === 'credit'" class="credit-card-form">
                  <div class="row">
                    <div class="col-md-8 mb-3">
                      <label class="form-label">Card Number *</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="cardInfo.number"
                        placeholder="1234 5678 9012 3456"
                        maxlength="19"
                        @input="formatCardNumber"
                      >
                    </div>
                    <div class="col-md-4 mb-3">
                      <label class="form-label">CVV *</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="cardInfo.cvv"
                        placeholder="123"
                        maxlength="4"
                      >
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Expiry Month *</label>
                      <select class="form-select" v-model="cardInfo.expiryMonth">
                        <option value="">Month</option>
                        <option v-for="month in 12" :key="month" :value="month.toString().padStart(2, '0')">
                          {{ month.toString().padStart(2, '0') }}
                        </option>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label">Expiry Year *</label>
                      <select class="form-select" v-model="cardInfo.expiryYear">
                        <option value="">Year</option>
                        <option v-for="year in 10" :key="year" :value="(new Date().getFullYear() + year - 1).toString()">
                          {{ new Date().getFullYear() + year - 1 }}
                        </option>
                      </select>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Cardholder Name *</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      v-model="cardInfo.holderName"
                      placeholder="John Doe"
                    >
                  </div>
                </div>

                <!-- PayPal Info -->
                <div v-if="paymentMethod === 'paypal'" class="paypal-info">
                  <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    You will be redirected to PayPal to complete your payment.
                  </div>
                </div>

                <!-- Cash on Delivery Info -->
                <div v-if="paymentMethod === 'cash'" class="cash-info">
                  <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Please have the exact amount ready. Our delivery agent will collect ${{ total.toFixed(2) }} upon delivery.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Special Instructions -->
          <div class="checkout-section mb-4">
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                  <i class="bi bi-chat-dots me-2"></i>Special Instructions
                </h5>
              </div>
              <div class="card-body p-4">
                <textarea 
                  class="form-control" 
                  rows="4" 
                  v-model="specialInstructions"
                  placeholder="Any special requests for your order? (e.g., extra spicy, no onions, etc.)"
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
          <div class="order-summary sticky-top">
            <div class="card border-0 shadow">
              <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                  <i class="bi bi-receipt me-2"></i>Order Summary
                </h5>
              </div>
              <div class="card-body">
                <!-- Order Items -->
                <div class="order-items mb-3">
                  <div 
                    v-for="item in orderItems" 
                    :key="item.id"
                    class="order-item d-flex justify-content-between align-items-center mb-2"
                  >
                    <div class="item-info">
                      <div class="item-name">{{ item.name }}</div>
                      <div class="item-quantity text-muted">Qty: {{ item.quantity }}</div>
                    </div>
                    <div class="item-price">${{ (item.price * item.quantity).toFixed(2) }}</div>
                  </div>
                </div>

                <hr>

                <!-- Cost Breakdown -->
                <div class="cost-breakdown">
                  <div class="cost-line">
                    <span>Subtotal</span>
                    <span>${{ subtotal.toFixed(2) }}</span>
                  </div>
                  <div class="cost-line">
                    <span>Delivery Fee</span>
                    <span>${{ deliveryFee.toFixed(2) }}</span>
                  </div>
                  <div class="cost-line">
                    <span>Service Fee</span>
                    <span>${{ serviceFee.toFixed(2) }}</span>
                  </div>
                  <div class="cost-line">
                    <span>Tax</span>
                    <span>${{ tax.toFixed(2) }}</span>
                  </div>
                  <div v-if="discount > 0" class="cost-line text-success">
                    <span>Discount</span>
                    <span>-${{ discount.toFixed(2) }}</span>
                  </div>
                </div>

                <hr>

                <div class="total-line">
                  <span class="fw-bold fs-5">Total</span>
                  <span class="fw-bold fs-5">${{ total.toFixed(2) }}</span>
                </div>

                <!-- Estimated Delivery Time -->
                <div class="delivery-estimate mt-3">
                  <div class="text-center p-3 bg-light rounded">
                    <i class="bi bi-clock text-primary me-2"></i>
                    <strong>Estimated Delivery: 25-35 mins</strong>
                  </div>
                </div>

                <!-- Place Order Button -->
                <button 
                  class="btn btn-success btn-lg w-100 mt-4"
                  @click="placeOrder"
                  :disabled="!isFormValid || isProcessing"
                >
                  <span v-if="isProcessing">
                    <span class="spinner-border spinner-border-sm me-2"></span>
                    Processing...
                  </span>
                  <span v-else>
                    <i class="bi bi-check-circle me-2"></i>
                    Place Order
                  </span>
                </button>
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
import { useRouter } from 'vue-router'

const router = useRouter()

// Form data
const deliveryInfo = ref({
  fullName: '',
  phone: '',
  address: '',
  city: '',
  state: '',
  zipCode: '',
  instructions: ''
})

const paymentMethod = ref('credit')
const cardInfo = ref({
  number: '',
  cvv: '',
  expiryMonth: '',
  expiryYear: '',
  holderName: ''
})

const specialInstructions = ref('')
const isProcessing = ref(false)

// Sample order data
const orderItems = ref([
  {
    id: 1,
    name: 'Margherita Pizza',
    price: 12.99,
    quantity: 2
  },
  {
    id: 2,
    name: 'Classic Burger',
    price: 9.99,
    quantity: 1
  }
])

const discount = ref(0)

// Computed properties
const subtotal = computed(() => {
  return orderItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const deliveryFee = computed(() => 2.99)
const serviceFee = computed(() => subtotal.value * 0.05)
const tax = computed(() => subtotal.value * 0.08)

const total = computed(() => {
  return subtotal.value + deliveryFee.value + serviceFee.value + tax.value - discount.value
})

const isFormValid = computed(() => {
  const deliveryValid = deliveryInfo.value.fullName && 
                       deliveryInfo.value.phone && 
                       deliveryInfo.value.address && 
                       deliveryInfo.value.city && 
                       deliveryInfo.value.state && 
                       deliveryInfo.value.zipCode

  if (paymentMethod.value === 'credit') {
    const cardValid = cardInfo.value.number && 
                     cardInfo.value.cvv && 
                     cardInfo.value.expiryMonth && 
                     cardInfo.value.expiryYear && 
                     cardInfo.value.holderName
    return deliveryValid && cardValid
  }

  return deliveryValid
})

// Methods
const formatCardNumber = (event) => {
  let value = event.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '')
  let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value
  cardInfo.value.number = formattedValue
}

const saveDeliveryInfo = () => {
  console.log('Delivery info saved:', deliveryInfo.value)
}

const placeOrder = async () => {
  if (!isFormValid.value) return

  isProcessing.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Redirect to order confirmation
    router.push('/order-confirmation')
  } catch (error) {
    console.error('Order placement failed:', error)
  } finally {
    isProcessing.value = false
  }
}
</script>

<style scoped>
.navbar-spacer {
  height: 80px;
}

.checkout-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
}

.checkout-section {
  margin-bottom: 2rem;
}

.card-header {
  font-weight: 600;
}

.payment-options .form-check-label {
  font-weight: 500;
  cursor: pointer;
}

.form-check-input:checked {
  background-color: #007bff;
  border-color: #007bff;
}

.order-summary {
  top: 100px;
}

.order-item {
  padding: 0.5rem 0;
}

.item-name {
  font-weight: 500;
  font-size: 0.9rem;
}

.item-quantity {
  font-size: 0.8rem;
}

.item-price {
  font-weight: 600;
  color: #007bff;
}

.cost-line {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.total-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 2px solid #dee2e6;
}

.delivery-estimate {
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
  border-radius: 10px;
}

.credit-card-form .form-control,
.credit-card-form .form-select {
  border-radius: 8px;
}

.alert {
  border-radius: 10px;
  border: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .checkout-title {
    font-size: 2rem;
  }
  
  .order-summary {
    position: static;
    margin-top: 2rem;
  }
  
  .card-body {
    padding: 1.5rem;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .checkout-title {
    font-size: 1.8rem;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .btn-lg {
    padding: 0.75rem 1rem;
    font-size: 1rem;
  }
}
</style>
