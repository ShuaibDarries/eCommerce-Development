// The Refillery SA — front-end interactivity
// (Cart, search and filtering are handled server-side; this file covers
// client-side enhancements with graceful degradation.)

// Auto-hide alert banners after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.alert-dismissible, .alert:not(.alert-danger)').forEach(el => {
        if (el.closest('form')) return; // never hide validation errors mid-form
        setTimeout(() => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
        }, 5000);
    });

    // Live client-side hint for card number formatting (server still validates)
    const card = document.querySelector('input[name="card_number"]');
    if (card) {
        card.addEventListener('input', () => {
            card.value = card.value.replace(/\D/g, '').slice(0, 16);
        });
    }
});
