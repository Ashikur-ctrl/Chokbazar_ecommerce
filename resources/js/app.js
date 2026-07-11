import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Register Alpine components
import './components/product-gallery';
import './components/quantity-selector';
import './components/toast-notification';

Alpine.start();

// Product filter behavior (AJAX + Alpine rendering)
import './product-filter';

// --- Global: Lazy Image IntersectionObserver ---
document.addEventListener('DOMContentLoaded', () => {
    if (!('IntersectionObserver' in window)) return;

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.dataset.src;
                if (src) {
                    img.src = src;
                    img.classList.remove('opacity-0');
                    img.classList.add('opacity-100');
                    img.addEventListener('load', () => {
                        img.parentElement?.classList.add('image-loaded');
                    });
                    img.addEventListener('error', () => {
                        img.classList.remove('opacity-0');
                        img.src = ''; // remove broken src
                    });
                }
                observer.unobserve(img);
            }
        });
    }, { rootMargin: '200px 0px' });

    document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));

    // --- Global: Animate on scroll ---
    const animateObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in-up');
                animateObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.05 });

    document.querySelectorAll('[data-animate]').forEach(el => animateObserver.observe(el));

    // --- Global: Live cart count ---
    const cartCount = document.getElementById('cart-count');
    const cartCountMobile = document.getElementById('cart-count-mobile');
    if (cartCount || cartCountMobile) {
        fetch('/cart/summary')
            .then(r => r.json())
            .then(data => {
                const count = data.items_count;
                if (count > 0) {
                    [cartCount, cartCountMobile].forEach(el => {
                        if (el) { el.textContent = count; el.classList.remove('hidden'); }
                    });
                }
            })
            .catch(() => {});
    }

    // Re-observe after AJAX content loads
    document.addEventListener('products-loaded', () => {
        document.querySelectorAll('img[data-src]').forEach(img => imageObserver.observe(img));
        document.querySelectorAll('[data-animate]').forEach(el => animateObserver.observe(el));
    });
});
