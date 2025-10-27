<template>
  <div class="profile-view">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container py-5">
      <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-4">
          <div class="profile-sidebar">
            <!-- Profile Card -->
            <div class="card border-0 shadow mb-4">
              <div class="card-body text-center p-4">
                <div class="profile-avatar mb-3">
                  <img 
                    :src="userProfile.avatar" 
                    :alt="userProfile.name"
                    class="rounded-circle"
                    width="120"
                    height="120"
                  >
                  <button class="btn btn-sm btn-primary avatar-edit-btn" @click="changeAvatar">
                    <i class="bi bi-camera"></i>
                  </button>
                </div>
                <h4 class="profile-name">{{ userProfile.name }}</h4>
                <p class="profile-email text-muted">{{ userProfile.email }}</p>
                <div class="profile-stats">
                  <div class="row text-center">
                    <div class="col-4">
                      <h6 class="stat-number">{{ userProfile.stats.totalOrders }}</h6>
                      <small class="text-muted">Orders</small>
                    </div>
                    <div class="col-4">
                      <h6 class="stat-number">${{ userProfile.stats.totalSpent }}</h6>
                      <small class="text-muted">Spent</small>
                    </div>
                    <div class="col-4">
                      <h6 class="stat-number">{{ userProfile.stats.memberSince }}</h6>
                      <small class="text-muted">Member Since</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Navigation Menu -->
            <div class="card border-0 shadow">
              <div class="card-body p-0">
                <div class="profile-nav">
                  <a 
                    href="#" 
                    class="nav-item"
                    :class="{ active: activeTab === 'personal' }"
                    @click="setActiveTab('personal')"
                  >
                    <i class="bi bi-person me-2"></i>Personal Info
                  </a>
                  <a 
                    href="#" 
                    class="nav-item"
                    :class="{ active: activeTab === 'addresses' }"
                    @click="setActiveTab('addresses')"
                  >
                    <i class="bi bi-geo-alt me-2"></i>Addresses
                  </a>
                  <a 
                    href="#" 
                    class="nav-item"
                    :class="{ active: activeTab === 'payment' }"
                    @click="setActiveTab('payment')"
                  >
                    <i class="bi bi-credit-card me-2"></i>Payment Methods
                  </a>
                  <a 
                    href="#" 
                    class="nav-item"
                    :class="{ active: activeTab === 'preferences' }"
                    @click="setActiveTab('preferences')"
                  >
                    <i class="bi bi-gear me-2"></i>Preferences
                  </a>
                  <a 
                    href="#" 
                    class="nav-item"
                    :class="{ active: activeTab === 'security' }"
                    @click="setActiveTab('security')"
                  >
                    <i class="bi bi-shield-lock me-2"></i>Security
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Profile Content -->
        <div class="col-lg-8">
          <!-- Personal Information -->
          <div v-if="activeTab === 'personal'" class="profile-content">
            <div class="card border-0 shadow">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                  <i class="bi bi-person me-2"></i>Personal Information
                </h5>
              </div>
              <div class="card-body p-4">
                <form @submit.prevent="updatePersonalInfo">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">First Name</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="personalInfo.firstName"
                      >
                    </div>
                    <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">Last Name</label>
                      <input 
                        type="text" 
                        class="form-control" 
                        v-model="personalInfo.lastName"
                      >
                    </div>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input 
                      type="email" 
                      class="form-control" 
                      v-model="personalInfo.email"
                      disabled
                    >
                    <small class="text-muted">Contact support to change your email</small>
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-bold">Phone Number</label>
                    <input 
                      type="tel" 
                      class="form-control" 
                      v-model="personalInfo.phone"
                    >
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-bold">Date of Birth</label>
                    <input 
                      type="date" 
                      class="form-control" 
                      v-model="personalInfo.dateOfBirth"
                    >
                  </div>
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Information
                  </button>
                </form>
              </div>
            </div>
          </div>

          <!-- Addresses -->
          <div v-if="activeTab === 'addresses'" class="profile-content">
            <div class="card border-0 shadow">
              <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="bi bi-geo-alt me-2"></i>Delivery Addresses
                </h5>
                <button class="btn btn-light btn-sm" @click="addNewAddress">
                  <i class="bi bi-plus me-1"></i>Add Address
                </button>
              </div>
              <div class="card-body p-4">
                <div class="addresses-list">
                  <div 
                    v-for="address in addresses" 
                    :key="address.id"
                    class="address-item mb-3 p-3 border rounded"
                  >
                    <div class="row align-items-center">
                      <div class="col-md-8">
                        <div class="address-info">
                          <h6 class="address-label">
                            {{ address.label }}
                            <span v-if="address.isDefault" class="badge bg-primary ms-2">Default</span>
                          </h6>
                          <p class="address-text mb-1">{{ address.street }}</p>
                          <p class="address-text text-muted mb-0">
                            {{ address.city }}, {{ address.state }} {{ address.zipCode }}
                          </p>
                        </div>
                      </div>
                      <div class="col-md-4 text-md-end">
                        <div class="address-actions">
                          <button class="btn btn-outline-primary btn-sm me-2" @click="editAddress(address)">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-outline-danger btn-sm" @click="deleteAddress(address.id)">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Methods -->
          <div v-if="activeTab === 'payment'" class="profile-content">
            <div class="card border-0 shadow">
              <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                  <i class="bi bi-credit-card me-2"></i>Payment Methods
                </h5>
                <button class="btn btn-dark btn-sm" @click="addPaymentMethod">
                  <i class="bi bi-plus me-1"></i>Add Card
                </button>
              </div>
              <div class="card-body p-4">
                <div class="payment-methods">
                  <div 
                    v-for="card in paymentMethods" 
                    :key="card.id"
                    class="payment-card mb-3 p-3 border rounded"
                  >
                    <div class="row align-items-center">
                      <div class="col-md-8">
                        <div class="card-info">
                          <div class="card-brand mb-1">
                            <i :class="getCardIcon(card.type)" class="me-2"></i>
                            {{ card.type }} ending in {{ card.lastFour }}
                          </div>
                          <small class="text-muted">Expires {{ card.expiryMonth }}/{{ card.expiryYear }}</small>
                          <span v-if="card.isDefault" class="badge bg-primary ms-2">Default</span>
                        </div>
                      </div>
                      <div class="col-md-4 text-md-end">
                        <div class="card-actions">
                          <button class="btn btn-outline-primary btn-sm me-2" @click="editPaymentMethod(card)">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-outline-danger btn-sm" @click="deletePaymentMethod(card.id)">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Preferences -->
          <div v-if="activeTab === 'preferences'" class="profile-content">
            <div class="card border-0 shadow">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                  <i class="bi bi-gear me-2"></i>Preferences
                </h5>
              </div>
              <div class="card-body p-4">
                <form @submit.prevent="updatePreferences">
                  <div class="preferences-section mb-4">
                    <h6 class="section-title">Notifications</h6>
                    <div class="form-check form-switch mb-2">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        id="emailNotifications"
                        v-model="preferences.emailNotifications"
                      >
                      <label class="form-check-label" for="emailNotifications">
                        Email notifications for order updates
                      </label>
                    </div>
                    <div class="form-check form-switch mb-2">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        id="smsNotifications"
                        v-model="preferences.smsNotifications"
                      >
                      <label class="form-check-label" for="smsNotifications">
                        SMS notifications for delivery updates
                      </label>
                    </div>
                    <div class="form-check form-switch mb-2">
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        id="promotionalEmails"
                        v-model="preferences.promotionalEmails"
                      >
                      <label class="form-check-label" for="promotionalEmails">
                        Promotional emails and offers
                      </label>
                    </div>
                  </div>

                  <div class="preferences-section mb-4">
                    <h6 class="section-title">Dietary Preferences</h6>
                    <div class="dietary-options">
                      <div class="form-check mb-2" v-for="diet in dietaryOptions" :key="diet">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          :id="'diet-' + diet"
                          :value="diet"
                          v-model="preferences.dietaryRestrictions"
                        >
                        <label class="form-check-label" :for="'diet-' + diet">
                          {{ diet }}
                        </label>
                      </div>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-info">
                    <i class="bi bi-check-circle me-2"></i>Save Preferences
                  </button>
                </form>
              </div>
            </div>
          </div>

          <!-- Security -->
          <div v-if="activeTab === 'security'" class="profile-content">
            <div class="card border-0 shadow">
              <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                  <i class="bi bi-shield-lock me-2"></i>Security Settings
                </h5>
              </div>
              <div class="card-body p-4">
                <form @submit.prevent="changePassword">
                  <div class="mb-3">
                    <label class="form-label fw-bold">Current Password</label>
                    <input 
                      type="password" 
                      class="form-control" 
                      v-model="securityForm.currentPassword"
                      required
                    >
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-bold">New Password</label>
                    <input 
                      type="password" 
                      class="form-control" 
                      v-model="securityForm.newPassword"
                      required
                    >
                  </div>
                  <div class="mb-4">
                    <label class="form-label fw-bold">Confirm New Password</label>
                    <input 
                      type="password" 
                      class="form-control" 
                      v-model="securityForm.confirmPassword"
                      required
                    >
                  </div>
                  <button type="submit" class="btn btn-danger">
                    <i class="bi bi-key me-2"></i>Change Password
                  </button>
                </form>

                <hr class="my-4">

                <div class="two-factor-section">
                  <h6 class="section-title">Two-Factor Authentication</h6>
                  <p class="text-muted">Add an extra layer of security to your account</p>
                  <button class="btn btn-outline-success" @click="enableTwoFactor">
                    <i class="bi bi-shield-check me-2"></i>Enable 2FA
                  </button>
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
import { ref } from 'vue'

