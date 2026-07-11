document.addEventListener('alpine:init', () => {
    Alpine.data('productGallery', (config = {}) => ({
        images: config.images || [],
        currentIndex: 0,
        lightboxOpen: false,

        get currentImage() {
            return this.images[this.currentIndex] || null;
        },

        select(index) {
            this.currentIndex = index;
        },

        prev() {
            this.currentIndex = this.currentIndex > 0 ? this.currentIndex - 1 : this.images.length - 1;
        },

        next() {
            this.currentIndex = this.currentIndex < this.images.length - 1 ? this.currentIndex + 1 : 0;
        },

        openLightbox() {
            this.lightboxOpen = true;
        },

        closeLightbox() {
            this.lightboxOpen = false;
        },

        handleKeydown(e) {
            if (e.key === 'Escape') this.closeLightbox();
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        },
    }));
});
