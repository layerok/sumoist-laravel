@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa fa-user"></i> {{ $pageTitle }}</h1>
        </div>
    </div>
    @include('admin.partials.flash')
    <div class="row">
        <div class="col-md-3">
            <div class="tile p-0">
                <ul class="nav flex-column nav-tabs user-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">Основные</a></li>
                    <li class="nav-item"><a class="nav-link" href="#orders" data-toggle="tab">Осуществленные заказы</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane active" id="general">

                <div class="tile">
                <h3 class="tile-title">{{ $subTitle }}</h3>
                <form action="{{ route('admin.users.update') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="tile-body">
                        <div>Создан: {{ $targetRecord->created_at }}</div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="name">Имя </label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $targetRecord->name) }}" placeholder="Введите имя пользователя"/>
                                    <input type="hidden" name="id" value="{{ $targetRecord->id }}">
                                    @error('name') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="phone">Телефон <span class="m-l-5 text-danger"> *</span></label>
                                    <input class="form-control @error('phone') is-invalid @enderror" type="text" name="phone" id="phone" value="{{ old('phone', $targetRecord->phone) }}" placeholder="Введите телефон пользователя"/>
                                    @error('phone') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="email">Эл. почта </label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="text" name="email" id="email" value="{{ old('email', $targetRecord->email) }}" placeholder="Введите эл. почту пользователя"/>
                                    @error('email') {{ $message }} @enderror
                                </div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="control-label" for="address">Адрес </label>
                            <input class="form-control @error('address') is-invalid @enderror" type="text" name="address" id="address" value="{{ old('address', $targetRecord->address) }}" placeholder="Введите адрес пользователя"/>
                            @error('address') {{ $message }} @enderror
                        </div>




                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-primary mb-1" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Обновить пользователя</button>
                        &nbsp;&nbsp;&nbsp;
                        <a class="btn btn-secondary mb-1" href="{{ route('admin.users.index') }}"><i class="fa fa-fw fa-lg fa-times-circle"></i>Отменить</a>
                    </div>
                </form>
            </div>
                </div>
                <div class="tab-pane" id="orders">
                    <user-orders :userid="{{ $targetRecord->id }}"></user-orders>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ Asset::load('backend/js/app.js', true) }}"></script>
@endpush
