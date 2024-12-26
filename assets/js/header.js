document.addEventListener("DOMContentLoaded", function () {
  // Add smooth scrolling to anchor links
  const links = document.querySelectorAll('a[href^="#"]');

  links.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      const targetElement = document.getElementById(targetId);

      window.scrollTo({
        top: targetElement.offsetTop,
        behavior: "smooth",
      });
    });
  });

  // Highlight the active link in the navbar
  const navLinks = document.querySelectorAll(".navbar-nav .nav-link");
  const currentPage = window.location.pathname.split("/").pop(); // Get current page name

  navLinks.forEach((link) => {
    if (link.getAttribute("href").includes(currentPage)) {
      link.parentElement.classList.add("active");
    }
  });
});
