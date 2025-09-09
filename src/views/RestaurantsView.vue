<template>
  <div class="restaurants-page">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-4">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-8">
            <h1 class="display-5 fw-bold mb-3">Discover Restaurants</h1>
            <p class="lead mb-0">Find amazing restaurants near you and order your favorite meals</p>
          </div>
          <div class="col-lg-4">
            <div class="search-box">
              <div class="input-group">
                <input 
                  type="text" 
                  class="form-control" 
                  placeholder="Search restaurants..."
                  v-model="searchQuery"
                >
                <button class="btn btn-light" type="button" @click="searchRestaurants">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filters & Results -->
    <section class="restaurants-section py-5">
      <div class="container">
        <div class="row">
          <!-- Filters Sidebar -->
          <div class="col-lg-3">
            <div class="filters-sidebar">
              <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                  <h6 class="mb-0">
                    <i class="bi bi-funnel me-2"></i>Filters
                  </h6>
                </div>
                <div class="card-body">
                  <!-- Cuisine Filter -->
                  <div class="filter-section mb-4">
                    <h6 class="filter-title">Cuisine Type</h6>
                    <div class="cuisine-filters">
                      <div 
                        v-for="cuisine in cuisineTypes" 
                        :key="cuisine.id"
                        class="form-check mb-2"
                      >
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          :id="'cuisine-' + cuisine.id"
                          :value="cuisine.name"
                          v-model="selectedCuisines"
                        >
                        <label class="form-check-label" :for="'cuisine-' + cuisine.id">
                          {{ cuisine.name }} ({{ cuisine.count }})
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Rating Filter -->
                  <div class="filter-section mb-4">
                    <h6 class="filter-title">Rating</h6>
                    <div class="rating-filters">
                      <div 
                        v-for="rating in ratingOptions" 
                        :key="rating"
                        class="form-check mb-2"
                      >
                        <input 
                          class="form-check-input" 
                          type="radio" 
                          name="rating"
                          :id="'rating-' + rating"
                          :value="rating"
                          v-model="selectedRating"
                        >
                        <label class="form-check-label" :for="'rating-' + rating">
                          <span v-for="star in 5" :key="star" class="star">
                            <i :class="star <= rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
                          </span>
                          & up
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Delivery Time -->
                  <div class="filter-section mb-4">
                    <h6 class="filter-title">Delivery Time</h6>
                    <select class="form-select" v-model="deliveryTimeFilter">
                      <option value="">Any time</option>
                      <option value="15">Under 15 min</option>
                      <option value="30">Under 30 min</option>
                      <option value="45">Under 45 min</option>
                    </select>
                  </div>

                  <!-- Clear Filters -->
                  <button class="btn btn-outline-secondary btn-sm w-100" @click="clearFilters">
                    <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Restaurant Grid -->
          <div class="col-lg-9">
            <!-- Sort Options -->
            <div class="sort-section mb-4">
              <div class="row align-items-center">
                <div class="col-md-6">
                  <p class="results-count mb-0">
                    Showing {{ filteredRestaurants.length }} of {{ restaurants.length }} restaurants
                  </p>
                </div>
                <div class="col-md-6 text-md-end">
                  <select class="form-select form-select-sm" v-model="sortBy" style="width: auto; display: inline-block;">
                    <option value="name">Sort by Name</option>
                    <option value="rating">Rating</option>
                    <option value="delivery-time">Delivery Time</option>
                    <option value="popular">Most Popular</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Restaurant Cards -->
            <div class="restaurants-grid">
              <div class="row g-4">
                <div 
                  v-for="restaurant in paginatedRestaurants" 
                  :key="restaurant.id"
                  class="col-lg-6 col-xl-4"
                >
                  <div class="restaurant-card" @click="viewRestaurant(restaurant.id)">
                    <div class="card border-0 shadow-sm h-100">
                      <div class="restaurant-image">
                        <img :src="restaurant.image" :alt="restaurant.name" class="card-img-top">
                        <div class="restaurant-badges">
                          <span v-if="restaurant.isNew" class="badge bg-success">New</span>
                          <span v-if="restaurant.isFeatured" class="badge bg-primary">Featured</span>
                          <span v-if="restaurant.freeDelivery" class="badge bg-warning">Free Delivery</span>
                        </div>
                        <div class="delivery-time">
                          <span class="badge bg-dark">{{ restaurant.deliveryTime }} min</span>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="restaurant-header mb-2">
                          <h5 class="restaurant-name mb-1">{{ restaurant.name }}</h5>
                          <div class="restaurant-rating">
                            <span class="rating-stars">
                              <span v-for="star in 5" :key="star" class="star">
                                <i :class="star <= restaurant.rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
                              </span>
                            </span>
                            <small class="rating-count text-muted">({{ restaurant.reviews }})</small>
                          </div>
                        </div>
                        <p class="restaurant-cuisine text-muted mb-2">{{ restaurant.cuisine }}</p>
                        <p class="restaurant-description">{{ restaurant.description }}</p>
                        <div class="restaurant-footer">
                          <div class="delivery-info">
                            <small class="text-muted">
                              <i class="bi bi-truck me-1"></i>
                              ${{ restaurant.deliveryFee }} delivery • {{ restaurant.deliveryTime }} min
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
            <div class="pagination-section mt-5" v-if="totalPages > 1">
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
      </div>
    </section>

    <!-- Footer -->
    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Footer from '@/components/Footer.vue'