// Data
const activeTab = ref('personal')

const userProfile = ref({
  name: 'John Doe',
  email: 'john.doe@example.com',
  avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80',
  stats: {
    totalOrders: 47,
    totalSpent: 1247,
    memberSince: '2023'
  }
})

const personalInfo = ref({
  firstName: 'John',
  lastName: 'Doe',
  email: 'john.doe@example.com',
  phone: '+1 (555) 123-4567',
  dateOfBirth: '1990-05-15'
})

const addresses = ref([
  {
    id: 1,
    label: 'Home',
    street: '123 Main Street, Apt 4B',
    city: 'New York',
    state: 'NY',
    zipCode: '10001',
    isDefault: true
  },
  {
    id: 2,
    label: 'Work',
    street: '456 Business Ave, Suite 200',
    city: 'New York',
    state: 'NY',
    zipCode: '10002',
    isDefault: false
  }
])

const paymentMethods = ref([
  {
    id: 1,
    type: 'Visa',
    lastFour: '4242',
    expiryMonth: '12',
    expiryYear: '25',
    isDefault: true
  },
  {
    id: 2,
    type: 'Mastercard',
    lastFour: '8888',
    expiryMonth: '08',
    expiryYear: '26',
    isDefault: false
  }
])

const preferences = ref({
  emailNotifications: true,
  smsNotifications: true,
  promotionalEmails: false,
  dietaryRestrictions: ['Vegetarian']
})

