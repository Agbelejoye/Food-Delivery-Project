<template>
  <div class="menu-view">
    <!-- Navbar Spacer -->
    <div class="navbar-spacer"></div>
    
    <div class="container py-5">
      <!-- Header -->
      <div class="menu-header mb-5">
        <div class="row align-items-center">
          <div class="col-md-8">
            <h2 class="menu-title">
              <i class="bi bi-card-list me-3"></i>Browse Menu
            </h2>
            <p class="menu-subtitle">Discover delicious food from top restaurants</p>
          </div>
          <div class="col-md-4">
            <div class="search-box">
              <div class="input-group">
                <input 
                  type="text" 
                  class="form-control" 
                  placeholder="Search for food..."
                  v-model="searchQuery"
                >
                <button class="btn btn-primary" type="button">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

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
                <!-- Category Filter -->
                <div class="filter-section mb-4">
                  <h6 class="filter-title">Categories</h6>
                  <div class="category-filters">
                    <div 
                      v-for="category in categories" 
                      :key="category.id"
                      class="form-check mb-2"
                    >
                      <input 
                        class="form-check-input" 
                        type="checkbox" 
                        :id="'category-' + category.id"
                        :value="category.name"
                        v-model="selectedCategories"
                      >
                      <label class="form-check-label" :for="'category-' + category.id">
                        {{ category.name }} ({{ category.count }})
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Price Range -->
                <div class="filter-section mb-4">
                  <h6 class="filter-title">Price Range</h6>
                  <div class="price-range">
                    <div class="row g-2">
                      <div class="col-6">
                        <input 
                          type="number" 
                          class="form-control form-control-sm" 
                          placeholder="Min"
                          v-model="priceRange.min"
                        >
                      </div>
                      <div class="col-6">
                        <input 
                          type="number" 
                          class="form-control form-control-sm" 
                          placeholder="Max"
                          v-model="priceRange.max"
                        >
                      </div>
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

                <!-- Clear Filters -->
                <button class="btn btn-outline-secondary btn-sm w-100" @click="clearFilters">
                  <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Menu Items -->
        <div class="col-lg-9">
          <!-- Sort Options -->
          <div class="sort-section mb-4">
            <div class="row align-items-center">
              <div class="col-md-6">
                <p class="results-count mb-0">
                  Showing {{ filteredItems.length }} of {{ menuItems.length }} items
                </p>
              </div>
              <div class="col-md-6 text-md-end">
                <div class="sort-dropdown">
                  <select class="form-select form-select-sm" v-model="sortBy" style="width: auto; display: inline-block;">
                    <option value="name">Sort by Name</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="rating">Rating</option>
                    <option value="popular">Most Popular</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Menu Grid -->
          <div class="menu-grid">
            <div class="row g-4">
              <div 
                v-for="item in paginatedItems" 
                :key="item.id"
                class="col-lg-4 col-md-6"
              >
                <div class="menu-item-card">
                  <div class="card border-0 shadow-sm h-100">
                    <div class="item-image">
                      <img :src="item.image" :alt="item.name" class="card-img-top">
                      <div class="item-badges">
                        <span v-if="item.isPopular" class="badge bg-danger">Popular</span>
                        <span v-if="item.isNew" class="badge bg-success">New</span>
                        <span v-if="item.isVegetarian" class="badge bg-warning">Veg</span>
                      </div>
                      <button class="btn btn-primary add-to-cart-btn" @click="addToCart(item)">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                    <div class="card-body">
                      <div class="item-header mb-2">
                        <h6 class="item-name mb-1">{{ item.name }}</h6>
                        <div class="item-rating">
                          <span class="rating-stars">
                            <span v-for="star in 5" :key="star" class="star">
                              <i :class="star <= item.rating ? 'bi bi-star-fill text-warning' : 'bi bi-star text-muted'"></i>
                            </span>
                          </span>
                          <small class="rating-count text-muted">({{ item.reviews }})</small>
                        </div>
                      </div>
                      <p class="item-description text-muted">{{ item.description }}</p>
                      <div class="item-footer">
                        <div class="item-restaurant">
                          <small class="text-muted">
                            <i class="bi bi-shop me-1"></i>{{ item.restaurant }}
                          </small>
                        </div>
                        <div class="item-price">
                          <span class="price">${{ item.price.toFixed(2) }}</span>
                          <span v-if="item.originalPrice" class="original-price">${{ item.originalPrice.toFixed(2) }}</span>
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

// Data
const searchQuery = ref('')
const selectedCategories = ref([])
const priceRange = ref({ min: '', max: '' })
const selectedRating = ref('')
const sortBy = ref('name')
const currentPage = ref(1)
const itemsPerPage = 12

