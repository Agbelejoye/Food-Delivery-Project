<template>
  <div id="app">
    <Navbar />
    <RouterView />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import Navbar from './components/Navbar.vue'
</script>

<style>
body {
  margin: 0;
  padding: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
  background-color: var(--black-900);
  color: var(--text-on-dark);
}

#app {
  min-height: 100vh;
}

/* Global styles */
.btn {
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

.card {
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
}

.form-control, .form-select {
  border-radius: 8px;
  border: 1px solid #dee2e6;
  transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
  border-color: var(--gold-500);
  box-shadow: 0 0 0 0.2rem rgba(201,162,39,0.25);
}

/* Responsive utilities */
@media (max-width: 768px) {
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }
}
</style>

