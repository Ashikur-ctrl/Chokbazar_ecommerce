document.addEventListener('alpine:init', () => {
    Alpine.data('quantitySelector', (config = {}) => ({
        qty: config.value || 1,
        min: config.min || 1,
        max: config.max || 999,

        get canDecrement() {
            return this.qty > this.min;
        },

        get canIncrement() {
            return this.qty < this.max;
        },

        decrement() {
            if (this.canDecrement) this.qty--;
        },

        increment() {
            if (this.canIncrement) this.qty++;
        },

        validate() {
            if (this.qty < this.min) this.qty = this.min;
            if (this.qty > this.max) this.qty = this.max;
        },
    }));
});
