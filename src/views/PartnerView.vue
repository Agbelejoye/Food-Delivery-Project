<template>
  <div class="partners-view">
    <div class="navbar-spacer"></div>

    <section class="hero-section text-light py-5">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-3">Partner With FoodExpress</h1>
            <p class="lead text-muted mb-4">
              Grow your business with our delivery network. Join thousands of restaurants increasing orders, revenue, and customer reach.
            </p>
            <div class="d-flex gap-3 flex-wrap">
              <a href="#apply" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-briefcase me-2"></i>Become a Partner
              </a>
              <a href="#benefits" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-stars me-2"></i>See Benefits
              </a>
            </div>
            <div class="hero-stats d-flex gap-4 flex-wrap mt-4">
              <div class="stat">
                <div class="h2 fw-bold text-gold">10k+</div>
                <div class="small text-muted">Active Partners</div>
              </div>
              <div class="stat">
                <div class="h2 fw-bold text-gold">40%</div>
                <div class="small text-muted">Avg Order Growth</div>
              </div>
              <div class="stat">
                <div class="h2 fw-bold text-gold">24/7</div>
                <div class="small text-muted">Business Support</div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <div class="hero-visual">
              <div class="glass-card">
                <i class="bi bi-shop display-5 text-gold"></i>
                <h5 class="mt-3 mb-1">Grow Your Restaurant</h5>
                <p class="text-muted mb-0">Reach new customers and boost repeat orders</p>
              </div>
              <div class="floating-badge badge-left">
                <i class="bi bi-graph-up-arrow me-2"></i> Marketing Boost
              </div>
              <div class="floating-badge badge-right">
                <i class="bi bi-truck me-2"></i> Reliable Delivery
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="benefits" class="benefits-section py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">Why Partner With Us</h2>
          <p class="section-subtitle">Modern tools, dedicated support, and real growth</p>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3" v-for="b in benefits" :key="b.title">
            <div class="benefit-card">
              <div class="icon-wrap">
                <i :class="b.icon"></i>
              </div>
              <h6 class="mb-2">{{ b.title }}</h6>
              <p class="text-muted mb-0">{{ b.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="how-it-works py-5 bg-light">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">How It Works</h2>
          <p class="section-subtitle">Start in days, not weeks</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-3 col-md-6" v-for="(step, i) in steps" :key="i">
            <div class="step-card h-100">
              <div class="step-index">0{{ i + 1 }}</div>
              <h6 class="mb-2">{{ step.title }}</h6>
              <p class="text-muted mb-0">{{ step.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="apply" class="apply-section py-5">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-6">
            <h3 class="mb-3">Apply to Become a Partner</h3>
            <p class="text-muted mb-4">We review applications within 2–3 business days.</p>
            <div class="apply-card p-4">
              <form @submit.prevent="submitApplication">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Business Name</label>
                    <input v-model.trim="form.businessName" type="text" class="form-control" placeholder="e.g. Bella Italia" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Contact Name</label>
                    <input v-model.trim="form.contactName" type="text" class="form-control" placeholder="Your full name" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input v-model.trim="form.email" type="email" class="form-control" placeholder="name@business.com" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input v-model.trim="form.phone" type="tel" class="form-control" placeholder="e.g. +1 555 123 4567" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input v-model.trim="form.city" type="text" class="form-control" placeholder="Business city" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Cuisine Type</label>
                    <input v-model.trim="form.cuisine" type="text" class="form-control" placeholder="e.g. Italian, Asian" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Restaurant Count</label>
                    <input v-model.number="form.restaurantCount" type="number" min="1" class="form-control" placeholder="e.g. 1" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Partnership Type</label>
                    <select v-model="form.partnershipType" class="form-select">
                      <option value="">Select one</option>
                      <option value="delivery">Delivery Only</option>
                      <option value="full">Full Platform (Ordering + Delivery)</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Logo (image, max 5MB)</label>
                    <input ref="logoInput" type="file" accept="image/*" class="form-control" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Menu PDF (max 10MB)</label>
                    <input ref="menuPdfInput" type="file" accept="application/pdf" class="form-control" />
                  </div>
                  <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="agree" v-model="form.agree" />
                      <label class="form-check-label" for="agree">
                        I agree to the terms and consent to be contacted
                      </label>
                    </div>
                  </div>
                </div>
                <div class="d-flex gap-3 mt-4">
                  <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-send me-2"></i>Submit Application
                  </button>
                  <button type="button" class="btn btn-outline-primary" @click="resetForm">
                    Reset
                  </button>
                </div>
              </form>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="value-card p-4 mb-4">
              <h5 class="mb-3">What You Get</h5>
              <ul class="mb-0 value-list">
                <li><i class="bi bi-check-circle-fill text-gold me-2"></i>Featured listing and marketing promotions</li>
                <li><i class="bi bi-check-circle-fill text-gold me-2"></i>Delivery analytics and insights</li>
                <li><i class="bi bi-check-circle-fill text-gold me-2"></i>Dedicated partner support</li>
                <li><i class="bi bi-check-circle-fill text-gold me-2"></i>Fast settlements and transparent fees</li>
              </ul>
            </div>
            <div class="value-card p-4">
              <h5 class="mb-3">Questions?</h5>
              <p class="text-muted mb-3">Our team is happy to help you evaluate the fit and answer any questions.</p>
              <router-link to="/contact#contact-form" class="btn btn-outline-primary">
                <i class="bi bi-chat-dots me-2"></i>Contact Us
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Testimonials / Case Studies -->
    <section class="testimonials-section py-5" v-if="testimonials.length">
      <div class="container">
        <div class="text-center mb-4">
          <h2 class="section-title">Partner Testimonials</h2>
          <p class="section-subtitle">Real success stories from businesses like yours</p>
        </div>
        <div id="partnerTestimonials" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <div 
              v-for="(chunk, idx) in testimonialChunks" 
              :key="'chunk-' + idx" 
              class="carousel-item"
              :class="{ active: idx === 0 }"
            >
              <div class="row g-4">
                <div class="col-md-4" v-for="t in chunk" :key="t.id">
                  <div class="testimonial-card h-100">
                    <div class="d-flex align-items-center mb-3">
                      <img v-if="t.avatar_url" :src="t.avatar_url" class="rounded-circle me-3" alt="avatar" width="48" height="48" />
                      <div>
                        <div class="testimonial-author">{{ t.author }}</div>
                        <div class="text-muted small">{{ t.business_name }}</div>
                      </div>
                    </div>
                    <div class="testimonial-quote mb-3">“{{ t.quote }}”</div>
                    <div class="text-gold">
                      <i v-for="n in 5" :key="n" class="bi" :class="n <= t.rating ? 'bi-star-fill' : 'bi-star'" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#partnerTestimonials" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#partnerTestimonials" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const benefits = ref([
  { icon: 'bi bi-graph-up-arrow text-gold', title: 'Increase Orders', text: 'Boost sales with new and returning customers.' },
  { icon: 'bi bi-bullseye text-gold', title: 'Marketing Reach', text: 'Get featured in campaigns and seasonal promos.' },
  { icon: 'bi bi-speedometer2 text-gold', title: 'Fast Onboarding', text: 'Set up quickly with our partner success team.' },
  { icon: 'bi bi-shield-check text-gold', title: 'Trusted Delivery', text: 'Reliable logistics and live tracking.' },
])

const steps = ref([
  { title: 'Apply', text: 'Tell us about your business and goals.' },
  { title: 'Onboard', text: 'We verify, set up menu and pricing.' },
  { title: 'Launch', text: 'Go live and start receiving orders.' },
  { title: 'Grow', text: 'Access insights and marketing support.' },
])

const form = ref({
  businessName: '',
  contactName: '',
  email: '',
  phone: '',
  city: '',
  cuisine: '',
  restaurantCount: null,
  partnershipType: '',
  agree: false,
})

const showError = (msg) => window.Swal?.fire({ icon: 'error', title: 'Invalid form', text: msg })
const showSuccess = (title, html) => window.Swal?.fire({ icon: 'success', title, html })

const resetForm = () => {
  form.value = {
    businessName: '',
    contactName: '',
    email: '',
    phone: '',
    city: '',
    cuisine: '',
    restaurantCount: null,
    partnershipType: '',
    agree: false,
  }
  if (logoInput.value) logoInput.value.value = ''
  if (menuPdfInput.value) menuPdfInput.value.value = ''
}

// API base
const API_BASE = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

// Upload inputs
const logoInput = ref(null)
const menuPdfInput = ref(null)

// Testimonials
const testimonials = ref([])

const testimonialChunks = computed(() => {
  const size = 3
  const arr = []
  for (let i = 0; i < testimonials.value.length; i += size) {
    arr.push(testimonials.value.slice(i, i + size))
  }
  return arr
})

const submitApplication = async () => {
  const f = form.value
  if (!f.businessName) return showError('Business name is required.')
  if (!f.contactName) return showError('Contact name is required.')
  if (!f.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email)) return showError('Enter a valid email address.')
  if (!f.phone) return showError('Phone is required.')
  if (!f.city) return showError('City is required.')
  if (!f.cuisine) return showError('Cuisine type is required.')
  if (!f.restaurantCount || f.restaurantCount < 1) return showError('Enter restaurant count (min 1).')
  if (!f.partnershipType) return showError('Select a partnership type.')
  if (!f.agree) return showError('You must agree to continue.')

  // Optional: upload assets first
  let logoUrl = null
  let menuPdfUrl = null
  try {
    const fd = new FormData()
    if (logoInput.value && logoInput.value.files[0]) {
      fd.append('logo', logoInput.value.files[0])
    }
    if (menuPdfInput.value && menuPdfInput.value.files[0]) {
      fd.append('menu_pdf', menuPdfInput.value.files[0])
    }
    if ([...fd.keys()].length > 0) {
      const up = await fetch(`${API_BASE}/api/partners/assets`, { method: 'POST', body: fd })
      const upData = await up.json()
      if (!up.ok || !upData.success) throw new Error(upData.message || 'Upload failed')
      logoUrl = upData.data.logoUrl || null
      menuPdfUrl = upData.data.menuPdfUrl || null
    }
  } catch (e) {
    return showError(e.message || 'Failed to upload assets')
  }

  // Submit application
  try {
    const res = await fetch(`${API_BASE}/api/partners/apply`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...f, logoUrl, menuPdfUrl })
    })
    const data = await res.json()
    if (!res.ok || !data.success) throw new Error(data.message || 'Submission failed')

    await showSuccess('Application Submitted', `
      <p>Thanks, <strong>${f.contactName}</strong>! Your application for <strong>${f.businessName}</strong> has been received.</p>
      <p class="text-muted">We will contact you at <strong>${f.email}</strong> within 2–3 business days.</p>
    `)
    resetForm()
  } catch (e) {
    return showError(e.message || 'Failed to submit application')
  }
}

