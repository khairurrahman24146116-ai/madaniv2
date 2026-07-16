@props([
    'headers' => [],
    'rows' => [],
    'key' => 'id',
    'sortable' => false,
    'sortBy' => null,
    'sortDirection' => 'asc',
    'striped' => true,
    'hoverable' => true,
    'bordered' => true,
    'compact' => false,
    'emptyMessage' => 'Tidak ada data.',
    'emptyIcon' => 'inbox',
    'class' => '',
    'actions' => null, // column definition for actions
    'rowClass' => null, // callback function(row) => string
    'checkbox' => false,
    'selectable' => false,
    'selected' => [],
    'onSelect' => null,
])

@php
    $compactClass = $compact ? 'text-sm' : 'text-body-md';
    $stripedClass = $striped && !$compact ? 'odd:bg-surface-container-low' : '';
    $hoverClass = $hoverable ? 'hover:bg-surface-container' : '';
    $borderedClass = $bordered ? 'divide-y divide-outline-variant' : '';
    $darkBorderedClass = $bordered ? 'dark:divide-outline' : '';
@endphp

<div class="overflow-x-auto {{ $class }}">
    <table class="w-full border-collapse {{ $compactClass }} {{ $stripedClass }} {{ $hoverClass }} {{ $borderedClass }} {{ $darkBorderedClass }}">
        <thead class="bg-surface-container-low border-b border-outline-variant dark:border-outline sticky top-0">
            <tr>
                @if($checkbox || $selectable)
                    <th class="px-md py-sm text-left">
                        <input type="checkbox"
                               class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-2 focus:ring-primary/20"
                               @if($selectable && count($selected) === count($rows)) checked @endif
                               @if($onSelect) wire:model="selectAll" @endif>
                    </th>
                @endif
                
                @foreach($headers as $header)
                    @php
                        $colClass = 'px-md py-sm text-left text-label-md font-semibold text-on-surface-variant uppercase tracking-wider';
                        if (isset($header['class'])) $colClass .= ' ' . $header['class'];
                        if (isset($header['align'])) $colClass .= ' text-' . $header['align'];
                    @endphp
                    <th class="{{ $colClass }}" @if(isset($header['width'])) style="width: {{ $header['width'] }}" @endif>
                        @if($sortable && isset($header['sortable']) && $header['sortable'])
                            <button type="button"
                                    class="flex items-center gap-1 hover:text-on-surface transition-colors"
                                    @if($onSelect) wire:click="sort('{{ $header['key'] }}')" @else onclick="sortTable('{{ $header['key'] }}')" @endif>
                                {{ $header['label'] }}
                                @if($sortBy === $header['key'])
                                    <span class="material-symbols-outlined text-[16px]">
                                        {{ $sortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward' }}
                                    </span>
                                @else
                                    <span class="material-symbols-outlined text-[16px] opacity-50">unfold_more</span>
                                @endif
                            </button>
                        @else
                            {{ $header['label'] }}
                        @endif
                    </th>
                @endforeach
                
                @if($actions)
                    <th class="px-md py-sm text-left text-label-md font-semibold text-on-surface-variant uppercase tracking-wider">Aksi</th>
                @endif
            </tr>
        </thead>
        
        <tbody class="divide-y divide-outline-variant dark:divide-outline">
            @forelse($rows as $row)
                @php
                    $rowKey = $row[$key] ?? $row->{$key} ?? $loop->index;
                    $isSelected = $selectable && in_array($rowKey, $selected);
                    $trClass = $isSelected ? 'bg-primary/5 dark:bg-primary/10' : '';
                    if ($rowClass) {
                        $trClass .= ' ' . $rowClass($row);
                    }
                @endphp
                <tr class="{{ $trClass }}" @if($rowClass) wire:click="selectRow('{{ $rowKey }}')" @endif>
                    @if($checkbox || $selectable)
                        <td class="px-md py-sm">
                            <input type="checkbox"
                                   value="{{ $rowKey }}"
                                   class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-2 focus:ring-primary/20"
                                   @if($isSelected) checked @endif
                                   @if($onSelect) wire:model="selected" @endif>
                        </td>
                    @endif
                    
                    @foreach($headers as $header)
                        @php
                            $cellValue = data_get($row, $header['key'] ?? $header['label']);
                            $tdClass = 'px-md py-sm text-on-surface';
                            if (isset($header['class'])) $tdClass .= ' ' . $header['class'];
                            if (isset($header['align'])) $tdClass .= ' text-' . $header['align'];
                            if (isset($header['mono'])) $tdClass .= ' font-mono tabular-nums';
                        @endphp
                        <td class="{{ $tdClass }}">
                            @if(isset($header['format']))
                                {{ $header['format']($cellValue, $row) }}
                            @elseif(isset($header['badge']))
                                @php $badgeVariant = $header['badge']($cellValue, $row) ?? 'default'; @endphp
                                <x-badge :variant="$badgeVariant" size="sm">{{ $cellValue }}</x-badge>
                            @elseif(isset($header['component']))
                                {{ $header['component']($cellValue, $row) }}
                            @else
                                {{ $cellValue }}
                            @endif
                        </td>
                    @endforeach
                    
                    @if($actions)
                        <td class="px-md py-sm">
                            <div class="flex items-center gap-1">
                                @foreach($actions as $action)
                                    @php
                                        $visible = !isset($action['visible']) || $action['visible']($row);
                                        $disabled = isset($action['disabled']) && $action['disabled']($row);
                                    @endphp
                                    @if($visible)
                                        @if(isset($action['href']))
                                            <a href="{{ $action['href']($row) }}"
                                               class="p-1.5 rounded-lg hover:bg-surface-container transition-colors text-on-surface-variant {{ $disabled ? 'opacity-50 pointer-events-none' : '' }}"
                                               @if(isset($action['confirm'])) onclick="return confirm('{{ $action['confirm']($row) }}')" @endif
                                               @if(isset($action['title'])) title="{{ $action['title']($row) }}" @endif>
                                                <span class="material-symbols-outlined text-[18px]">{{ $action['icon'] }}</span>
                                            </a>
                                        @else
                                            <button type="button"
                                                    class="p-1.5 rounded-lg hover:bg-surface-container transition-colors text-on-surface-variant {{ $disabled ? 'opacity-50 pointer-events-none' : '' }}"
                                                    @if(isset($action['click'])) wire:click="{{ $action['click'] }}({{ $rowKey }})" @else onclick="{{ $action['onclick'] }}({{ $rowKey }})" @endif
                                                    @if(isset($action['confirm'])) onclick="return confirm('{{ $action['confirm']($row) }}')" @endif
                                                    @if(isset($action['title'])) title="{{ $action['title']($row) }}" @endif>
                                                <span class="material-symbols-outlined text-[18px]">{{ $action['icon'] }}</span>
                                            </button>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + ($checkbox || $selectable ? 1 : 0) + ($actions ? 1 : 0) }}"
                        class="px-md py-xl text-center text-on-surface-variant">
                        <div class="flex flex-col items-center gap-sm">
                            <span class="material-symbols-outlined text-4xl opacity-30">{{ $emptyIcon }}</span>
                            <p class="text-body-md">{{ $emptyMessage }}</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($sortable && !$onSelect)
@push('scripts')
<script>
    function sortTable(key) {
        const url = new URL(window.location.href);
        const currentSort = url.searchParams.get('sort');
        const currentDir = url.searchParams.get('direction');
        
        if (currentSort === key) {
            url.searchParams.set('direction', currentDir === 'asc' ? 'desc' : 'asc');
        } else {
            url.searchParams.set('sort', key);
            url.searchParams.set('direction', 'asc');
        }
        
        window.location.href = url.toString();
    }
</script>
@endpush
@endif