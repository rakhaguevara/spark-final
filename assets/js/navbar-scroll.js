document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.querySelector(".spark-navbar");

    window.addEventListener("scroll", () => {
        if (window.scrollY > 10) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }
    });
});
