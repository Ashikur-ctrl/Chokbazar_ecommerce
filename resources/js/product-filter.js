/**
 * Product Filter — AJAX-based filtering with Alpine.js rendering
 *
 * Instead of generating HTML strings, this fetches JSON from the API
 * and dispatches a custom event with product data for Alpine to render.
 */
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('product-filter-form');
    const gridWrapper = document.getElementById('product-grid');

    if (!form || !gridWrapper) return;

    // Global toast utility (used by wishlist/compare buttons)
    window.showToast = function (message, success = true, timeout = 3000) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.className = `fixed right-4 bottom-4 z-50 rounded-md px-4 py-2 text-sm font-bold shadow-elevated animate-slide-down ${
            success ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'
        }`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.3s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, timeout);
    };

    // Helper: render a single product card HTML (used by Alpine x-html)
    window.renderProductCard = function (product) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const imageHtml = product.image
            ? `<img data-src="/storage/${product.image}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E" alt="${escapeHtml(product.name)}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">`
            : `<div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-secondary-50"><span class="text-5xl font-black text-brand-200">${escapeHtml(product.name.substring(0, 2).toUpperCase())}</span></div>`;

        const discountBadge = product.is_on_sale
            ? `<span class="absolute left-3 top-3 inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700 shadow-sm">${product.discount_percentage}% OFF</span>`
            : '';

        const stockBadge = product.stock > 0 && product.stock <= 5
            ? `<span class="absolute right-3 top-3 inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700 shadow-sm">Only ${product.stock} left</span>`
            : '';

        const soldOutOverlay = product.stock <= 0
            ? `<div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] flex items-center justify-center"><span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700">Sold Out</span></div>`
            : '';

        const starsHtml = Array.from({ length: 5 }, (_, i) => {
            const filled = i < Math.round(product.average_rating || 0);
            return `<svg class="w-3.5 h-3.5 ${filled ? 'text-amber-400' : 'text-gray-200'}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
        }).join('');

        return `
            <article class="group rounded-card border border-gray-100 bg-white shadow-card hover:shadow-card-hover transition-all duration-300 hover:-translate-y-0.5 overflow-hidden">
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                    <a href="/shop/product/${product.slug}">
                        ${imageHtml}
                    </a>
                    ${discountBadge}
                    ${stockBadge}
                    ${soldOutOverlay}
                </div>
                <div class="p-4 space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-secondary-600">${escapeHtml(product.category || 'Uncategorized')}</p>
                    <h3 class="font-semibold text-gray-900 line-clamp-2 min-h-[2.5rem]">
                        <a href="/shop/product/${product.slug}" class="hover:text-brand-600 transition-colors">${escapeHtml(product.name)}</a>
                    </h3>
                    <div class="flex items-center gap-1 text-sm">
                        <div class="flex items-center">${starsHtml}</div>
                        <span class="text-xs text-gray-400">(${product.order_items_count || 0})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-black text-brand-600">৳${product.price}</span>
                        ${product.is_on_sale ? `<span class="text-sm text-gray-400 line-through">৳${parseFloat(product.sale_price || product.price).toFixed(2)}</span>` : ''}
                    </div>
                    <div class="flex gap-2 pt-1">
                        <form method="POST" action="/cart/add" class="flex-1">
                            <input type="hidden" name="_token" value="${csrf}">
                            <input type="hidden" name="product_id" value="${product.id}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" ${product.stock <= 0 ? 'disabled' : ''}
                                    class="w-full rounded-lg bg-gradient-to-r from-brand-600 to-brand-700 px-3 py-2.5 text-sm font-bold text-white hover:from-brand-700 hover:to-brand-800 disabled:from-gray-300 disabled:to-gray-300 transition-all duration-200 active:scale-[0.98]">
                                ${product.stock > 0 ? 'Add to Cart' : 'Sold Out'}
                            </button>
                        </form>
                        <button data-action="wishlist" data-id="${product.id}"
                                class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 text-gray-400 hover:text-rose-500 hover:border-rose-200 transition-colors shrink-0">
                            <svg class="w-5 h-5" fill="${product.is_in_wishlist ? 'currentColor' : 'none'}" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </article>
        `;
    };

    window.escapeHtml = function (text) {
        return String(text).replace(/[&<>"']/g, function (s) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[s];
        });
    };

    // Submit handler
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const params = new URLSearchParams(new FormData(form));
        const url = '/api/products/filter?' + params.toString();

        // Skeleton loading state
        gridWrapper.innerHTML = `
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                ${Array.from({ length: 8 }, () => `
                    <div class="rounded-card border border-gray-100 bg-white shadow-card overflow-hidden">
                        <div class="skeleton-thumb"></div>
                        <div class="p-4 space-y-3">
                            <div class="skeleton h-3 w-1/3"></div>
                            <div class="skeleton h-4 w-5/6"></div>
                            <div class="skeleton h-3 w-2/3"></div>
                            <div class="skeleton h-10 w-full rounded-lg"></div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Network error');
            const data = await res.json();

            const products = data.products || [];
            const meta = data.meta || {};

            if (products.length === 0) {
                gridWrapper.innerHTML = `
                    <div class="col-span-full mt-8">
                        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <h3 class="mt-4 text-lg font-semibold text-gray-900">কোনো পণ্য পাওয়া যায়নি</h3>
                            <p class="mt-2 text-sm text-gray-500">অন্য search বা category চেষ্টা করুন।</p>
                            <a href="/shop" class="mt-6 inline-flex items-center rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-700">Reset filters</a>
                        </div>
                    </div>
                `;
                return;
            }

            // Render product cards
            const cardsHtml = products.map(product => window.renderProductCard(product)).join('');

            let paginationHtml = '';
            if (meta.last_page > 1) {
                paginationHtml = `<div class="mt-8 flex items-center justify-center gap-2">
                    ${meta.current_page > 1 ? `<a href="?${params.toString()}&page=${meta.current_page - 1}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50 data-filter-page">Previous</a>` : ''}
                    <span class="text-sm text-gray-500">Page ${meta.current_page} of ${meta.last_page}</span>
                    ${meta.current_page < meta.last_page ? `<a href="?${params.toString()}&page=${meta.current_page + 1}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50 data-filter-page">Next</a>` : ''}
                </div>`;
            }

            gridWrapper.innerHTML = `
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" data-animate>
                    ${cardsHtml}
                </div>
                ${paginationHtml}
            `;

            // Dispatch event for re-observing lazy images and animations
            document.dispatchEvent(new CustomEvent('products-loaded'));

        } catch (err) {
            console.error(err);
            gridWrapper.innerHTML = `<div class="col-span-full text-center py-8 text-red-600">Could not load products. <button onclick="location.reload()" class="underline">Try again</button></div>`;
        }
    });

    // Delegated click: wishlist & compare
    gridWrapper.addEventListener('click', async function (e) {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;

        const action = btn.getAttribute('data-action');
        const id = btn.getAttribute('data-id');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (action === 'compare') {
            try {
                const res = await fetch(`/compare/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({}),
                });
                if (!res.ok) throw new Error('Failed');
                const data = await res.json();
                window.showToast(data.message || 'Added to comparison.', true);
            } catch (err) {
                window.showToast('Could not add to compare.', false);
            }
        }

        if (action === 'wishlist') {
            try {
                const res = await fetch(`/wishlist/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({}),
                });

                if (res.status === 401 || res.status === 403) {
                    window.location.href = '/login';
                    return;
                }

                const data = await res.json();
                window.showToast(data.message || 'Wishlist updated.', true);
            } catch (err) {
                window.showToast('Could not update wishlist.', false);
            }
        }
    });
});
