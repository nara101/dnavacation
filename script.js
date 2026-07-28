const hamburger = document.querySelector(".hamburger");
const navLinks = document.querySelector(".nav-links");
const navbar = document.querySelector(".navbar");

// Toggle menu
hamburger.addEventListener("click", () => {
  navLinks.classList.toggle("show");
});

// Tambah background blur saat scroll
window.addEventListener("scroll", () => {
  if (window.scrollY > 50) {
    navbar.classList.add("scrolled");
  } else {
    navbar.classList.remove("scrolled");
  }
});

fetch('get_tours.php')
  .then(res => res.json())
  .then(data => console.log(data));

  // script.js
// efek blur transparan ketika scroll
window.addEventListener("scroll", function() {
  const header = document.querySelector(".header-blur");
  if (window.scrollY > 10) {
    header.classList.add("scrolled");
  } else {
    header.classList.remove("scrolled");
  }
});
