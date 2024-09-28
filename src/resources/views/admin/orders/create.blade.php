@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
=@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fas fa-shopping-cart"></i> {{ $pageTitle }} - {{ $subTitle }}</h1>
        </div>
    </div>
    @include('admin.partials.flash')
    <div class="row user">
        <div class="col-md-3">
            <div class="tile p-0">
                <ul class="nav flex-column nav-tabs user-tabs">
                    <li class="nav-item"><a class="nav-link active" href="#general" data-toggle="tab">Основные</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane active" id="general">
                    <div class="tile">
                        <form action="{{ route('admin.orders.store') }}" method="POST" role="form">
                            @csrf
                            <h3 class="tile-title">Данные товара</h3>
                            <hr>
                            <div class="tile-body">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="sum">Сумма</label>
                                            <input
                                                class="form-control @error('sum') is-invalid @enderror"
                                                type="text"
                                                placeholder="Введите сумму заказа"
                                                id="sum"
                                                name="sum"
                                                value="{{ old('sum') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('sum') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="delivery_id">Способ доставки</label>
                                            <select name="delivery_id"  class="form-control">
                                                @foreach($delivery as $value)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="payment_id">Способ оплаты</label>
                                            <select name="payment_id"  class="form-control">
                                                @foreach($payment as $value)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="payment_status_id">Статус оплаты</label>
                                            <select name="payment_status_id"  class="form-control">
                                                @foreach($payment_statuses as $value)
                                                    <option value="{{ $value->id }}" selected>{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="comment">Комментарий</label>
                                            <textarea class="form-control" rows="4" name="comment" id="comment">{{ old('comment') }}</textarea>
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('comment') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="first_name">Имя</label>
                                            <input
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                type="text"
                                                placeholder="Введите имя пользователя"
                                                id="first_name"
                                                name="first_name"
                                                value="{{ old('first_name') }}"
                                            />

                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('first_name') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">Email</label>
                                            <input
                                                class="form-control"
                                                type="text"
                                                placeholder="Введите email пользователя"
                                                id="email"
                                                name="email"
                                                value="{{ old('email') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('email') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="phone">Телефон</label>
                                            <input
                                                class="form-control @error('phone') is-invalid @enderror"
                                                type="text"
                                                placeholder="Введите телефон пользователя"
                                                id="phone"
                                                name="phone"
                                                value="{{ old('phone') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('phone') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="address">Адрес</label>
                                            <input
                                                class="form-control"
                                                type="text"
                                                placeholder="Введите адрес пользователя"
                                                id="address"
                                                name="address"
                                                value="{{ old('address') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('address') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="tile-footer">
                                <div class="row d-print-none mt-2">
                                    <div class="col-12 text-right">
                                        <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Сохранить заказ</button>
                                        <a class="btn btn-danger" href="{{ route('admin.orders.index') }}"><i class="fa fa-fw fa-lg fa-arrow-left"></i>Назад</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('backend/js/plugins/select2.min.js') }}"></script>
    <script>
        $( document ).ready(function() {
            $('#categories').select2();
        });
    </script>
@endpush
