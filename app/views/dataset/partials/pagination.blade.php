@if(!empty($paging))
    <ul class="pagination">
        @if(!empty($prev_link))
            <li>
                <a href="{{ URL::to($dataset_link . $prev_link) }}">&larr; Previous</a>
            </li>
        @endif
        @if(!empty($next_link))
            <li>
                <a href="{{ URL::to($dataset_link . $next_link) }}">Next &rarr;</a>
            <li>
        @endif
    </ul>
@endif