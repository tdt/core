@extends('layouts.master')

@section('content')

    <div class="col-sm-9">
        <table class='table table-hover well'>
            @if($source_definition->has_header_row)
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
                        <td>{{ nl2br($value) }}</td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-sm-3">
        <a href="{{ $dataset_link }}.json{{ $query_string }}" class="btn btn-block btn-primary"><i class='fa fa-file-text-o'></i> View as JSON</a>
        <a href="{{ $dataset_link }}.csv{{ $query_string }}" class="btn btn-block"><i class='fa fa-table'></i> Download CSV</a>

        <br/>
        <ul class="list-group">
            <li class="list-group-item">
                <h5 class="list-group-item-heading">Description</h5>
                <p class="list-group-item-text">
                    {{ $source_definition->description }}
                </p>
            </li>
            <li class="list-group-item">
                <h5 class="list-group-item-heading">Source Type</h5>
                <p class="list-group-item-text">
                    {{ strtoupper($source_definition->getType()) }}
                </p>
            </li>
        </ul>
    </div>

    <style>
        @media only screen and (max-width: 900px) {

            table td:nth-child(6),
            table th:nth-child(6),
            table td:nth-child(7),
            table th:nth-child(7),
            table td:nth-child(9),
            table th:nth-child(9),
            table td:nth-child(10),
            table th:nth-child(10),
            table td:nth-child(8),
            table th:nth-child(8){display: none;}
        }

        @media only screen and (max-width: 640px) {

            table td:nth-child(4),
            table th:nth-child(4),
            table td:nth-child(5),
            table th:nth-child(5) {display: none;}
        }
    </style>

@stop

@section('navigation')
    @include('dataset/partials/pagination')
@stop