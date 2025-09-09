<template>
  <footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
      <div class="row">
        <!-- Company Info -->
        <div class="col-lg-4 col-md-6 mb-4">
          <h5 class="fw-bold mb-3">
            <i class="bi bi-basket3-fill me-2"></i>
            FoodExpress
          </h5>
          <p class="text-muted">
            Your favorite food delivery platform. Fast, reliable, and delicious meals delivered right to your doorstep. 
            We connect you with the best restaurants in your area.
          </p>
          <div class="d-flex gap-3">
            <a href="#" class="text-light fs-4"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-light fs-4"><i class="bi bi-twitter"></i></a>
            <a href="#" class="text-light fs-4"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-light fs-4"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">Quick Links</h6>
          <ul class="list-unstyled">
            <li><router-link to="/" class="text-muted text-decoration-none">Home</router-link></li>
            <li><router-link to="/menu" class="text-muted text-decoration-none">Browse Menu</router-link></li>
            <li><router-link to="/cart" class="text-muted text-decoration-none">My Cart</router-link></li>
            <li><router-link to="/orders" class="text-muted text-decoration-none">Order History</router-link></li>
            <li><router-link to="/profile" class="text-muted text-decoration-none">My Profile</router-link></li>
          </ul>
        </div>

        <!-- For Business -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">For Business</h6>
          <ul class="list-unstyled">
            <li><a href="#" class="text-muted text-decoration-none">Partner with Us</a></li>
            <li><router-link to="/register?role=restaurant" class="text-muted text-decoration-none">Add Restaurant</router-link></li>
            <li><router-link to="/register?role=agent" class="text-muted text-decoration-none">Become a Driver</router-link></li>
            <li><a href="#" class="text-muted text-decoration-none">Business Portal</a></li>
            <li><a href="#" class="text-muted text-decoration-none">API Documentation</a></li>
          </ul>
        </div>

        <!-- Support -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">Support</h6>
          <ul class="list-unstyled">
            <li><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
            <li><a href="#" class="text-muted text-decoration-none">Contact Us</a></li>
            <li><a href="#" class="text-muted text-decoration-none">FAQs</a></li>
            <li><a href="#" class="text-muted text-decoration-none">Report Issue</a></li>
            <li><a href="#" class="text-muted text-decoration-none">Safety</a></li>
          </ul>
        </div>

        <!-- Contact Info -->
        <div class="col-lg-2 col-md-6 mb-4">
          <h6 class="fw-bold mb-3">Contact</h6>
          <ul class="list-unstyled text-muted">
            <li class="mb-2">
              <i class="bi bi-geo-alt me-2"></i>
              123 Food Street<br>
              New York, NY 10001
            </li>
            <li class="mb-2">
              <i class="bi bi-telephone me-2"></i>
              +1 (555) 123-FOOD
            </li>
            <li class="mb-2">
              <i class="bi bi-envelope me-2"></i>
              support@foodexpress.com
            </li>
          </ul>
        </div>
      </div>

      <hr class="my-4">

      <!-- Bottom Footer -->
      <div class="row align-items-center">
        <div class="col-md-6">
          <p class="text-muted mb-0">
            &copy; 2024 FoodExpress. All rights reserved.
          </p>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="d-flex justify-content-md-end gap-3 flex-wrap">
            <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
            <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
            <a href="#" class="text-muted text-decoration-none">Cookie Policy</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// Footer component - no additional logic needed
</script>

<style scoped>
footer a:hover {
  color: #007bff !important;
  transition: color 0.3s ease;
}

footer .list-unstyled li {
  margin-bottom: 0.5rem;
}
</style>
