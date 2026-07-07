// === NAVBAR SCROLL EFFECT ===
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 50);
});

// === SCROLL ANIMATIONS ===
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, index) => {
    if (entry.isIntersecting) {
      setTimeout(() => {
        entry.target.classList.add('visible');
      }, index * 100);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.animate-in').forEach(el => observer.observe(el));

// === PRODUCT FILTER ===
function filterProducts(category, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  
  document.querySelectorAll('.product-card').forEach(card => {
    if (category === 'all' || card.dataset.category === category) {
      card.style.display = '';
      card.style.animation = 'fadeUp 0.5s ease forwards';
    } else {
      card.style.display = 'none';
    }
  });
}

// === MOBILE MENU TOGGLE ===
function toggleMobileMenu() {
  const navLinks = document.querySelector('.nav-links');
  navLinks.classList.toggle('show');
}

// === SMOOTH SCROLL FOR ANCHOR LINKS ===
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href === '#') return;
    e.preventDefault();
    const target = document.querySelector(href);
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// === ACTIVE NAV LINK HIGHLIGHTING ===
function setActiveNav() {
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-links a:not(.cta-btn)').forEach(link => {
    const linkHref = link.getAttribute('href');
    if (linkHref === currentPage || 
        (currentPage === 'index.html' && linkHref === 'index.html')) {
      link.classList.add('active-link');
    }
  });
}
setActiveNav();

// === CONTACT FORM HANDLER ===
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Show success message
    const formContainer = this.parentElement;
    const successMsg = document.createElement('div');
    successMsg.className = 'form-success';
    successMsg.innerHTML = `
      <div style="text-align:center; padding: 40px;">
        <div style="font-size: 3rem; margin-bottom: 16px;">&#10003;</div>
        <h3 style="font-family: 'Playfair Display', serif; color: var(--bark); margin-bottom: 8px;">Message Sent!</h3>
        <p style="color: var(--text-light);">Thank you for reaching out. We'll get back to you soon.</p>
      </div>
    `;
    
    this.style.display = 'none';
    formContainer.appendChild(successMsg);
    
    // Reset after 3 seconds
    setTimeout(() => {
      this.reset();
      this.style.display = '';
      successMsg.remove();
    }, 3000);
  });
}
