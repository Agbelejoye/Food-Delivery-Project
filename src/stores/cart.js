import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { useAuthStore } from './auth'

export const useCartStore = defineStore('cart', () => {
  const authStore = useAuthStore()
  const items = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  const totalItems = computed(() => {
    return items.value.reduce((sum, item) => sum + item.quantity, 0)
  })

  const subtotal = computed(() => {
    return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
  })

  const fetchCart = async () => {
    if (!authStore.isAuthenticated) return

    isLoading.value = true
    error.value = null

    try {
      const response = await fetch('http://localhost/backend/api/cart', {
        headers: authStore.getAuthHeaders()
      })

      const data = await response.json()

      if (data.success) {
        items.value = data.data.items || []
      } else {
        error.value = data.message
      }
    } catch (err) {
      error.value = 'Failed to load cart'
    } finally {
      isLoading.value = false
    }
  }

  const addToCart = async (foodItem, quantity = 1, specialInstructions = '') => {
    if (!authStore.isAuthenticated) {
      error.value = 'Please login to add items to cart'
      return { success: false }
    }

    isLoading.value = true
    error.value = null

    try {
      const response = await fetch('http://localhost/backend/api/cart', {
        method: 'POST',
        headers: authStore.getAuthHeaders(),
        body: JSON.stringify({
          food_item_id: foodItem.id,
          quantity,
          special_instructions: specialInstructions
        })
      })

      const data = await response.json()

      if (data.success) {
        items.value = data.data.items || []
        return { success: true }
      } else {
        error.value = data.message
        return { success: false, message: data.message }
      }
    } catch (err) {
      error.value = 'Failed to add item to cart'
      return { success: false, message: 'Failed to add item to cart' }
    } finally {
      isLoading.value = false
    }
  }

  const updateCartItem = async (cartItemId, quantity, specialInstructions = '') => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fetch(`http://localhost/backend/api/cart/${cartItemId}`, {
        method: 'PUT',
        headers: authStore.getAuthHeaders(),
        body: JSON.stringify({
          quantity,
          special_instructions: specialInstructions
        })
      })

      const data = await response.json()

      if (data.success) {
        items.value = data.data.items || []
        return { success: true }
      } else {
        error.value = data.message
        return { success: false }
      }
    } catch (err) {
      error.value = 'Failed to update cart item'
      return { success: false }
    } finally {
      isLoading.value = false
    }
  }

  const removeFromCart = async (cartItemId) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fetch(`http://localhost/backend/api/cart/${cartItemId}`, {
        method: 'DELETE',
        headers: authStore.getAuthHeaders()
      })

      const data = await response.json()

      if (data.success) {
        items.value = data.data.items || []
        return { success: true }
      } else {
        error.value = data.message
        return { success: false }
      }
    } catch (err) {
      error.value = 'Failed to remove item from cart'
      return { success: false }
    } finally {
      isLoading.value = false
    }
  }

  const clearCart = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fetch('http://localhost/backend/api/cart/clear', {
        method: 'POST',
        headers: authStore.getAuthHeaders()
      })

      const data = await response.json()

      if (data.success) {
        items.value = []
        return { success: true }
      } else {
        error.value = data.message
        return { success: false }
      }
    } catch (err) {
      error.value = 'Failed to clear cart'
      return { success: false }
    } finally {
      isLoading.value = false
    }
  }

  return {
    items,
    isLoading,
    error,
    totalItems,
    subtotal,
    fetchCart,
    addToCart,
    updateCartItem,
    removeFromCart,
    clearCart
  }
})
