<ul class="list-group">
    <li class="list-group-item">
        <h5 class="list-group-item-heading">Formats</h5>
        <div class="btn-group formats">
            <?php $i = 0; ?>
            @foreach($formats as $format => $extension)
                <a href="{{ $dataset_link }}.{{ $extension }}{{ $query_string }}" class="btn">
                    @if($i == 0)
                        @if($extension == 'map')
                            <i class='fa fa-expand'></i>
                        @else
                            <i class='fa fa-file-text-o'></i>
                        @endif
                    @endif
                    {{ $format }}
                </a>
                <?php $i++; ?>
            @endforeach
        </div>
    </li>
    @if(!empty($source_definition['description']))
        <li class="list-group-item">
            <h5 class="list-group-item-heading">Description</h5>
            <p class="list-group-item-text">
                {{ $source_definition['description'] }}
            </p>
        </li>
    @endif
    <li class="list-group-item">
        <h5 class="list-group-item-heading">Source Type</h5>
        <p class="list-group-item-text">
            {{ strtoupper($source_definition['type']) }}
        </p>
    </li>
</ul>