const categories = ref([
  { id: 1, name: 'Pizza', count: 45 },
  { id: 2, name: 'Burgers', count: 32 },
  { id: 3, name: 'Sushi', count: 28 },
  { id: 4, name: 'Chinese', count: 41 },
  { id: 5, name: 'Mexican', count: 23 },
  { id: 6, name: 'Desserts', count: 19 },
  { id: 7, name: 'Salads', count: 15 },
  { id: 8, name: 'Sandwiches', count: 26 }
])

const ratingOptions = ref([4, 3, 2, 1])

const menuItems = ref([
  {
    id: 1,
    name: 'Margherita Pizza',
    description: 'Fresh tomatoes, mozzarella cheese, basil leaves',
    price: 12.99,
    originalPrice: null,
    rating: 4.8,
    reviews: 124,
    restaurant: 'Bella Italia',
    category: 'Pizza',
    image: 'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isPopular: true,
    isNew: false,
    isVegetarian: true
  },
  {
    id: 2,
    name: 'Classic Burger',
    description: 'Beef patty, lettuce, tomato, onion, special sauce',
    price: 9.99,
    originalPrice: 11.99,
    rating: 4.6,
    reviews: 89,
    restaurant: 'Burger Palace',
    category: 'Burgers',
    image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isPopular: false,
    isNew: false,
    isVegetarian: false
  },
  {
    id: 3,
    name: 'Salmon Roll',
    description: 'Fresh salmon, avocado, cucumber, sesame seeds',
    price: 15.99,
    originalPrice: null,
    rating: 4.9,
    reviews: 67,
    restaurant: 'Sakura Sushi',
    category: 'Sushi',
    image: 'https://images.unsplash.com/photo-1617196034796-73dfa7b1fd56?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isPopular: true,
    isNew: true,
    isVegetarian: false
  },
  {
    id: 4,
    name: 'Chicken Tacos',
    description: 'Grilled chicken, salsa, lettuce, cheese, sour cream',
    price: 8.99,
    originalPrice: null,
    rating: 4.4,
    reviews: 156,
    restaurant: 'El Mariachi',
    category: 'Mexican',
    image: 'https://yandex-images.clstorage.net/4NuW7S239/023aa7URz/AQFbQPrZhjAFUvTAKHLdID_PXAGE687BTsEDWM8OTH0AuhL02YHOALDqMsLLHTfF1sKg5hMzVy3OV9pOyKEwBlwVRl3L1IJdYB_cuQlCwQQvsnB0pOrSsAbtXzWSY0EUdKoa_WQgB8AgBoNDM5WfZaLenOYDRmp4lwM_7ol8tZNREddAHtFrhK1emdQk4qG45zrYqRTmvkxi-XY1zs_YoNlnj2kocMMg4MYbt4ud48XOmpjSkz86VLPjIy_pGK3jsVXXAMKF6xiV9hVwpPqhKIeKaFkwNmqs4kEiaUozzKBdd8MhPHCvcJSyOz_L2QtF8ppgntOHQjzrExtHWYSpQwEd53TSKasAJX5pEGAW4awbMryhBKpuqOa1Co1W5zwgbQc7oVTop-wEkg_jpyGrdfr-_FYfH-Jws4Orh1HAXQPBQVuA-lFrML32iSCYyn3g9yKs8bRyQqSC_d4xjue0_MV3H71cyL8I5L73m9ttlxGWUjDyy6O2uKdfI6PhkMlftR2TKC4xU6TlSl1A1Nb9JLdmxL04cva4nq0G-cqD0Jjln2epvPBDrJymf09PqbeNrsKQXktHcqwj_-_DaRy1y7EVz4D2BauAYWolYNSi0SxPPugRCObieL69ujmSywxM_WNn9bTAi2iActuP08mLnV4SCLq3O-YwwyObIxHsKfNFRW_wmiFnIC32EdRQnnXs6zrQgSz6ZmwWwR6FNrPAmNnP97XYeJt8rMb7B5vBPxlqzggiC3fycLMbF0s54MVrERW33DoZXwA9doH8qFrB1BMOXI1Yrlp8JjmeYSJrTDQta8_Z1ISbJJRSIw8Lof891gqILpsbYkTLB1PDQdzN3-G5Q5iuLfPEffYp7HCWXZCjOtTxPILerOo9dnl-C9A8XdvvGYhso3BgZv_vz63jtfa-8F7LH34k85_zc9HQOQtNff8kLtGfPOka2fAssiW05wa0ZVymvlgeoR4pAu9k',
    isPopular: false,
    isNew: false,
    isVegetarian: false
  },
  {
    id: 5,
    name: 'Caesar Salad',
    description: 'Romaine lettuce, parmesan, croutons, caesar dressing',
    price: 7.99,
    originalPrice: null,
    rating: 4.2,
    reviews: 43,
    restaurant: 'Green Garden',
    category: 'Salads',
    image: 'https://images.unsplash.com/photo-1546793665-c74683f339c1?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isPopular: false,
    isNew: false,
    isVegetarian: true
  },
  {
    id: 6,
    name: 'Chocolate Cake',
    description: 'Rich chocolate cake with chocolate frosting',
    price: 6.99,
    originalPrice: null,
    rating: 4.7,
    reviews: 92,
    restaurant: 'Sweet Treats',
    category: 'Desserts',
    image: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80',
    isPopular: true,
    isNew: false,
    isVegetarian: true
  }
])

