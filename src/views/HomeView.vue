<template>
  <div class="home">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
      <div class="container">
        <div class="row align-items-center min-vh-100">
          <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">
              Delicious Food Delivered to Your Door
            </h1>
            <p class="lead mb-4">
              Order from your favorite restaurants and get fresh, hot meals delivered fast. 
              Browse hundreds of cuisines and discover new flavors in your neighborhood. 
              Experience the convenience of food delivery with FoodExpress - your trusted partner for quality meals.
            </p>
            <div class="d-flex gap-3 mb-4 flex-wrap">
              <router-link to="/menu" class="btn btn-light btn-lg px-4">
                <i class="bi bi-search me-2"></i>
                Browse Restaurants
              </router-link>
              <button class="btn btn-outline-light btn-lg px-4" @click="scrollToHowItWorks">
                <i class="bi bi-play-circle me-2"></i>
                How it Works
              </button>
            </div>
            <div class="d-flex align-items-center gap-4 text-light">
              <div class="text-center">
                <h3 class="fw-bold mb-0">500+</h3>
                <small>Restaurants</small>
              </div>
              <div class="text-center">
                <h3 class="fw-bold mb-0">50K+</h3>
                <small>Happy Customers</small>
              </div>
              <div class="text-center">
                <h3 class="fw-bold mb-0">30min</h3>
                <small>Avg Delivery</small>
              </div>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <img src="/api/placeholder/500/400" alt="Food delivery hero image showing delicious meals" class="img-fluid rounded shadow">
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">Browse by Category</h2>
          <p class="section-subtitle">Discover amazing food from different cuisines</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-2 col-md-4 col-6" v-for="category in categories" :key="category.id">
            <div class="category-card" @click="browseCategory(category.name)">
              <div class="category-icon">
                <i :class="category.icon"></i>
              </div>
              <h6 class="category-name">{{ category.name }}</h6>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Restaurants -->
    <section class="featured-section py-5 bg-light">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">Featured Restaurants</h2>
          <p class="section-subtitle">Top-rated restaurants in your area</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-4 col-md-6" v-for="restaurant in featuredRestaurants" :key="restaurant.id">
            <div class="restaurant-card">
              <div class="restaurant-image">
                <img :src="restaurant.image" :alt="restaurant.name" class="img-fluid">
                <div class="restaurant-badge">
                  <span class="badge bg-success">{{ restaurant.rating }} ⭐</span>
                </div>
              </div>
              <div class="restaurant-info">
                <h5 class="restaurant-name">{{ restaurant.name }}</h5>
                <p class="restaurant-cuisine">{{ restaurant.cuisine }}</p>
                <div class="restaurant-details">
                  <span class="delivery-time">
                    <i class="bi bi-clock me-1"></i>{{ restaurant.deliveryTime }}
                  </span>
                  <span class="delivery-fee">
                    <i class="bi bi-truck me-1"></i>${{ restaurant.deliveryFee }}
                  </span>
                </div>
                <button class="btn btn-primary w-100 mt-3" @click="viewRestaurant(restaurant.id)">
                  View Menu
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Dishes -->
    <section class="popular-dishes py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">Popular Dishes</h2>
          <p class="section-subtitle">Most ordered items this week</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-3 col-md-6" v-for="dish in popularDishes" :key="dish.id">
            <div class="dish-card">
              <div class="dish-image">
                <img :src="dish.image" :alt="dish.name" class="img-fluid">
                <button class="btn btn-sm btn-warning add-to-cart-btn" @click="addToCart(dish)">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
              <div class="dish-info">
                <h6 class="dish-name">{{ dish.name }}</h6>
                <p class="dish-restaurant">{{ dish.restaurant }}</p>
                <div class="dish-price">${{ dish.price }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="how-it-works py-5 bg-primary text-white">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title text-white">How It Works</h2>
          <p class="section-subtitle text-white-50">Simple steps to get your food delivered quickly and safely</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-4 text-center" v-for="step in howItWorks" :key="step.id">
            <div class="step-card">
              <div class="step-icon">
                <i :class="step.icon"></i>
              </div>
              <h4 class="step-title">{{ step.title }}</h4>
              <p class="step-description">{{ step.description }}</p>
            </div>
          </div>
        </div>
        <div class="text-center mt-5">
          <router-link to="/register" class="btn btn-light btn-lg px-5">
            <i class="bi bi-person-plus me-2"></i>
            Get Started Now
          </router-link>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import Footer from '@/components/Footer.vue'

const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()
const searchLocation = ref('')

// Sample data
const categories = ref([
  { id: 1, name: 'Pizza', icon: 'bi bi-circle-fill text-danger' },
  { id: 2, name: 'Burger', icon: 'bi bi-circle-fill text-warning' },
  { id: 3, name: 'Sushi', icon: 'bi bi-circle-fill text-info' },
  { id: 4, name: 'Chinese', icon: 'bi bi-circle-fill text-success' },
  { id: 5, name: 'Mexican', icon: 'bi bi-circle-fill text-primary' },
  { id: 6, name: 'Desserts', icon: 'bi bi-circle-fill text-pink' }
])

const featuredRestaurants = ref([
  {
    id: 1,
    name: 'Bella Italia',
    cuisine: 'Italian • Pizza',
    rating: 4.8,
    deliveryTime: '25-35 min',
    deliveryFee: 2.99,
    image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
  },
  {
    id: 2,
    name: 'Burger Palace',
    cuisine: 'American • Burgers',
    rating: 4.6,
    deliveryTime: '20-30 min',
    deliveryFee: 1.99,
    image: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
  },
  {
    id: 3,
    name: 'Sakura Sushi',
    cuisine: 'Japanese • Sushi',
    rating: 4.9,
    deliveryTime: '30-40 min',
    deliveryFee: 3.99,
    image: 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'
  }
])

const popularDishes = ref([
  {
    id: 1,
    name: 'Margherita Pizza',
    restaurant: 'Bella Italia',
    price: 12.99,
    image: 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
  },
  {
    id: 2,
    name: 'Classic Burger',
    restaurant: 'Burger Palace',
    price: 9.99,
    image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
  },
  {
    id: 3,
    name: 'Salmon Roll',
    restaurant: 'Sakura Sushi',
    price: 15.99,
    image: 'https://images.unsplash.com/photo-1617196034796-73dfa7b1fd56?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
  },
  {
    id: 4,
    name: 'Chicken Tacos',
    restaurant: 'El Mariachi',
    price: 8.99,
    image: 'https://images.unsplash.com/photo-1565299585323-38174c4a6471?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80'
  }
])

const howItWorks = ref([
  {
    id: 1,
    icon: 'bi bi-geo-alt-fill',
    title: 'Choose Location',
    description: 'Enter your address to see restaurants near you'
  },
  {
    id: 2,
    icon: 'bi bi-cart-fill',
    title: 'Select Food',
    description: 'Browse menus and add items to your cart'
  },
  {
    id: 3,
    icon: 'bi bi-truck',
    title: 'Fast Delivery',
    description: 'Get your food delivered hot and fresh'
  }
])

// Methods
const scrollToHowItWorks = () => {
  document.getElementById('how-it-works')?.scrollIntoView({ behavior: 'smooth' })
}

const searchRestaurants = () => {
  if (searchLocation.value.trim()) {
    router.push({ path: '/menu', query: { location: searchLocation.value } })
  }
}

const browseCategory = (category) => {
  router.push({ path: '/menu', query: { category: category.toLowerCase() } })
}

const viewRestaurant = (restaurantId) => {
  router.push(`/restaurants/${restaurantId}`)
}

const addToCart = async (dish) => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  
  const result = await cartStore.addToCart(dish, 1)
  if (result.success) {
    // Show success message or toast
    console.log('Added to cart:', dish.name)
  }
}