const dietaryOptions = ref([
  'Vegetarian',
  'Vegan',
  'Gluten-Free',
  'Dairy-Free',
  'Nut-Free',
  'Halal',
  'Kosher'
])

const securityForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Methods
const setActiveTab = (tab) => {
  activeTab.value = tab
}

const changeAvatar = () => {
  console.log('Change avatar')
}

const updatePersonalInfo = () => {
  console.log('Update personal info:', personalInfo.value)
}

const addNewAddress = () => {
  console.log('Add new address')
}

const editAddress = (address) => {
  console.log('Edit address:', address)
}

const deleteAddress = (addressId) => {
  if (confirm('Are you sure you want to delete this address?')) {
    const index = addresses.value.findIndex(addr => addr.id === addressId)
    if (index > -1) {
      addresses.value.splice(index, 1)
    }
  }
}

const addPaymentMethod = () => {
  console.log('Add payment method')
}

const editPaymentMethod = (card) => {
  console.log('Edit payment method:', card)
}

const deletePaymentMethod = (cardId) => {
  if (confirm('Are you sure you want to delete this payment method?')) {
    const index = paymentMethods.value.findIndex(card => card.id === cardId)
    if (index > -1) {
      paymentMethods.value.splice(index, 1)
    }
  }
}

const getCardIcon = (type) => {
  const icons = {
    'Visa': 'bi bi-credit-card text-primary',
    'Mastercard': 'bi bi-credit-card text-warning',
    'Amex': 'bi bi-credit-card text-success'
  }
  return icons[type] || 'bi bi-credit-card'
}