const router = useRouter()

// Data
const searchQuery = ref('')
const selectedCuisines = ref([])
const selectedRating = ref('')
const deliveryTimeFilter = ref('')
const sortBy = ref('name')
const currentPage = ref(1)
const itemsPerPage = 12

const cuisineTypes = ref([
  { id: 1, name: 'Italian', count: 25 },
  { id: 2, name: 'Chinese', count: 18 },
  { id: 3, name: 'Mexican', count: 15 },
  { id: 4, name: 'Indian', count: 22 },
  { id: 5, name: 'American', count: 30 },
  { id: 6, name: 'Japanese', count: 12 },
  { id: 7, name: 'Thai', count: 8 },
  { id: 8, name: 'Mediterranean', count: 10 }
])

const ratingOptions = ref([4, 3, 2, 1])

const restaurants = ref([
  {
    id: 1,
    name: 'Bella Italia',
    cuisine: 'Italian',
    rating: 4.8,
    reviews: 324,
    deliveryTime: 25,
    deliveryFee: 2.99,
    description: 'Authentic Italian cuisine with fresh ingredients and traditional recipes.',
    image: 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isNew: false,
    isFeatured: true,
    freeDelivery: false
  },
  {
    id: 2,
    name: 'Dragon Palace',
    cuisine: 'Chinese',
    rating: 4.6,
    reviews: 256,
    deliveryTime: 30,
    deliveryFee: 1.99,
    description: 'Traditional Chinese dishes with modern presentation and bold flavors.',
    image: 'https://images.unsplash.com/photo-1526318896980-cf78c088247c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isNew: false,
    isFeatured: false,
    freeDelivery: true
  },
  {
    id: 3,
    name: 'Taco Fiesta',
    cuisine: 'Mexican',
    rating: 4.4,
    reviews: 189,
    deliveryTime: 20,
    deliveryFee: 2.49,
    description: 'Fresh Mexican street food with authentic spices and premium ingredients.',
    image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isNew: true,
    isFeatured: false,
    freeDelivery: false
  },
  {
    id: 4,
    name: 'Spice Garden',
    cuisine: 'Indian',
    rating: 4.7,
    reviews: 412,
    deliveryTime: 35,
    deliveryFee: 3.49,
    description: 'Aromatic Indian curries and tandoor specialties made with authentic spices.',
    image: 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isNew: false,
    isFeatured: true,
    freeDelivery: false
  },
  {
    id: 5,
    name: 'Burger Junction',
    cuisine: 'American',
    rating: 4.3,
    reviews: 298,
    deliveryTime: 15,
    deliveryFee: 1.49,
    description: 'Gourmet burgers made with premium beef and fresh local ingredients.',
    image: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isNew: false,
    isFeatured: false,
    freeDelivery: true
  },
  {
    id: 6,
    name: 'Sakura Sushi',
    cuisine: 'Japanese',
    rating: 4.9,
    reviews: 156,
    deliveryTime: 40,
    deliveryFee: 4.99,
    description: 'Fresh sushi and sashimi prepared by experienced Japanese chefs.',
    image: 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isNew: false,
    isFeatured: true,
    freeDelivery: false
  }
])

