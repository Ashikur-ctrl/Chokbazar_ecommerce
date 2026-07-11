document.addEventListener('alpine:init', () => {
    Alpine.data('toastNotification', () => ({
        toasts: [],

        add(message, type = 'success', duration = 3000) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type });
            setTimeout(() => {
                this.remove(id);
            }, duration);
        },

        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },

        success(msg, duration) { this.add(msg, 'success', duration); },
        error(msg, duration) { this.add(msg, 'error', duration); },
        info(msg, duration) { this.add(msg, 'info', duration); },
    }));
});