const updatePreferences = () => {
  console.log('Update preferences:', preferences.value)
}

const changePassword = () => {
  if (securityForm.value.newPassword !== securityForm.value.confirmPassword) {
    alert('Passwords do not match')
    return
  }
  console.log('Change password')
}

const enableTwoFactor = () => {
  console.log('Enable 2FA')
}
</script>

<style scoped>
.navbar-spacer {
  height: 80px;
}

.profile-sidebar {
  position: sticky;
  top: 100px;
}

.profile-avatar {
  position: relative;
  display: inline-block;
}

.avatar-edit-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-name {
  font-weight: 700;
  color: var(--text-on-dark);
  margin-bottom: 0.5rem;
}

.profile-email {
  font-size: 0.9rem;
}

.profile-stats {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #f0f0f0;
}

.stat-number {
  font-weight: 700;
  color: var(--gold-500);
  margin-bottom: 0.25rem;
}

.profile-nav {
  display: flex;
  flex-direction: column;
}

.nav-item {
  display: block;
  padding: 1rem 1.5rem;
  color: var(--text-on-dark);
  text-decoration: none;
  border-bottom: 1px solid #f0f0f0;
  transition: all 0.3s ease;
}

.nav-item:hover {
  background-color: rgba(201,162,39,0.12);
  color: var(--gold-500);
}

.nav-item.active {
  background-color: var(--gold-500);
  color: #111;
}

.nav-item:last-child {
  border-bottom: none;
}

.profile-content {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.address-item,
.payment-card {
  transition: all 0.3s ease;
}

.address-item:hover,
.payment-card:hover {
  background-color: var(--black-800);
}

.address-label {
  font-weight: 600;
  color: var(--text-on-dark);
}

.address-text {
  font-size: 0.9rem;
  line-height: 1.4;
}

.card-info .card-brand {
  font-weight: 600;
  color: var(--text-on-dark);
}

.preferences-section {
  border-bottom: 1px solid #f0f0f0;
  padding-bottom: 1rem;
}

.preferences-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.section-title {
  font-weight: 600;
  color: var(--text-on-dark);
  margin-bottom: 1rem;
}

.form-check-input:checked {
  background-color: var(--gold-500);
  border-color: var(--gold-500);
}

.two-factor-section {
  padding-top: 1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .profile-sidebar {
    position: static;
    margin-bottom: 2rem;
  }
  
  .profile-nav {
    flex-direction: row;
    overflow-x: auto;
  }
  
  .nav-item {
    white-space: nowrap;
    border-bottom: none;
    border-right: 1px solid #f0f0f0;
  }
  
  .nav-item:last-child {
    border-right: none;
  }
  
  .address-actions,
  .card-actions {
    text-align: center;
    margin-top: 1rem;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .card-body {
    padding: 1.5rem;
  }
  
  .profile-stats .row {
    text-align: center;
  }
  
  .address-item .row,
  .payment-card .row {
    flex-direction: column;
  }
  
  .address-actions .btn,
  .card-actions .btn {
    margin: 0.25rem;
  }
}
</style>
