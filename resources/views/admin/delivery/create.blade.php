@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fas fa-truck"></i> {{ $pageTitle }}</h1>
        </div>
    </div>
    @include('admin.partials.flash')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="tile">
                <h3 class="tile-title">{{ $subTitle }}</h3>
                <form action="{{ route('admin.delivery.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="tile-body">
                        <div class="form-group">
                            <label class="control-label" for="name">Имя <span class="m-l-5 text-danger"> *</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name') }}"/>
                            @error('name') {{ $message }} @enderror
                        </div>

                        <div class="form-group">
                            <div class="toggle-flip">
                                <label>
                                    <input type="checkbox" name="hidden" checked >
                                    <span class="flip-indecator" data-toggle-on="Вкл" data-toggle-off="Выкл"></span>
                                </label>
                            </div>
                        </div>


                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Сохранить</button>
                        &nbsp;&nbsp;&nbsp;
                        <a class="btn btn-secondary" href="{{ route('admin.delivery.index') }}"><i class="fa fa-fw fa-lg fa-times-circle"></i>Отменить</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
