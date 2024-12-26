// Smooth Scrolling Effect
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelector(this.getAttribute("href")).scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  });
});

// Scroll Animations (Animate elements as they come into view)
window.addEventListener("scroll", function () {
  const elements = document.querySelectorAll(".animate-on-scroll");
  elements.forEach((element) => {
    const position = element.getBoundingClientRect();
    if (position.top >= 0 && position.bottom <= window.innerHeight) {
      element.classList.add("fade-in");
    } else {
      element.classList.remove("fade-in");
    }
  });
});

// Button Hover Effect (For better interaction on the "Start Shopping" button)
const btn = document.querySelector(".btn-primary");
btn.addEventListener("mouseover", function () {
  btn.style.transform = "scale(1.05)";
  btn.style.transition = "transform 0.2s ease";
});
btn.addEventListener("mouseout", function () {
  btn.style.transform = "scale(1)";
});

// Optional: Modal Popup Example (Show modal when clicking on an image or a section)
const image = document.querySelector(".profile-img");
image.addEventListener("click", function () {
  const modal = document.createElement("div");
  modal.classList.add("modal");
  modal.innerHTML = `
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <img src="${image.src}" alt="Profile Image" class="modal-img">
            <p>Here is a closer look at my image. Enjoy browsing!</p>
        </div>
    `;
  document.body.appendChild(modal);

  // Close modal on click
  const closeBtn = modal.querySelector(".close-btn");
  closeBtn.addEventListener("click", function () {
    modal.remove();
  });
});
