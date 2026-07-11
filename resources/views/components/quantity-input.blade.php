@props(['name' => 'quantity', 'value' => 1, 'min' => 1, 'max' => 999, 'productId' => null])

<div x-data="{
    qty: {{ $value }},
    min: {{ $min }},
    max: {{ $max }},
    get canDecrement() { return this.qty > this.min; },
    get canIncrement() { return this.qty < this.max; },
    decrement() { if (this.canDecrement) this.qty--; },
    increment() { if (this.canIncrement) this.qty++; },
    validate() {
        if (this.qty < this.min) this.qty = this.min;
        if (this.qty > this.max) this.qty = this.max;
    }
}"
     {{ $attributes->merge(['class' => 'inline-flex items-center rounded-lg border border-gray-200 bg-white']) }}>
    <button type="button" @click="decrement()"
            :disabled="!canDecrement"
            class="flex items-center justify-center w-10 h-10 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-l-lg transition-colors disabled:text-gray-300 disabled:hover:bg-transparent disabled:cursor-not-allowed">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
    </button>
    <input type="number" name="{{ $name }}" x-model="qty" @blur="validate" min="{{ $min }}" :max="max"
           class="w-14 border-0 text-center text-sm font-semibold text-gray-900 focus:ring-0 [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
    <button type="button" @click="increment()"
            :disabled="!canIncrement"
            class="flex items-center justify-center w-10 h-10 text-gray-500 hover:text-brand-600 hover:bg-brand-50 rounded-r-lg transition-colors disabled:text-gray-300 disabled:hover:bg-transparent disabled:cursor-not-allowed">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    </button>
</div>
