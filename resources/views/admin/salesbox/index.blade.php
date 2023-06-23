@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
    <div class="app-title">
        <div class="mb-3">
            <h1><i class="fa fa-mobile"></i> {{ $pageTitle }}</h1>
            <p>{{ $subTitle }} </p>
        </div>
        <div>
{{--            <a href="{{ route('admin.users.export') }}" class="btn btn-primary pull-right mb-1">Экспортировать в excel</a>--}}
        </div>

    </div>
    @include('admin.partials.flash')
    <div class="row">
        <div class="col-md-12">
            <div class="tile" style="overflow-y:auto">
                <div class="tile-body">
                    <form action="{{ route('admin.salesbox.sync-categories') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-primary pull-right mb-1">Синхронизировать категории</button>
                    </form>
                    <form action="{{ route('admin.salesbox.sync-products') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-primary pull-right mb-1">Синхронизировать товары</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush
