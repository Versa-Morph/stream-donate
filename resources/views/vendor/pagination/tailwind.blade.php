@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display:flex;align-items:center;justify-content:between;gap:16px;margin-top:16px;flex-wrap:wrap;">

    <div style="font-size:13px;color:#606078;">
        @if ($paginator->firstItem())
            Menampilkan <strong style="color:#a0a0b4;">{{ $paginator->firstItem() }}</strong>
            &ndash;
            <strong style="color:#a0a0b4;">{{ $paginator->lastItem() }}</strong>
            dari <strong style="color:#a0a0b4;">{{ $paginator->total() }}</strong> hasil
        @else
            {{ $paginator->count() }} hasil
        @endif
    </div>

    <div style="display:flex;gap:4px;align-items:center;margin-left:auto;">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span style="display:inline-flex;align-items:center;padding:6px 12px;font-size:13px;background:#141419;border:1px solid rgba(255,255,255,.07);border-radius:8px;color:#606078;cursor:not-allowed;">&#8592;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               style="display:inline-flex;align-items:center;padding:6px 12px;font-size:13px;background:#141419;border:1px solid rgba(255,255,255,.07);border-radius:8px;color:#a0a0b4;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.borderColor='rgba(124,108,252,.5)';this.style.color='#f1f1f6'"
               onmouseout="this.style.borderColor='rgba(255,255,255,.07)';this.style.color='#a0a0b4'"
               aria-label="{{ __('pagination.previous') }}">&#8592;</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="display:inline-flex;align-items:center;padding:6px 10px;font-size:13px;color:#606078;">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page"
                              style="display:inline-flex;align-items:center;padding:6px 12px;font-size:13px;font-weight:600;background:linear-gradient(135deg,#7c6cfc,#6356e8);border:1px solid transparent;border-radius:8px;color:#fff;cursor:default;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           style="display:inline-flex;align-items:center;padding:6px 12px;font-size:13px;background:#141419;border:1px solid rgba(255,255,255,.07);border-radius:8px;color:#a0a0b4;text-decoration:none;transition:all .15s;"
                           onmouseover="this.style.borderColor='rgba(124,108,252,.5)';this.style.color='#f1f1f6'"
                           onmouseout="this.style.borderColor='rgba(255,255,255,.07)';this.style.color='#a0a0b4'"
                           aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               style="display:inline-flex;align-items:center;padding:6px 12px;font-size:13px;background:#141419;border:1px solid rgba(255,255,255,.07);border-radius:8px;color:#a0a0b4;text-decoration:none;transition:all .15s;"
               onmouseover="this.style.borderColor='rgba(124,108,252,.5)';this.style.color='#f1f1f6'"
               onmouseout="this.style.borderColor='rgba(255,255,255,.07)';this.style.color='#a0a0b4'"
               aria-label="{{ __('pagination.next') }}">&#8594;</a>
        @else
            <span style="display:inline-flex;align-items:center;padding:6px 12px;font-size:13px;background:#141419;border:1px solid rgba(255,255,255,.07);border-radius:8px;color:#606078;cursor:not-allowed;">&#8594;</span>
        @endif

    </div>
</nav>
@endif