// Computed properties
const filteredItems = computed(() => {
  let items = menuItems.value

  // Search filter
  if (searchQuery.value) {
    items = items.filter(item => 
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.restaurant.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  }

  // Category filter
  if (selectedCategories.value.length > 0) {
    items = items.filter(item => selectedCategories.value.includes(item.category))
  }

  // Price filter
  if (priceRange.value.min) {
    items = items.filter(item => item.price >= parseFloat(priceRange.value.min))
  }
  if (priceRange.value.max) {
    items = items.filter(item => item.price <= parseFloat(priceRange.value.max))
  }

  // Rating filter
  if (selectedRating.value) {
    items = items.filter(item => item.rating >= parseFloat(selectedRating.value))
  }

  // Sort items
  items.sort((a, b) => {
    switch (sortBy.value) {
      case 'price-low':
        return a.price - b.price
      case 'price-high':
        return b.price - a.price
      case 'rating':
        return b.rating - a.rating
      case 'popular':
        return b.reviews - a.reviews
      default:
        return a.name.localeCompare(b.name)
    }
  })

  return items
})

const totalPages = computed(() => Math.ceil(filteredItems.value.length / itemsPerPage))

const paginatedItems = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredItems.value.slice(start, end)
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
      restaurant: item.restaurant
    }, 1)
    
    if (result.success) {
      // Show success feedback (could add toast notification here)
      console.log('Added to cart:', item.name)
    } else {
      console.error('Failed to add to cart:', result.message)
    }
  } catch (error) {
    console.error('Error adding to cart:', error)
  }
}

const clearFilters = () => {
  selectedCategories.value = []
  priceRange.value = { min: '', max: '' }
  selectedRating.value = ''
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
  authStore.initializeAuth()
  if (authStore.isAuthenticated) {
    cartStore.fetchCart()
  }
})
</script>

<style scoped>
.navbar-spacer {
  height: 80px;
}

.menu-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-on-dark);
}

.menu-subtitle {
  color: #cfcfc9;
  font-size: 1.1rem;
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
  color: var(--text-on-dark);
  margin-bottom: 1rem;
}

.filter-section {
  border-bottom: 1px solid #2a2b2f;
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
  color: #cfcfc9;
  font-weight: 500;
}

.menu-item-card {
  transition: transform 0.3s ease;
}

.menu-item-card:hover {
  transform: translateY(-5px);
}

.item-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.menu-item-card:hover .item-image img {
  transform: scale(1.05);
}

.item-badges {
  position: absolute;
  top: 10px;
  left: 10px;
}

.item-badges .badge {
  margin-right: 5px;
  font-size: 0.7rem;
}

.add-to-cart-btn {
  position: absolute;
  bottom: 10px;
  right: 10px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.menu-item-card:hover .add-to-cart-btn {
  opacity: 1;
}

.item-name {
  font-weight: 600;
  color: var(--text-on-dark);
}

.item-description {
  font-size: 0.9rem;
  line-height: 1.4;
  margin-bottom: 1rem;
}

.item-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--gold-500);
}

.original-price {
  font-size: 0.9rem;
  color: #999;
  text-decoration: line-through;
  margin-left: 0.5rem;
}

.rating-stars {
  margin-right: 0.5rem;
}

.rating-count {
  font-size: 0.8rem;
}

.pagination .page-link {
  border-radius: 8px;
  margin: 0 2px;
  border: 1px solid #2a2b2f;
  background: var(--black-800);
  color: var(--text-on-dark);
}

.pagination .page-item.active .page-link {
  background-color: var(--gold-500);
  border-color: var(--gold-500);
  color: #111;
}

/* Responsive Design */
@media (max-width: 768px) {
  .menu-title {
    font-size: 2rem;
  }
  
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
  
  .item-image {
    height: 180px;
  }
}

@media (max-width: 576px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  
  .menu-title {
    font-size: 1.8rem;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .item-image {
    height: 160px;
  }
  
  .pagination {
    font-size: 0.9rem;
  }
}
</style>
