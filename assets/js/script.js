document.addEventListener("DOMContentLoaded", function () {
  console.log("Toggle script initialized!");

  /**
   * Toggles visibility for collapsible sections
   * Each button with the class `.sdp-toggle-btn` should have a `data-target-id`
   * that corresponds to the `id` of the element it should show/hide
   */
  document.querySelectorAll(".sdp-toggle-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const targetId = btn.getAttribute("data-target-id");
      const content = document.getElementById(targetId);
      
      if (content) {
        content.classList.toggle("hidden");
      }
    });
  });

  /**
   * Handles sub-filter dropdowns (e.g., by Area or State)
   * Filters cards inside the same `.sdp-section` based on `data-filter` attribute
   */
  document.querySelectorAll(".sdp-subfilter").forEach((dropdown) => {
    dropdown.addEventListener("change", function () {
      const section = dropdown.closest(".sdp-section");
      const filterValue = this.value;
      const cards = section.querySelectorAll(".sdp-card");

      cards.forEach((card) => {
        card.style.display =
          filterValue === "all" || card.dataset.filter === filterValue
            ? "block"
            : "none";
      });
    });
  });
});
