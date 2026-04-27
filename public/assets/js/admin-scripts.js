/*!
 * Admin Dashboard Scripts
 */

window.addEventListener("DOMContentLoaded", (event) => {
  // Toggle the side navigation
  const sidebarToggle = document.body.querySelector("#sidebarToggle");
  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", (event) => {
      event.preventDefault();
      document.body.classList.toggle("sb-sidenav-toggled");
      localStorage.setItem(
        "sb|sidebar-toggle",
        document.body.classList.contains("sb-sidenav-toggled")
      );
    });
  }

  // Add active class to current nav item
  const path = window.location.pathname;
  const navLinks = document.querySelectorAll(".nav-link");

  navLinks.forEach((link) => {
    const href = link.getAttribute("href");
    if (href && path.includes(href) && href !== "#") {
      link.classList.add("active");

      // If it's in a collapse, expand the collapse
      const collapse = link.closest(".collapse");
      if (collapse) {
        collapse.classList.add("show");
        const trigger = document.querySelector(
          `[data-bs-target="#${collapse.id}"]`
        );
        if (trigger) {
          trigger.classList.remove("collapsed");
          trigger.setAttribute("aria-expanded", "true");
        }
      }
    }
  });

  // Auto-dismiss alerts after 5 seconds
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)");
  alerts.forEach((alert) => {
    setTimeout(() => {
      if (typeof bootstrap !== "undefined") {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      } else {
        // Handle the case where Bootstrap is not loaded.
        // For example, you could remove the alert element directly.
        alert.remove();
      }
    }, 5000);
  });
});
