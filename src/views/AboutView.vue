<template>
  <div class="about-page">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
      <div class="container">
        <div class="row align-items-center min-vh-50">
          <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">About FoodExpress</h1>
            <p class="lead mb-4">
              We're passionate about connecting food lovers with their favorite restaurants, 
              delivering exceptional dining experiences right to your doorstep since 2020.
            </p>
            <div class="d-flex gap-3 mb-4">
              <router-link to="/menu" class="btn btn-light btn-lg">
                <i class="bi bi-search me-2"></i>
                Browse Menu
              </router-link>
              <router-link to="/contact" class="btn btn-outline-light btn-lg">
                <i class="bi bi-envelope me-2"></i>
                Contact Us
              </router-link>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <img src="/api/placeholder/500/400" alt="About FoodExpress" class="img-fluid rounded shadow">
          </div>
        </div>
      </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="section-title mb-4">Our Mission</h2>
            <p class="lead text-muted mb-5">
              To revolutionize food delivery by creating seamless connections between restaurants, 
              customers, and delivery partners while maintaining the highest standards of quality, 
              speed, and customer satisfaction.
            </p>
          </div>
        </div>
        
        <div class="row g-4">
          <div class="col-md-4 text-center">
            <div class="mission-card">
              <div class="mission-icon mb-3">
                <i class="bi bi-lightning-charge-fill text-warning"></i>
              </div>
              <h4 class="mission-title">Fast Delivery</h4>
              <p class="mission-description">
                Lightning-fast delivery with real-time tracking to ensure your food arrives hot and fresh.
              </p>
            </div>
          </div>
          <div class="col-md-4 text-center">
            <div class="mission-card">
              <div class="mission-icon mb-3">
                <i class="bi bi-heart-fill text-danger"></i>
              </div>
              <h4 class="mission-title">Quality First</h4>
              <p class="mission-description">
                We partner only with restaurants that meet our strict quality and hygiene standards.
              </p>
            </div>
          </div>
          <div class="col-md-4 text-center">
            <div class="mission-card">
              <div class="mission-icon mb-3">
                <i class="bi bi-people-fill text-success"></i>
              </div>
              <h4 class="mission-title">Community Focus</h4>
              <p class="mission-description">
                Supporting local restaurants and creating opportunities for delivery partners in our community.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5 bg-light">
      <div class="container">
        <div class="row text-center">
          <div class="col-md-3 col-6 mb-4">
            <div class="stat-item">
              <h3 class="stat-number text-primary">500+</h3>
              <p class="stat-label">Partner Restaurants</p>
            </div>
          </div>
          <div class="col-md-3 col-6 mb-4">
            <div class="stat-item">
              <h3 class="stat-number text-primary">50K+</h3>
              <p class="stat-label">Happy Customers</p>
            </div>
          </div>
          <div class="col-md-3 col-6 mb-4">
            <div class="stat-item">
              <h3 class="stat-number text-primary">100K+</h3>
              <p class="stat-label">Orders Delivered</p>
            </div>
          </div>
          <div class="col-md-3 col-6 mb-4">
            <div class="stat-item">
              <h3 class="stat-number text-primary">25min</h3>
              <p class="stat-label">Average Delivery</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="team-section py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">Meet Our Team</h2>
          <p class="section-subtitle">The passionate people behind FoodExpress</p>
        </div>
        
        <div class="row g-4">
          <div v-for="member in teamMembers" :key="member.id" class="col-lg-3 col-md-6">
            <div class="team-card text-center">
              <div class="team-image mb-3">
                <img :src="member.image" :alt="member.name" class="img-fluid rounded-circle">
              </div>
              <h5 class="team-name">{{ member.name }}</h5>
              <p class="team-role text-primary">{{ member.role }}</p>
              <p class="team-bio text-muted">{{ member.bio }}</p>
              <div class="team-social">
                <a v-for="social in member.social" :key="social.platform" :href="social.url" class="social-link me-2">
                  <i :class="social.icon"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Values Section -->
    <section class="values-section py-5 bg-primary text-white">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title text-white">Our Values</h2>
          <p class="section-subtitle text-white-50">What drives us every day</p>
        </div>
        
        <div class="row g-4">
          <div v-for="value in companyValues" :key="value.id" class="col-md-6 col-lg-3">
            <div class="value-card text-center">
              <div class="value-icon mb-3">
                <i :class="value.icon"></i>
              </div>
              <h4 class="value-title">{{ value.title }}</h4>
              <p class="value-description">{{ value.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 mx-auto text-center">
            <h2 class="cta-title mb-4">Ready to Join FoodExpress?</h2>
            <p class="cta-description mb-4">
              Whether you're a food lover, restaurant owner, or looking to become a delivery partner, 
              we have opportunities for everyone.
            </p>
            <div class="cta-buttons">
              <router-link to="/register" class="btn btn-primary btn-lg me-3 mb-2">
                <i class="bi bi-person-plus me-2"></i>
                Join as Customer
              </router-link>
              <router-link to="/register" class="btn btn-outline-primary btn-lg me-3 mb-2">
                <i class="bi bi-shop me-2"></i>
                Partner Restaurant
              </router-link>
              <router-link to="/register" class="btn btn-outline-primary btn-lg mb-2">
                <i class="bi bi-bicycle me-2"></i>
                Become Delivery Partner
              </router-link>
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
import { ref } from 'vue'
import Footer from '@/components/Footer.vue'

const teamMembers = ref([
  {
    id: 1,
    name: 'Sarah Johnson',
    role: 'CEO & Founder',
    bio: 'Passionate about revolutionizing food delivery with 10+ years in tech.',
    image: '/api/placeholder/200/200',
    social: [
      { platform: 'linkedin', icon: 'bi bi-linkedin', url: '#' },
      { platform: 'twitter', icon: 'bi bi-twitter', url: '#' }
    ]
  },
  {
    id: 2,
    name: 'Michael Chen',
    role: 'CTO',
    bio: 'Tech visionary building scalable platforms for seamless user experiences.',
    image: '/api/placeholder/200/200',
    social: [
      { platform: 'linkedin', icon: 'bi bi-linkedin', url: '#' },
      { platform: 'github', icon: 'bi bi-github', url: '#' }
    ]
  },
  {
    id: 3,
    name: 'Emily Rodriguez',
    role: 'Head of Operations',
    bio: 'Ensuring smooth operations and exceptional customer service daily.',
    image: '/api/placeholder/200/200',
    social: [
      { platform: 'linkedin', icon: 'bi bi-linkedin', url: '#' },
      { platform: 'twitter', icon: 'bi bi-twitter', url: '#' }
    ]
  },
  {
    id: 4,
    name: 'David Kim',
    role: 'Head of Marketing',
    bio: 'Creative strategist connecting brands with food-loving communities.',
    image: '/api/placeholder/200/200',
    social: [
      { platform: 'linkedin', icon: 'bi bi-linkedin', url: '#' },
      { platform: 'instagram', icon: 'bi bi-instagram', url: '#' }
    ]
  }
])

const companyValues = ref([
  {
    id: 1,
    title: 'Reliability',
    description: 'Consistent, dependable service you can count on every time.',
    icon: 'bi bi-shield-check text-white'
  },
  {
    id: 2,
    title: 'Innovation',
    description: 'Continuously improving through technology and creative solutions.',
    icon: 'bi bi-lightbulb text-white'
  },
  {
    id: 3,
    title: 'Transparency',
    description: 'Open, honest communication in all our business practices.',
    icon: 'bi bi-eye text-white'
  },
  {
    id: 4,
    title: 'Sustainability',
    description: 'Committed to environmentally responsible business practices.',
    icon: 'bi bi-leaf text-white'
  }
])
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, var(--black-900) 0%, var(--black-800) 100%);
  min-height: 50vh;
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-on-dark);
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.2rem;
  color: #cfcfc9;
  margin-bottom: 2rem;
}

