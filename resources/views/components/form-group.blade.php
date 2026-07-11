@props(['label' => null, 'name' => null, 'required' => false, 'help' => null, 'error' => null])

<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif
    <div class="{{ $label ? 'mt-1' : '' }}">
        {{ $slot }}
    </div>
    @if($help)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>
