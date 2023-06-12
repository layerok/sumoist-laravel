@extends('admin.app')
@section('title') Dashboard @endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-dashboard"></i> Админ панель</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <div class="widget-small primary coloured-icon">
                <i class="icon fa fa-users fa-3x"></i>
                <div class="info">
                    <h4>Продукты</h4>
                    <p><b>{{ $products_count }}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="widget-small info coloured-icon">
                <i class="icon fa fa-tags fa-3x"></i>
                <div class="info">
                    <h4>Категории</h4>
                    <p><b>{{ $categories_count }}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="widget-small warning coloured-icon">
                <i class="icon fa fa-user fa-3x"></i>
                <div class="info">
                    <h4>Покупатели</h4>
                    <p><b>{{ $users_count }}</b></p>
                </div>
            </div>
        </div>
        {{--<div class="col-md-6 col-lg-3">
            <div class="widget-small warning coloured-icon">
                <i class="icon fas fa-store fa-3x"></i>
                <div class="info">
                    <h4>Заведения</h4>
                    <p><b>{{ $spots_count }}</b></p>
                </div>
            </div>
        </div>--}}
        <div class="col-md-6 col-lg-3">
            <div class="widget-small danger coloured-icon">
                <i class="icon fa fa-th fa-3x"></i>
                <div class="info">
                    <h4>Ингридиенты</h4>
                    <p><b>{{ $attribute_values_count }}</b></p>
                </div>
            </div>
        </div>
    </div>
@endsection
