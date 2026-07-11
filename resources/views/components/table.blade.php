@props(['headers' => [], 'empty' => 'No data found.', 'striped' => true, 'hover' => true])

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto rounded-card border border-gray-100 bg-white shadow-card']) }}>
    <table class="w-full text-sm">
        @if(count($headers) > 0)
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/80">
                    @foreach($headers as $header)
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-gray-50">
            @if(isset($slot) && trim((string)$slot))
                {{ $slot }}
            @elseif(isset($rows) && $rows->count() > 0)
                @foreach($rows as $row)
                    <tr class="{{ $hover ? 'hover:bg-gray-50/50 transition-colors' : '' }} {{ $striped ? 'even:bg-gray-50/30' : '' }}">
                        @foreach($row as $cell)
                            <td class="px-5 py-3.5 text-gray-700">{{ $cell }}</td>
                        @endforeach
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="{{ count($headers) ?: 1 }}" class="px-5 py-12 text-center text-gray-500">
                        {{ $empty }}
                    </td>
                </tr>
            @endif
        </tbody>
        @if(isset($tfoot))
            <tfoot class="border-t border-gray-100 bg-gray-50/50">
                <tr>
                    {{ $tfoot }}
                </tr>
            </tfoot>
        @endif
    </table>
</div>
