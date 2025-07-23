document.addEventListener("DOMContentLoaded", function () {
  console.log("Toggle script initialized!");

  // Enhanced toggle functionality - Fixed version
  document.querySelectorAll(".sdp-toggle-btn").forEach((btn) => {
    btn.addEventListener("click", function(e) {
      e.preventDefault(); // Prevent any default behavior
      
      const targetId = this.getAttribute("data-target-id");
      const content = document.getElementById(targetId);

      if (content) {
        console.log("Toggling content for:", targetId); // Debug log
        
        // Toggle the content visibility
        const isCurrentlyHidden = content.classList.contains("hidden");
        
        if (isCurrentlyHidden) {
          // Show content
          content.classList.remove("hidden");
          this.classList.add("expanded");
          console.log("Showing content"); // Debug log
        } else {
          // Hide content
          content.classList.add("hidden");
          this.classList.remove("expanded");
          console.log("Hiding content"); // Debug log
        }
      } else {
        console.error("Content element not found:", targetId); // Debug log
      }
    });
  });

  // Filter functionality for dropdown filters
  document.querySelectorAll(".sdp-subfilter").forEach((dropdown) => {
    dropdown.addEventListener("change", function () {
      const section = dropdown.closest(".sdp-section");
      const filterValue = this.value;
      const cards = section.querySelectorAll(".sdp-card");

      cards.forEach((card) => {
        const cardFilterValue = card.dataset.filter;
        if (filterValue === "all" || cardFilterValue === filterValue) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  });

  // Back to Top Button Functionality
  function initBackToTop() {
    // Create the back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.className = 'sdp-back-to-top';
    backToTopBtn.setAttribute('aria-label', 'Back to top');
    backToTopBtn.title = 'Back to Filter Options';
    
    // Add the button to the page
    document.body.appendChild(backToTopBtn);

    // Show/hide button based on scroll position
    function toggleBackToTopVisibility() {
      const heroSection = document.querySelector('.sdp-hero');
      if (!heroSection) return;

      const heroBottom = heroSection.offsetTop + heroSection.offsetHeight;
      const scrollPosition = window.pageYOffset;
      
      // Show button when user scrolls past the hero section
      if (scrollPosition > heroBottom + 100) {
        backToTopBtn.classList.add('visible');
      } else {
        backToTopBtn.classList.remove('visible');
      }
    }

    // Scroll to top when button is clicked
    backToTopBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Find the hero section with filter options
      const heroSection = document.querySelector('.sdp-hero');
      const filterSection = document.querySelector('.sdp-hero-subtitle');
      
      if (filterSection) {
        // Scroll to the "FILTER BY" section specifically
        filterSection.scrollIntoView({ 
          behavior: 'smooth', 
          block: 'center' 
        });
      } else if (heroSection) {
        // Fallback to hero section
        heroSection.scrollIntoView({ 
          behavior: 'smooth', 
          block: 'start' 
        });
      } else {
        // Ultimate fallback to top of page
        window.scrollTo({ 
          top: 0, 
          behavior: 'smooth' 
        });
      }

      // Add a subtle animation feedback
      backToTopBtn.style.transform = 'scale(0.9)';
      setTimeout(() => {
        backToTopBtn.style.transform = '';
      }, 150);
    });

    // Listen for scroll events
    window.addEventListener('scroll', toggleBackToTopVisibility);
    
    // Check initial state
    toggleBackToTopVisibility();
  }

  // Initialize back to top functionality
  initBackToTop();

  // Optional: Accordion behavior (close other sections when opening a new one)
  // This is a separate event listener to handle accordion behavior
  const enableAccordion = false; // Set to true if you want accordion behavior

  if (enableAccordion) {
    document.querySelectorAll(".sdp-toggle-btn").forEach((btn) => {
      btn.addEventListener("click", function() {
        const targetId = this.getAttribute("data-target-id");
        const currentContent = document.getElementById(targetId);
        
        // Find all other toggle contents in the same parent section
        const parentSection = this.closest(".sdp-section");
        if (parentSection && !currentContent.classList.contains("hidden")) {
          const allContents = parentSection.querySelectorAll(".sdp-toggle-content");
          const allButtons = parentSection.querySelectorAll(".sdp-toggle-btn");
          
          allContents.forEach((content) => {
            if (content !== currentContent && !content.classList.contains("hidden")) {
              content.classList.add("hidden");
            }
          });
          
          allButtons.forEach((button) => {
            if (button !== this) {
              button.classList.remove("expanded");
            }
          });
        }
      });
    });
  }

  // Debug: Log all toggle buttons and their targets
  console.log("Found toggle buttons:", document.querySelectorAll(".sdp-toggle-btn").length);
  document.querySelectorAll(".sdp-toggle-btn").forEach((btn, index) => {
    const targetId = btn.getAttribute("data-target-id");
    const target = document.getElementById(targetId);
    console.log(`Button ${index + 1}: targets "${targetId}", found: ${!!target}`);
  });
});