@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-tags"></i> {{ $pageTitle }}</h1>
            <p>{{ $subTitle }}</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary pull-right">Add Category</a>
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
                            <th> Имя на сайте </th>
                            <th> Имя в постере </th>
                            <th> Заведение </th>
                            <th class="text-center"> Активный </th>
                            <th style="width:100px; min-width:100px;" class="text-center text-danger"><i class="fa fa-bolt"> </i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ingredients as $ingredient)
                            @if ($ingredient->id != 1)
                                <tr>
                                    <td>{{ $ingredient->id }}</td>
                                    <td>{{ $ingredient->name }}</td>
                                    <td>{{ $ingredient->poster_name }}</td>
                                    <th> @if($ingredient->spot_id == 1) 7 небо @else Маршал @endif </th>

                                    <td class="text-center">
                                        @if ($ingredient->status == 1)
                                            <span class="badge badge-success">Да</span>
                                        @else
                                            <span class="badge badge-danger">Нет</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group" aria-label="Second group">
                                            <a href="{{ route('admin.ingredients.edit', $ingredient->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                            <a href="{{ route('admin.ingredients.delete', $ingredient->id) }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
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