// Computed properties
const filteredRestaurants = computed(() => {
  let filtered = restaurants.value

  // Search filter
  if (searchQuery.value) {
    filtered = filtered.filter(restaurant => 
      restaurant.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      restaurant.cuisine.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      restaurant.description.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Cuisine filter
  if (selectedCuisines.value.length > 0) {
    filtered = filtered.filter(restaurant => selectedCuisines.value.includes(restaurant.cuisine))
  }

  // Rating filter
  if (selectedRating.value) {
    filtered = filtered.filter(restaurant => restaurant.rating >= parseFloat(selectedRating.value))
  }

  // Delivery time filter
  if (deliveryTimeFilter.value) {
    filtered = filtered.filter(restaurant => restaurant.deliveryTime <= parseInt(deliveryTimeFilter.value))
  }

  // Sort restaurants
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'rating':
        return b.rating - a.rating
      case 'delivery-time':
        return a.deliveryTime - b.deliveryTime
      case 'popular':
        return b.reviews - a.reviews
      default:
        return a.name.localeCompare(b.name)
    }
  })

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredRestaurants.value.length / itemsPerPage))

const paginatedRestaurants = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredRestaurants.value.slice(start, end)
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
const searchRestaurants = () => {
  currentPage.value = 1
}

const viewRestaurant = (restaurantId) => {
  router.push(`/restaurants/${restaurantId}`)
}

const clearFilters = () => {
  selectedCuisines.value = []
  selectedRating.value = ''
  deliveryTimeFilter.value = ''
  searchQuery.value = ''
  currentPage.value = 1
}

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

onMounted(() => {
  // Initialize any data if needed
})
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.search-box .form-control {
  border-radius: 25px 0 0 25px;
}

.search-box .btn {
  border-radius: 0 25px 25px 0;
}

.filters-sidebar {
  position: sticky;
  top: 100px;
}

.filter-title {
  font-weight: 600;
  color: #333;
  margin-bottom: 1rem;
}

.filter-section {
  border-bottom: 1px solid #f0f0f0;
  padding-bottom: 1rem;
}

.filter-section:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.star {
  font-size: 0.8rem;
}

.results-count {
  color: #666;
  font-weight: 500;
}

.restaurant-card {
  cursor: pointer;
  transition: transform 0.3s ease;
}

.restaurant-card:hover {
  transform: translateY(-5px);
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
  transition: transform 0.3s ease;
}

.restaurant-card:hover .restaurant-image img {
  transform: scale(1.05);
}

.restaurant-badges {
  position: absolute;
  top: 10px;
  left: 10px;
}

.restaurant-badges .badge {
  margin-right: 5px;
  font-size: 0.7rem;
}

.delivery-time {
  position: absolute;
  top: 10px;
  right: 10px;
}

.restaurant-name {
  font-weight: 600;
  color: #333;
}

.restaurant-cuisine {
  font-weight: 500;
  text-transform: capitalize;
}

.restaurant-description {
  font-size: 0.9rem;
  line-height: 1.4;
  margin-bottom: 1rem;
  color: #666;
}

.rating-stars {
  margin-right: 0.5rem;
}

.rating-count {
  font-size: 0.8rem;
}

.delivery-info {
  font-size: 0.9rem;
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
  .filters-sidebar {
    position: static;
    margin-bottom: 2rem;
  }
  
  .search-box {
    margin-top: 1rem;
  }
  
  .sort-section .row {
    flex-direction: column;
    text-align: center;
  }
  
  .sort-section .col-md-6 {
    margin-bottom: 1rem;
  }
  
  .restaurant-image {
    height: 180px;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .restaurant-image {
    height: 160px;
  }
  
  .pagination {
    font-size: 0.9rem;
  }
}
</style>
