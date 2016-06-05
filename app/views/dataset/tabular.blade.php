@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <div class='scroll-horizontal'>
            <table class='table table-hover well'>
                @if(@$source_definition['has_header_row'] || @$source_definition['query_type'] == 'select')
                <thead>
                    <?php
                        $first_row = array_shift($body);
                        array_unshift($body, $first_row);
                    ?>
                    <tr>
                        @foreach($first_row as $key => $value)
                            <th>{{ $key }}</th>
                        @endforeach
                    </tr>
                </thead>
                @endif
                <tbody>
                    @foreach($body as $row)
                    <tr>
                        @foreach($row as $key => $value)
                            @if (substr($value, 0, 4) == 'http' && parse_url($value))
                                <td><a href="{{ $value }}">{{ $value }}</a></td>
                            @else
                                <td>{{ nl2br($value) }}</td>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-sm-3">
        @include('dataset/partials/details')
    </div>

@stop

@section('navigation')
    @include('dataset/partials/pagination')
@stop