onMounted(() => {
  authStore.initializeAuth()
})
</script>

<style scoped>
/* Hero Section */
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  padding-top: 80px;
}

.hero-overlay {
  background: rgba(0,0,0,0.3);
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  color: white;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 1.2rem;
  color: rgba(255,255,255,0.9);
  margin-bottom: 2rem;
}

.hero-search .form-control {
  border: none;
  border-radius: 50px 0 0 50px;
  padding: 1rem 1.5rem;
}

.hero-search .btn {
  border-radius: 0 50px 50px 0;
  padding: 1rem 2rem;
  font-weight: 600;
}

.hero-stats h3 {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.hero-stats p {
  color: rgba(255,255,255,0.8);
  margin-bottom: 0;
}

/* Sections */
.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: #666;
  margin-bottom: 0;
}

/* Categories */
.category-card {
  background: white;
  border-radius: 15px;
  padding: 2rem 1rem;
  text-align: center;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.category-icon {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.category-name {
  font-weight: 600;
  color: #333;
  margin-bottom: 0;
}

/* Restaurant Cards */
.restaurant-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.restaurant-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.restaurant-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.restaurant-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.restaurant-badge {
  position: absolute;
  top: 15px;
  right: 15px;
}

.restaurant-info {
  padding: 1.5rem;
}

.restaurant-name {
  font-weight: 700;
  color: #333;
  margin-bottom: 0.5rem;
}

.restaurant-cuisine {
  color: #666;
  margin-bottom: 1rem;
}

.restaurant-details {
  display: flex;
  justify-content: space-between;
  font-size: 0.9rem;
  color: #666;
}

/* Dish Cards */
.dish-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.dish-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.dish-image {
  position: relative;
  height: 180px;
  overflow: hidden;
}

.dish-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.add-to-cart-btn {
  position: absolute;
  bottom: 15px;
  right: 15px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dish-info {
  padding: 1rem;
}

.dish-name {
  font-weight: 600;
  color: #333;
  margin-bottom: 0.5rem;
}

.dish-restaurant {
  color: #666;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.dish-price {
  font-size: 1.2rem;
  font-weight: 700;
  color: #007bff;
}

/* How It Works */
.step-card {
  padding: 2rem 1rem;
}

.step-icon {
  font-size: 3rem;
  margin-bottom: 1.5rem;
  color: #ffc107;
}

.step-title {
  font-weight: 700;
  margin-bottom: 1rem;
}

.step-description {
  color: rgba(255,255,255,0.8);
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-title {
    font-size: 2.5rem;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .hero-stats {
    margin-top: 2rem;
  }
  
  .categories-section .col-6 {
    margin-bottom: 1rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-search .input-group-lg .form-control,
  .hero-search .input-group-lg .btn {
    font-size: 1rem;
    padding: 0.75rem 1rem;
  }
}
</style>
