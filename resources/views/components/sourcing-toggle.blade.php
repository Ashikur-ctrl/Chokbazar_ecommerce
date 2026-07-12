@props(['mode' => 'local'])

<div class="flex items-center gap-1 bg-white rounded-lg border border-gray-200 p-0.5 shadow-sm">
    <button
        type="button"
        class="px-3 py-1.5 text-xs sm:text-sm font-medium rounded-md transition-all duration-200 whitespace-nowrap
            {{ $mode === 'local' ? 'bg-brand-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
        onclick="setSourcingMode('local')">
        🇧🇩 Local
    </button>
    <button
        type="button"
        class="px-3 py-1.5 text-xs sm:text-sm font-medium rounded-md transition-all duration-200 whitespace-nowrap
            {{ $mode === 'import' ? 'bg-brand-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
        onclick="setSourcingMode('import')">
        🇨🇳 Import
    </button>
</div>

<script>
function setSourcingMode(mode) {
    var btn = event.target;
    btn.disabled = true;
    btn.textContent = '...';

    fetch('/sourcing/mode/' + mode, { method: 'GET', headers: { 'Accept': 'application/json' } })
        .then(function() {
            localStorage.setItem('sourcing_mode', mode);
            window.location.reload();
        });
}

(function() {
    var stored = localStorage.getItem('sourcing_mode');
    if (stored && stored !== '{{ $mode }}') {
        fetch('/sourcing/mode/' + stored, { method: 'GET', headers: { 'Accept': 'application/json' } });
    }
})();
</script>
