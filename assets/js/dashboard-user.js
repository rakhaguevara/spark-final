document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("profileToggle");
    const dropdown = document.getElementById("profileDropdown");

    toggle.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.classList.toggle("show");
    });

    document.addEventListener("click", () => {
        dropdown.classList.remove("show");
    });
});
