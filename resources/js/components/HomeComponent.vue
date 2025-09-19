<template>
  <div>
    <!-- This component handles all the interactive functionality for the home page -->
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'HomeComponent',
  setup() {
    const headerOpacity = ref(0.95)
    
    // Smooth scrolling for navigation links
    const setupSmoothScrolling = () => {
      const anchors = document.querySelectorAll('a[href^="#"]')
      anchors.forEach(anchor => {
        anchor.addEventListener('click', handleSmoothScroll)
      })
    }
    
    const handleSmoothScroll = (e) => {
      e.preventDefault()
      const target = document.querySelector(e.target.getAttribute('href'))
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        })
      }
    }
    
    // Header background on scroll
    const handleScroll = () => {
      const header = document.querySelector('.header')
      if (window.scrollY > 100) {
        header.style.background = 'rgba(255, 255, 255, 0.98)'
        headerOpacity.value = 0.98
      } else {
        header.style.background = 'rgba(255, 255, 255, 0.95)'
        headerOpacity.value = 0.95
      }
    }
    
    // Skill cards animation
    const setupSkillCardsAnimation = () => {
      const skillCards = document.querySelectorAll('.skill-card')
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      }

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1'
            entry.target.style.transform = 'translateY(0)'
          }
        })
      }, observerOptions)

      skillCards.forEach(card => {
        card.style.opacity = '0'
        card.style.transform = 'translateY(20px)'
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease'
        observer.observe(card)
      })
    }
    
    // Button ripple effect
    const setupButtonRippleEffect = () => {
      const buttons = document.querySelectorAll('.btn')
      buttons.forEach(button => {
        button.addEventListener('click', handleButtonClick)
      })
      
      // Add CSS for ripple effect
      const style = document.createElement('style')
      style.textContent = `
        .btn {
          position: relative;
          overflow: hidden;
        }
        
        .ripple {
          position: absolute;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.6);
          transform: scale(0);
          animation: ripple-animation 0.6s linear;
          pointer-events: none;
        }
        
        @keyframes ripple-animation {
          to {
            transform: scale(4);
            opacity: 0;
          }
        }
      `
      document.head.appendChild(style)
    }
    
    const handleButtonClick = (e) => {
      const ripple = document.createElement('span')
      const rect = e.target.getBoundingClientRect()
      const size = Math.max(rect.width, rect.height)
      const x = e.clientX - rect.left - size / 2
      const y = e.clientY - rect.top - size / 2
      
      ripple.style.width = ripple.style.height = size + 'px'
      ripple.style.left = x + 'px'
      ripple.style.top = y + 'px'
      ripple.classList.add('ripple')
      
      e.target.appendChild(ripple)
      
      setTimeout(() => {
        ripple.remove()
      }, 600)
    }
    
    // Mobile menu functionality
    const setupMobileMenu = () => {
      const nav = document.querySelector('.nav')
      const navLinks = document.querySelector('.nav-links')
      
      if (window.innerWidth <= 768) {
        if (!document.querySelector('.mobile-menu-toggle')) {
          const mobileToggle = document.createElement('button')
          mobileToggle.className = 'mobile-menu-toggle'
          mobileToggle.innerHTML = '<i class="fas fa-bars"></i>'
          mobileToggle.style.cssText = `
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
            display: block;
          `
          
          mobileToggle.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex'
            navLinks.style.flexDirection = 'column'
            navLinks.style.position = 'absolute'
            navLinks.style.top = '100%'
            navLinks.style.left = '0'
            navLinks.style.right = '0'
            navLinks.style.background = 'white'
            navLinks.style.padding = '20px'
            navLinks.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)'
          })
          
          nav.appendChild(mobileToggle)
        }
      }
    }
    
    const handleResize = () => {
      setupMobileMenu()
    }
    
    // Lifecycle hooks
    onMounted(() => {
      setupSmoothScrolling()
      setupSkillCardsAnimation()
      setupButtonRippleEffect()
      setupMobileMenu()
      
      window.addEventListener('scroll', handleScroll)
      window.addEventListener('resize', handleResize)
    })
    
    onUnmounted(() => {
      window.removeEventListener('scroll', handleScroll)
      window.removeEventListener('resize', handleResize)
      
      // Clean up event listeners
      const anchors = document.querySelectorAll('a[href^="#"]')
      anchors.forEach(anchor => {
        anchor.removeEventListener('click', handleSmoothScroll)
      })
      
      const buttons = document.querySelectorAll('.btn')
      buttons.forEach(button => {
        button.removeEventListener('click', handleButtonClick)
      })
    })
    
    return {
      headerOpacity
    }
  }
}
</script>

<style scoped>
/* Component-specific styles if needed */
</style>