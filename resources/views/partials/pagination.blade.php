@if ($paginator->hasPages())
    <div class="pagination-wrapper">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-link disabled"><i class="fas fa-chevron-right"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev"><i class="fas fa-chevron-right"></i></a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="page-dots">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="page-link active">{{ sprintf('%02d', $page) }}</span>
                    @else
                        <a href="{{ $url }}" class="page-link">{{ sprintf('%02d', $page) }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next"><i class="fas fa-chevron-left"></i></a>
        @else
            <span class="page-link disabled"><i class="fas fa-chevron-left"></i></span>
        @endif
    </div>
@endif