.mission-card {
  padding: 2rem 1rem;
  height: 100%;
  transition: transform 0.3s ease;
}

.mission-card:hover {
  transform: translateY(-5px);
}

.mission-icon {
  font-size: 3rem;
}

.mission-title {
  font-weight: 600;
  color: var(--text-on-dark);
  margin-bottom: 1rem;
}

.mission-description {
  color: #cfcfc9;
  line-height: 1.6;
}

.stat-item {
  padding: 1rem;
}

.stat-number {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 1.1rem;
  color: #cfcfc9;
  margin: 0;
}

.team-card {
  padding: 2rem 1rem;
  background: var(--black-800);
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
  transition: transform 0.3s ease;
}

.team-card:hover {
  transform: translateY(-5px);
}

.team-image img {
  width: 120px;
  height: 120px;
  object-fit: cover;
}

.team-name {
  font-weight: 600;
  color: var(--text-on-dark);
  margin-bottom: 0.5rem;
}

.team-role {
  font-weight: 500;
  margin-bottom: 1rem;
}

.team-bio {
  font-size: 0.9rem;
  line-height: 1.5;
  margin-bottom: 1rem;
}

.social-link {
  color: #cfcfc9;
  font-size: 1.2rem;
  text-decoration: none;
  transition: color 0.3s ease;
}

.social-link:hover {
  color: var(--gold-500);
}

.value-card {
  padding: 2rem 1rem;
}

.value-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.value-title {
  color: white;
  font-weight: 600;
  margin-bottom: 1rem;
}

.value-description {
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.6;
}

.cta-section {
  background: var(--black-800);
}

.cta-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: var(--text-on-dark);
}

.cta-description {
  font-size: 1.2rem;
  color: #cfcfc9;
  line-height: 1.6;
}

.cta-buttons .btn {
  margin: 0.25rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .section-title {
    font-size: 2rem;
  }
  
  .mission-icon,
  .value-icon {
    font-size: 2.5rem;
  }
  
  .stat-number {
    font-size: 2.5rem;
  }
  
  .team-image img {
    width: 100px;
    height: 100px;
  }
  
  .cta-title {
    font-size: 2rem;
  }
  
  .cta-buttons .btn {
    display: block;
    width: 100%;
    margin: 0.5rem 0;
  }
}

@media (max-width: 576px) {
  .hero-section .display-4 {
    font-size: 2rem;
  }
  
  .mission-card,
  .team-card,
  .value-card {
    padding: 1.5rem 1rem;
  }
  
  .section-title {
    font-size: 1.8rem;
  }
}
</style>
