// ===== REPLACE THE ENTIRE assets/js/script.js file with this: =====

document.addEventListener("DOMContentLoaded", function () {
  console.log("Enhanced toggle script initialized!");

  // Enhanced toggle functionality with better handling
  document.querySelectorAll(".sdp-toggle-btn").forEach((btn) => {
    btn.addEventListener("click", function(e) {
      e.preventDefault();
      e.stopPropagation(); // Prevent event bubbling
      
      const targetId = this.getAttribute("data-target-id");
      const content = document.getElementById(targetId);

      if (content) {
        console.log("Toggling content for:", targetId);
        
        const isCurrentlyHidden = content.classList.contains("hidden");
        
        if (isCurrentlyHidden) {
          // Show content
          content.classList.remove("hidden");
          this.classList.add("expanded");
          
          // Force reflow to ensure proper display
          content.offsetHeight;
          
          console.log("Showing content for:", targetId);
        } else {
          // Hide content
          content.classList.add("hidden");
          this.classList.remove("expanded");
          console.log("Hiding content for:", targetId);
        }
      } else {
        console.error("Content element not found:", targetId);
      }
    });
  });

  // Enhanced filter functionality
  document.querySelectorAll(".sdp-subfilter").forEach((dropdown) => {
    dropdown.addEventListener("change", function () {
      const section = dropdown.closest(".sdp-section");
      const filterValue = this.value;
      const cards = section.querySelectorAll(".sdp-card");

      console.log("Filtering by:", filterValue);

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

  // Smooth scroll for hero navigation links
  document.querySelectorAll('.sdp-hero-links a[href^="#"]').forEach((link) => {
    link.addEventListener("click", function(e) {
      e.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      const targetElement = document.getElementById(targetId);
      
      if (targetElement) {
        targetElement.scrollIntoView({
          behavior: "smooth",
          block: "start"
        });
      }
    });
  });

  // Debug information
  console.log("Found toggle buttons:", document.querySelectorAll(".sdp-toggle-btn").length);
  console.log("Found toggle contents:", document.querySelectorAll(".sdp-toggle-content").length);
  
  // Validate all toggle button/content pairs
  document.querySelectorAll(".sdp-toggle-btn").forEach((btn, index) => {
    const targetId = btn.getAttribute("data-target-id");
    const target = document.getElementById(targetId);
    console.log(`Button ${index + 1}: "${btn.textContent.trim()}" -> "${targetId}" (Found: ${!!target})`);
    
    if (!target) {
      console.warn(`Missing target element for button: ${targetId}`);
    }
  });

  // Handle any orphaned content (content without corresponding buttons)
  document.querySelectorAll(".sdp-toggle-content").forEach((content) => {
    const contentId = content.id;
    const correspondingButton = document.querySelector(`[data-target-id="${contentId}"]`);
    
    if (!correspondingButton) {
      console.warn(`Found orphaned content element: ${contentId}`);
    }
  });
});