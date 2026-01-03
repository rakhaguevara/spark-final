document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('profileToggle');
    const dropdown = document.getElementById('profileDropdown');

    if (!toggle || !dropdown) return;

    // Toggle dropdown saat avatar diklik
    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', () => {
        dropdown.classList.remove('show');
    });

    // Supaya klik di dalam dropdown tidak nutup
    dropdown.addEventListener('click', (e) => {
        e.stopPropagation();
    });
});