onMounted(async () => {
  try {
    const r = await fetch(`${API_BASE}/api/partners/testimonials`)
    const j = await r.json()
    if (j?.success) testimonials.value = j.data.items || []
  } catch {}
})
</script>

<style scoped>
.navbar-spacer { height: 80px; }

.hero-section {
  background: linear-gradient(135deg, var(--black-900) 0%, var(--black-800) 100%);
}

.text-gold { color: var(--gold-500) !important; }

.hero-visual {
  position: relative;
  min-height: 260px;
}

.glass-card {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(201,162,39,0.18);
  border-radius: 16px;
  padding: 2rem;
  backdrop-filter: blur(6px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.35);
}

.floating-badge {
  position: absolute;
  top: 10px;
  padding: 0.5rem 0.75rem;
  border-radius: 999px;
  background: rgba(201,162,39,0.12);
  border: 1px solid rgba(201,162,39,0.25);
  color: var(--text-on-dark);
  animation: float 6s ease-in-out infinite;
}
.badge-left { left: -10px; animation-delay: 0.2s; }
.badge-right { right: -10px; top: auto; bottom: 10px; animation-delay: 0.8s; }

@keyframes float { 50% { transform: translateY(-10px) } }

.section-title {
  font-size: 2.25rem;
  font-weight: 700;
  color: var(--text-on-dark);
}
.section-subtitle { color: #cfcfc9; }

.benefit-card {
  background: var(--black-800);
  border: 1px solid rgba(201,162,39,0.12);
  border-radius: 16px;
  padding: 1.25rem;
  box-shadow: 0 10px 30px rgba(0,0,0,0.35);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.benefit-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.45);
}
.icon-wrap { font-size: 2rem; color: var(--gold-500); margin-bottom: 0.75rem; }

.how-it-works { background: var(--black-800); }

.step-card {
  background: var(--black-800);
  border: 1px solid rgba(201,162,39,0.12);
  border-radius: 14px;
  padding: 1.25rem;
  box-shadow: 0 6px 18px rgba(0,0,0,0.35);
}
.step-index { color: var(--gold-500); font-weight: 700; margin-bottom: 0.5rem; }

.apply-card, .value-card {
  background: var(--black-800);
  border: 1px solid rgba(201,162,39,0.12);
  border-radius: 16px;
  box-shadow: 0 12px 24px rgba(0,0,0,0.4);
}

.value-list { list-style: none; padding-left: 0; }
.value-list li { margin-bottom: 0.5rem; }

/* Testimonials Carousel */
.testimonials-section { background: var(--black-800); }
.testimonial-card {
  background: var(--black-800);
  border: 1px solid rgba(201,162,39,0.12);
  border-radius: 16px;
  box-shadow: 0 10px 24px rgba(0,0,0,0.4);
  padding: 1.25rem;
}
.testimonial-quote { color: #cfcfc9; font-style: italic; }
.testimonial-author { color: var(--text-on-dark); font-weight: 600; }
</style>
