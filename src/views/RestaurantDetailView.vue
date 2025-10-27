<template>
  <div class="restaurant-detail-page">
    <!-- Restaurant Header -->
    <section class="restaurant-header py-4 bg-light">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <router-link to="/restaurants" class="text-decoration-none">Restaurants</router-link>
                </li>
                <li class="breadcrumb-item active">{{ restaurant.name }}</li>
              </ol>
            </nav>
            <h1 class="restaurant-name mb-2">{{ restaurant.name }}</h1>
            <div class="restaurant-info d-flex align-items-center gap-3 flex-wrap">
              <div class="rating">
                <span v-for="star in 5" :key="star" class="star">
                  <i :class="star <= restaurant.rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
                </span>
                <span class="ms-2">{{ restaurant.rating }} ({{ restaurant.reviews }} reviews)</span>
              </div>
              <span class="cuisine-type">{{ restaurant.cuisine }}</span>
              <span class="delivery-time">
                <i class="bi bi-clock me-1"></i>{{ restaurant.deliveryTime }} min
              </span>
              <span class="delivery-fee">
                <i class="bi bi-truck me-1"></i>${{ restaurant.deliveryFee }} delivery
              </span>
            </div>
          </div>
          <div class="col-md-4 text-md-end">
            <img :src="restaurant.image" :alt="restaurant.name" class="restaurant-hero-image img-fluid rounded">
          </div>
        </div>
      </div>
    </section>

    <!-- Menu Section -->
    <section class="menu-section py-5">
      <div class="container">
        <div class="row">
          <!-- Menu Categories Sidebar -->
          <div class="col-lg-3">
            <div class="menu-categories sticky-top">
              <h5 class="mb-3">Menu Categories</h5>
              <div class="list-group">
                <button 
                  v-for="category in menuCategories" 
                  :key="category.id"
                  class="list-group-item list-group-item-action"
                  :class="{ active: selectedCategory === category.id }"
                  @click="scrollToCategory(category.id)"
                >
                  {{ category.name }} ({{ category.items.length }})
                </button>
              </div>
            </div>
          </div>

          <!-- Menu Items -->
          <div class="col-lg-9">
            <div v-for="category in menuCategories" :key="category.id" class="menu-category mb-5">
              <h3 :id="'category-' + category.id" class="category-title mb-4">{{ category.name }}</h3>
              <div class="row g-4">
                <div v-for="item in category.items" :key="item.id" class="col-md-6">
                  <div class="menu-item-card">
                    <div class="card border-0 shadow-sm h-100">
                      <div class="row g-0 h-100">
                        <div class="col-4">
                          <img :src="item.image" :alt="item.name" class="menu-item-image">
                        </div>
                        <div class="col-8">
                          <div class="card-body d-flex flex-column h-100">
                            <div class="flex-grow-1">
                              <h6 class="item-name mb-2">{{ item.name }}</h6>
                              <p class="item-description text-muted mb-2">{{ item.description }}</p>
                              <div class="item-price mb-2">
                                <span class="price fw-bold text-primary">${{ item.price.toFixed(2) }}</span>
                                <span v-if="item.originalPrice" class="original-price text-muted ms-2">${{ item.originalPrice.toFixed(2) }}</span>
                              </div>
                            </div>
                            <div class="item-actions">
                              <button 
                                class="btn btn-primary btn-sm w-100"
                                @click="addToCart(item)"
                              >
                                <i class="bi bi-plus me-1"></i>Add to Cart
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Restaurant Info -->
    <section class="restaurant-info-section py-5 bg-light">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="info-card">
              <h4 class="mb-3">
                <i class="bi bi-info-circle me-2"></i>About {{ restaurant.name }}
              </h4>
              <p class="text-muted">{{ restaurant.description }}</p>
              <div class="restaurant-details">
                <div class="detail-item mb-2">
                  <strong>Cuisine:</strong> {{ restaurant.cuisine }}
                </div>
                <div class="detail-item mb-2">
                  <strong>Average Rating:</strong> {{ restaurant.rating }}/5
                </div>
                <div class="detail-item mb-2">
                  <strong>Total Reviews:</strong> {{ restaurant.reviews }}
                </div>
                <div class="detail-item mb-2">
                  <strong>Delivery Time:</strong> {{ restaurant.deliveryTime }} minutes
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-card">
              <h4 class="mb-3">
                <i class="bi bi-clock me-2"></i>Opening Hours
              </h4>
              <div class="opening-hours">
                <div v-for="(hours, day) in restaurant.openingHours" :key="day" class="hours-item d-flex justify-content-between mb-2">
                  <span class="day">{{ day }}:</span>
                  <span class="hours">{{ hours }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews-section py-5">
      <div class="container">
        <h3 class="mb-4">Customer Reviews</h3>
        <div class="row g-4">
          <div v-for="review in restaurant.customerReviews" :key="review.id" class="col-md-6">
            <div class="review-card">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="review-header d-flex justify-content-between align-items-start mb-3">
                    <div>
                      <h6 class="reviewer-name mb-1">{{ review.customerName }}</h6>
                      <div class="review-rating">
                        <span v-for="star in 5" :key="star" class="star">
                          <i :class="star <= review.rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
                        </span>
                      </div>
                    </div>
                    <small class="review-date text-muted">{{ formatDate(review.date) }}</small>
                  </div>
                  <p class="review-text text-muted mb-0">{{ review.comment }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import Footer from '@/components/Footer.vue'

const route = useRoute()
const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()

const selectedCategory = ref(1)

// Sample restaurant data - in real app, this would come from API
const restaurant = ref({
  id: parseInt(route.params.id),
  name: 'Bella Italia',
  cuisine: 'Italian',
  rating: 4.8,
  reviews: 324,
  deliveryTime: 25,
  deliveryFee: 2.99,
  description: 'Authentic Italian cuisine with fresh ingredients and traditional recipes. Our chefs bring the taste of Italy to your doorstep with handmade pasta, wood-fired pizzas, and classic Italian desserts.',
  image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
  openingHours: {
    'Monday': '11:00 AM - 10:00 PM',
    'Tuesday': '11:00 AM - 10:00 PM',
    'Wednesday': '11:00 AM - 10:00 PM',
    'Thursday': '11:00 AM - 10:00 PM',
    'Friday': '11:00 AM - 11:00 PM',
    'Saturday': '11:00 AM - 11:00 PM',
    'Sunday': '12:00 PM - 9:00 PM'
  },
  customerReviews: [
    {
      id: 1,
      customerName: 'Sarah Johnson',
      rating: 5,
      comment: 'Amazing food! The pasta was perfectly cooked and the sauce was incredible. Will definitely order again.',
      date: '2024-01-15'
    },
    {
      id: 2,
      customerName: 'Mike Chen',
      rating: 4,
      comment: 'Great pizza and fast delivery. The margherita was fresh and delicious.',
      date: '2024-01-10'
    },
    {
      id: 3,
      customerName: 'Emily Rodriguez',
      rating: 5,
      comment: 'Best Italian restaurant in the area! Authentic flavors and generous portions.',
      date: '2024-01-08'
    },
    {
      id: 4,
      customerName: 'David Kim',
      rating: 4,
      comment: 'Good food quality and reasonable prices. The tiramisu was outstanding!',
      date: '2024-01-05'
    }
  ]
})

const menuCategories = ref([
  {
    id: 1,
    name: 'Appetizers',
    items: [
      {
        id: 101,
        name: 'Bruschetta',
        description: 'Toasted bread with fresh tomatoes, basil, and garlic',
        price: 8.99,
        image: 'https://images.unsplash.com/photo-1572695157366-5e585ab2b69f?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      },
      {
        id: 102,
        name: 'Antipasto Platter',
        description: 'Selection of cured meats, cheeses, and marinated vegetables',
        price: 14.99,
        image: 'https://images.unsplash.com/photo-1544025162-d76694265947?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      }
    ]
  },
  {
    id: 2,
    name: 'Pizza',
    items: [
      {
        id: 201,
        name: 'Margherita Pizza',
        description: 'Fresh tomatoes, mozzarella cheese, basil leaves',
        price: 12.99,
        image: 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      },
      {
        id: 202,
        name: 'Pepperoni Pizza',
        description: 'Classic pepperoni with mozzarella cheese',
        price: 14.99,
        image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      },
      {
        id: 203,
        name: 'Quattro Stagioni',
        description: 'Four seasons pizza with ham, mushrooms, artichokes, and olives',
        price: 16.99,
        image: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      }
    ]
  },
  {
    id: 3,
    name: 'Pasta',
    items: [
      {
        id: 301,
        name: 'Spaghetti Carbonara',
        description: 'Classic Roman pasta with eggs, cheese, and pancetta',
        price: 13.99,
        image: 'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      },
      {
        id: 302,
        name: 'Fettuccine Alfredo',
        description: 'Creamy pasta with parmesan cheese and butter',
        price: 12.99,
        image: 'https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      }
    ]
  },
  {
    id: 4,
    name: 'Desserts',
    items: [
      {
        id: 401,
        name: 'Tiramisu',
        description: 'Classic Italian dessert with coffee and mascarpone',
        price: 6.99,
        image: 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      },
      {
        id: 402,
        name: 'Gelato',
        description: 'Authentic Italian ice cream - vanilla, chocolate, or strawberry',
        price: 4.99,
        image: 'https://images.unsplash.com/photo-1567206563064-6f60f40a2b57?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80'
      }
    ]
  }
])

// Methods
const scrollToCategory = (categoryId) => {
  selectedCategory.value = categoryId
  const element = document.getElementById(`category-${categoryId}`)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const addToCart = async (item) => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
    return
  }
  
  try {
    const result = await cartStore.addToCart({
      id: item.id,
      name: item.name,
      price: item.price,
      image: item.image,
      restaurant: restaurant.value.name
    }, 1)
    
    if (result.success) {
      console.log('Added to cart:', item.name)
    }
  } catch (error) {
    console.error('Error adding to cart:', error)
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  })
}

onMounted(() => {
  authStore.initializeAuth()
  // In a real app, fetch restaurant data based on route.params.id
})
</script>

<style scoped>
.restaurant-name {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
}

.restaurant-info {
  font-size: 0.9rem;
  color: #666;
}

.restaurant-info .star {
  font-size: 0.8rem;
}

.cuisine-type {
  background: #f8f9fa;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-weight: 500;
}

.restaurant-hero-image {
  max-height: 200px;
  object-fit: cover;
}

.menu-categories {
  top: 100px;
}

.category-title {
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #007bff;
  padding-bottom: 0.5rem;
}

.menu-item-card {
  transition: transform 0.3s ease;
}

.menu-item-card:hover {
  transform: translateY(-2px);
}

.menu-item-image {
  width: 100%;
  height: 120px;
  object-fit: cover;
}

.item-name {
  font-weight: 600;
  color: #333;
}

.item-description {
  font-size: 0.85rem;
  line-height: 1.4;
}

.price {
  font-size: 1.1rem;
}

.original-price {
  text-decoration: line-through;
  font-size: 0.9rem;
}

.info-card {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.detail-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.detail-item:last-child {
  border-bottom: none;
}

.hours-item {
  padding: 0.5rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.hours-item:last-child {
  border-bottom: none;
}

.day {
  font-weight: 500;
  text-transform: capitalize;
}

.review-card {
  height: 100%;
}

.reviewer-name {
  font-weight: 600;
  color: #333;
}

.review-rating .star {
  font-size: 0.8rem;
}

.review-text {
  font-size: 0.9rem;
  line-height: 1.5;
}

.review-date {
  font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .restaurant-name {
    font-size: 2rem;
  }
  
  .menu-categories {
    position: static;
    margin-bottom: 2rem;
  }
  
  .restaurant-hero-image {
    margin-top: 1rem;
    max-height: 150px;
  }
  
  .info-card {
    padding: 1.5rem;
    margin-bottom: 1rem;
  }
}

@media (max-width: 576px) {
  .restaurant-name {
    font-size: 1.8rem;
  }
  
  .restaurant-info {
    flex-direction: column;
    align-items: flex-start !important;
    gap: 0.5rem !important;
  }
  
  .menu-item-image {
    height: 100px;
  }
  
  .info-card {
    padding: 1rem;
  }
}
</style>
