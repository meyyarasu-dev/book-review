@if ($rating)
    @for ($i=1;$i<=5;$i++)
        {{ $rating >= $i ? '★' : '☆' }}
    @endfor
@else
    No rating yet
@endif
