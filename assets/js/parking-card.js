// DETAILS BUTTON
document.addEventListener('click', e => {
    const btn = e.target.closest('.btn-card-details');
    if (!btn) return;

    const id = btn.dataset.cardId;
    openDetailPopup(id);
});

// ACTIVE CARD + SCROLL
function setActiveCard(id) {
    document.querySelectorAll('.parking-card')
        .forEach(c => c.classList.remove('active'));

    const card = document.querySelector(`.parking-card[data-id="${id}"]`);
    if (card) {
        card.classList.add('active');
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
