@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')

    <div class="app-title">
        <div>
            <h1><i class="fa fa-tags"></i> {{ $pageTitle }}</h1>
            <p>{{ $subTitle }}</p>
        </div>
        <div>
           {{-- <a href="{{ route('admin.products.sync-photos') }}" class="btn btn-primary pull-right mb-2">Синхронизировать фотографии</a>--}}
            <a href="{{ route('admin.products.sync-prices') }}" class="btn btn-primary pull-right mb-2">Синхронизировать цены и веса</a>
            <a href="{{ route('admin.products.sync-menu') }}" class="btn btn-primary pull-right mb-2">Синхронизировать меню</a>
{{--            <a href="{{ route('admin.products.sync-ingredients') }}" class="btn btn-primary pull-right mb-2">Синхронизировать ингридиенты</a>--}}
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary pull-right mb-2">Добавить продукт</a>
        </div>


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
                            <th class="text-center"> Категории </th>
                            <th class="text-center"> Цена </th>
                            <th class="text-center"> Статус </th>
                            <th style="width:100px; min-width:100px;" class="text-center text-danger"><i class="fa fa-bolt"> </i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td>
                                    @foreach($product->categories as $category)
                                        <span class="badge badge-info">{{ $category->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $product->price }} {{ config('settings.currency_symbol') }}</td>
                                <td class="text-center">
                                    <div class="toggle-flip">
                                        <label>
                                            <input type="checkbox" name="hidden" {{ $product->hidden == 0 ? 'checked' : '' }} data-table="{{ $product->getTable() }}" data-id="{{ $product->id }}">
                                            <span class="flip-indecator" data-toggle-on="Вкл" data-toggle-off="Выкл">

                                            </span>
                                        </label>
                                    </div>

                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group" aria-label="Second group">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('admin.products.delete', $product->id) }}" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


