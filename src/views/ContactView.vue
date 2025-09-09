<template>
  <div class="contact-page">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-4">Get In Touch</h1>
            <p class="lead mb-4">
              Have questions, feedback, or need support? We're here to help! 
              Reach out to us through any of the channels below.
            </p>
          </div>
          <div class="col-lg-6 text-center">
            <img src="/api/placeholder/400/300" alt="Contact us" class="img-fluid rounded shadow">
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form & Info Section -->
    <section class="contact-section py-5">
      <div class="container">
        <div class="row g-5">
          <!-- Contact Form -->
          <div class="col-lg-8">
            <div class="contact-form-container">
              <h2 class="section-title mb-4">Send us a Message</h2>
              
              <!-- Success/Error Messages -->
              <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ successMessage }}
                <button type="button" class="btn-close" @click="successMessage = ''"></button>
              </div>
              
              <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ errorMessage }}
                <button type="button" class="btn-close" @click="errorMessage = ''"></button>
              </div>

              <form @submit.prevent="submitForm" class="contact-form">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="firstName" class="form-label fw-semibold">First Name *</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="firstName"
                      v-model="contactForm.firstName"
                      required
                      :disabled="isLoading"
                    >
                  </div>
                  <div class="col-md-6">
                    <label for="lastName" class="form-label fw-semibold">Last Name *</label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="lastName"
                      v-model="contactForm.lastName"
                      required
                      :disabled="isLoading"
                    >
                  </div>
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label fw-semibold">Email Address *</label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email"
                    v-model="contactForm.email"
                    required
                    :disabled="isLoading"
                  >
                </div>

                <div class="mb-3">
                  <label for="phone" class="form-label fw-semibold">Phone Number</label>
                  <input 
                    type="tel" 
                    class="form-control" 
                    id="phone"
                    v-model="contactForm.phone"
                    :disabled="isLoading"
                  >
                </div>

                <div class="mb-3">
                  <label for="subject" class="form-label fw-semibold">Subject *</label>
                  <select 
                    class="form-select" 
                    id="subject"
                    v-model="contactForm.subject"
                    required
                    :disabled="isLoading"
                  >
                    <option value="">Select a subject</option>
                    <option value="general">General Inquiry</option>
                    <option value="support">Customer Support</option>
                    <option value="restaurant">Restaurant Partnership</option>
                    <option value="delivery">Delivery Partner</option>
                    <option value="feedback">Feedback</option>
                    <option value="complaint">Complaint</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                <div class="mb-4">
                  <label for="message" class="form-label fw-semibold">Message *</label>
                  <textarea 
                    class="form-control" 
                    id="message"
                    rows="5"
                    v-model="contactForm.message"
                    placeholder="Please describe your inquiry in detail..."
                    required
                    :disabled="isLoading"
                  ></textarea>
                </div>

                <button 
                  type="submit" 
                  class="btn btn-primary btn-lg"
                  :disabled="isLoading"
                >
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-send me-2"></i>
                  {{ isLoading ? 'Sending...' : 'Send Message' }}
                </button>
              </form>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="col-lg-4">
            <div class="contact-info">
              <h3 class="section-title mb-4">Contact Information</h3>
              
              <div class="contact-item mb-4">
                <div class="contact-icon">
                  <i class="bi bi-geo-alt-fill text-primary"></i>
                </div>
                <div class="contact-details">
                  <h5>Address</h5>
                  <p class="text-muted mb-0">
                    123 Food Street<br>
                    Downtown District<br>
                    City, State 12345
                  </p>
                </div>
              </div>

              <div class="contact-item mb-4">
                <div class="contact-icon">
                  <i class="bi bi-telephone-fill text-primary"></i>
                </div>
                <div class="contact-details">
                  <h5>Phone</h5>
                  <p class="text-muted mb-0">
                    <a href="tel:+1234567890" class="text-decoration-none">+1 (234) 567-8900</a><br>
                    <small>Mon-Sun: 24/7 Support</small>
                  </p>
                </div>
              </div>

              <div class="contact-item mb-4">
                <div class="contact-icon">
                  <i class="bi bi-envelope-fill text-primary"></i>
                </div>
                <div class="contact-details">
                  <h5>Email</h5>
                  <p class="text-muted mb-0">
                    <a href="mailto:support@foodexpress.com" class="text-decoration-none">support@foodexpress.com</a><br>
                    <a href="mailto:partnerships@foodexpress.com" class="text-decoration-none">partnerships@foodexpress.com</a>
                  </p>
                </div>
              </div>

              <div class="contact-item mb-4">
                <div class="contact-icon">
                  <i class="bi bi-clock-fill text-primary"></i>
                </div>
                <div class="contact-details">
                  <h5>Business Hours</h5>
                  <p class="text-muted mb-0">
                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                    Saturday - Sunday: 10:00 AM - 4:00 PM<br>
                    <small>Customer Support: 24/7</small>
                  </p>
                </div>
              </div>

              <!-- Social Media -->
              <div class="social-media mt-4">
                <h5 class="mb-3">Follow Us</h5>
                <div class="social-links">
                  <a href="#" class="social-link me-3">
                    <i class="bi bi-facebook"></i>
                  </a>
                  <a href="#" class="social-link me-3">
                    <i class="bi bi-twitter"></i>
                  </a>
                  <a href="#" class="social-link me-3">
                    <i class="bi bi-instagram"></i>
                  </a>
                  <a href="#" class="social-link me-3">
                    <i class="bi bi-linkedin"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-5 bg-light">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">Frequently Asked Questions</h2>
          <p class="section-subtitle">Quick answers to common questions</p>
        </div>
        
        <div class="row">
          <div class="col-lg-8 mx-auto">
            <div class="accordion" id="faqAccordion">
              <div v-for="(faq, index) in faqs" :key="faq.id" class="accordion-item">
                <h2 class="accordion-header">
                  <button 
                    class="accordion-button" 
                    :class="{ collapsed: index !== 0 }"
                    type="button" 
                    data-bs-toggle="collapse" 
                    :data-bs-target="'#faq' + faq.id"
                    :aria-expanded="index === 0 ? 'true' : 'false'"
                  >
                    {{ faq.question }}
                  </button>
                </h2>
                <div 
                  :id="'faq' + faq.id" 
                  class="accordion-collapse collapse"
                  :class="{ show: index === 0 }"
                  data-bs-parent="#faqAccordion"
                >
                  <div class="accordion-body">
                    {{ faq.answer }}
                  </div>
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
import { ref } from 'vue'
import Footer from '@/components/Footer.vue'

const contactForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})

const isLoading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const faqs = ref([
  {
    id: 1,
    question: 'How long does delivery usually take?',
    answer: 'Our average delivery time is 25-35 minutes, depending on your location and the restaurant. You can track your order in real-time through our app.'
  },
  {
    id: 2,
    question: 'What are the delivery fees?',
    answer: 'Delivery fees typically range from $1.99 to $4.99 depending on distance and demand. We often offer free delivery promotions for orders above certain amounts.'
  },
  {
    id: 3,
    question: 'Can I cancel my order?',
    answer: 'You can cancel your order within 2 minutes of placing it. After that, please contact customer support as the restaurant may have already started preparing your food.'
  },
  {
    id: 4,
    question: 'Do you offer refunds?',
    answer: 'Yes, we offer full refunds for orders that are significantly delayed, incorrect, or if there are quality issues. Contact our support team for assistance.'
  },
  {
    id: 5,
    question: 'How can I become a delivery partner?',
    answer: 'You can sign up to become a delivery partner through our website. Requirements include being 18+, having a valid driver\'s license, and access to a vehicle or bicycle.'
  },
  {
    id: 6,
    question: 'How do restaurants join FoodExpress?',
    answer: 'Restaurants can apply for partnership through our restaurant portal. We review applications based on location, menu quality, and operational standards.'
  }
])

const submitForm = async () => {
  if (!contactForm.value.firstName || !contactForm.value.lastName || 
      !contactForm.value.email || !contactForm.value.subject || 
      !contactForm.value.message) {
    errorMessage.value = 'Please fill in all required fields.'
    return
  }

  isLoading.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Reset form
    contactForm.value = {
      firstName: '',
      lastName: '',
      email: '',
      phone: '',
      subject: '',
      message: ''
    }
    
    successMessage.value = 'Thank you for your message! We\'ll get back to you within 24 hours.'
  } catch (error) {
    errorMessage.value = 'Failed to send message. Please try again later.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.section-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.2rem;
  color: #666;
}

.contact-form-container {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.contact-form .form-control,
.contact-form .form-select {
  border: 2px solid #f0f0f0;
  border-radius: 8px;
  padding: 0.75rem 1rem;
  transition: border-color 0.3s ease;
}

.contact-form .form-control:focus,
.contact-form .form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.contact-info {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  height: fit-content;
  position: sticky;
  top: 100px;
}

.contact-item {
  display: flex;
  align-items: flex-start;
}

.contact-icon {
  width: 50px;
  height: 50px;
  background: rgba(0, 123, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  flex-shrink: 0;
}

.contact-icon i {
  font-size: 1.2rem;
}

.contact-details h5 {
  font-weight: 600;
  color: #333;
  margin-bottom: 0.5rem;
}

.contact-details a {
  color: #666;
  transition: color 0.3s ease;
}

.contact-details a:hover {
  color: #007bff;
}

.social-links {
  display: flex;
  gap: 1rem;
}

.social-link {
  width: 40px;
  height: 40px;
  background: #f8f9fa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  text-decoration: none;
  transition: all 0.3s ease;
}

.social-link:hover {
  background: #007bff;
  color: white;
  transform: translateY(-2px);
}

.accordion-item {
  border: none;
  margin-bottom: 1rem;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.accordion-button {
  background: white;
  border: none;
  font-weight: 600;
  color: #333;
  padding: 1.25rem 1.5rem;
}

.accordion-button:not(.collapsed) {
  background: #007bff;
  color: white;
}

.accordion-button:focus {
  box-shadow: none;
}

.accordion-body {
  padding: 1.5rem;
  background: #f8f9fa;
  color: #666;
  line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
  .section-title {
    font-size: 2rem;
  }
  
  .contact-form-container,
  .contact-info {
    padding: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .contact-info {
    position: static;
  }
  
  .contact-item {
    margin-bottom: 2rem;
  }
  
  .social-links {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .hero-section .display-4 {
    font-size: 2rem;
  }
  
  .contact-form-container,
  .contact-info {
    padding: 1rem;
  }
  
  .section-title {
    font-size: 1.8rem;
  }
}
</style>
