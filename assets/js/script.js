document.addEventListener("DOMContentLoaded", function () {
  const modal = document.getElementById("sdp-modal");
  const modalBody = document.getElementById("sdp-modal-body");
  const closeModal = document.querySelector(".sdp-close");

  // Modal close
  closeModal.addEventListener("click", () => {
    modal.style.display = "none";
    modalBody.innerHTML = "";
  });

  window.addEventListener("click", (event) => {
    if (event.target === modal) {
      modal.style.display = "none";
      modalBody.innerHTML = "";
    }
  });

  // Open modal with card content
  document.querySelectorAll(".sdp-card").forEach((card) => {
    card.addEventListener("click", () => {
      modalBody.innerHTML = card.innerHTML;
      modal.style.display = "block";
    });
  });

  // Filter by State and Area
  const stateSelect = document.getElementById("state-select");
  const areaSelect = document.getElementById("area-select");
  const cards = document.querySelectorAll("#state-card-grid .sdp-card");

  function filterCards() {
    const selectedState = stateSelect.value;
    const selectedArea = areaSelect.value;

    cards.forEach((card) => {
      const cardState = card.dataset.state;
      const cardArea = card.dataset.area;

      const matchState = selectedState === "all" || selectedState === cardState;
      const matchArea = selectedArea === "all" || selectedArea === cardArea;

      card.style.display = matchState && matchArea ? "block" : "none";
    });
  }

  if (stateSelect && areaSelect) {
    stateSelect.addEventListener("change", filterCards);
    areaSelect.addEventListener("change", filterCards);
  }

  // Collapsible Sections
  document.querySelectorAll(".sdp-collapsible").forEach((header) => {
    header.addEventListener("click", () => {
      const section = header.closest(".sdp-section");
      section.classList.toggle("active");
    });
  });

  // Subfilter dropdowns inside collapsible sections
  document.querySelectorAll(".sdp-subfilter").forEach((dropdown) => {
    dropdown.addEventListener("change", function () {
      const section = dropdown.closest(".sdp-section");
      const filterValue = this.value;
      const cards = section.querySelectorAll(".sdp-card");

      cards.forEach((card) => {
        if (filterValue === "all" || card.dataset.filter === filterValue) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  });
});
