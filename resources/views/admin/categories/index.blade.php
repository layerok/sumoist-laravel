@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-tags"></i> {{ $pageTitle }}</h1>
            <p>{{ $subTitle }}</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary pull-right">Добавить Категорию</a>
    </div>
    @include('admin.partials.flash')
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <table class="table table-hover table-bordered" id="sampleTable">
                        <thead>
                        <tr>
                            <th> # </th>
                            <th> Имя </th>
                            <th> Ссылка </th>
                            <th class="text-center"> Порядок </th>
                            <th class="text-center"> Статус </th>
                            <th style="width:100px; min-width:100px;" class="text-center text-danger"><i class="fa fa-bolt"> </i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $category)
                            @if ($category->id != 1)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td class="text-center">
                                        {{ $category->sort_order }}
                                    </td>

                                    <td>{{ $category->slug }}</td>
                                    <td class="text-center">
                                        <div class="toggle-flip">
                                            <label>
                                                <input type="checkbox" name="hidden" {{ $category->hidden == 0 ? 'checked' : '' }} data-table="{{ $category->getTable() }}" data-id="{{ $category->id }}">
                                                <span class="flip-indecator" data-toggle-on="Вкл" data-toggle-off="Выкл">

                                            </span>
                                            </label>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Second group">
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                            <a href="{{ route('admin.categories.delete', $category->id) }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush
