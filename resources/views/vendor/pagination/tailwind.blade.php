@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        <div class="flex flex-wrap items-center justify-between gap-3">

            {{-- Summary --}}
            <p class="text-sm text-on-surface-variant leading-5">
                {!! __('Menampilkan') !!}
                @if ($paginator->firstItem())
                    <span class="font-medium text-on-surface">{{ $paginator->firstItem() }}</span>
                    {!! __('sampai') !!}
                    <span class="font-medium text-on-surface">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {!! __('dari') !!}
                <span class="font-medium text-on-surface">{{ $paginator->total() }}</span>
                {!! __('data') !!}
            </p>

            {{-- Page Buttons (scrollable aman di layar kecil) --}}
            <div class="max-w-full overflow-x-auto">
                <span class="inline-flex items-center gap-1">

                    {{-- Previous --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                              class="inline-flex items-center justify-center w-9 h-9 rounded-full text-on-surface-variant/40 bg-surface-container border border-outline-variant cursor-not-allowed">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                           class="inline-flex items-center justify-center w-9 h-9 rounded-full text-on-surface-variant bg-surface-container border border-outline-variant hover:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                        </a>
                    @endif

                    {{-- Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true" class="px-1 text-on-surface-variant/60 select-none">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page"
                                          class="inline-flex items-center justify-center min-w-9 h-9 px-2 rounded-full bg-primary text-on-primary font-medium shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                       class="inline-flex items-center justify-center min-w-9 h-9 px-2 rounded-full text-on-surface-variant border border-outline-variant bg-surface-container-lowest hover:bg-surface-container-high transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                           class="inline-flex items-center justify-center w-9 h-9 rounded-full text-on-surface-variant bg-surface-container border border-outline-variant hover:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                              class="inline-flex items-center justify-center w-9 h-9 rounded-full text-on-surface-variant/40 bg-surface-container border border-outline-variant cursor-not-allowed">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </span>
                    @endif

                </span>
            </div>
        </div>
    </nav>
@endif
