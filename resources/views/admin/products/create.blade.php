@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
=@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-shopping-bag"></i> {{ $pageTitle }} - {{ $subTitle }}</h1>
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
                        <form action="{{ route('admin.products.store') }}" method="POST" role="form">
                            @csrf
                            <h3 class="tile-title">Данные товара</h3>
                            <hr>
                            <div class="tile-body">
                                <div class="form-group">
                                    <label class="control-label" for="name">Имя</label>
                                    <input
                                        class="form-control @error('name') is-invalid @enderror"
                                        type="text"
                                        placeholder="Введите имя продукта"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                    />

                                    <div class="invalid-feedback active">
                                        <i class="fa fa-exclamation-circle fa-fw"></i> @error('name') <span>{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="poster_id">ID в системе Poster</label>
                                    <input
                                        class="form-control @error('poster_id') is-invalid @enderror"
                                        type="text"
                                        placeholder="Введить id товара"
                                        id="poster_id"
                                        name="poster_id"
                                        value="{{ old('poster_id') }}"

                                    />
                                    <div class="invalid-feedback active">
                                        <i class="fa fa-exclamation-circle fa-fw"></i> @error('poster_id') <span>{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="image">Описание</label>
                                    <textarea
                                        class="form-control @error('description') is-invalid @enderror"
                                        id="description"
                                        name="description"
                                    >{{ old('description') }}</textarea>
                                    <div class="invalid-feedback active">
                                        <i class="fa fa-exclamation-circle fa-fw"></i> @error('description') <span>{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label" for="categories">Категории</label>
                                            <select name="categories[]" id="categories" class="form-control" multiple>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" >{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="price">Цена</label>
                                            <input
                                                class="form-control @error('price') is-invalid @enderror"
                                                type="text"
                                                placeholder="Введите цену товара"
                                                id="price"
                                                name="price"
                                                value="{{ old('price') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('price') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="weight">Вес</label>
                                            <input
                                                class="form-control"
                                                type="text"
                                                placeholder="Введите вес товара"
                                                id="weight"
                                                name="weight"
                                                value="{{ old('weight') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('weight') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="sort_order">Порядок сортировки</label>
                                            <input
                                                class="form-control @error('sort_order') is-invalid @enderror"
                                                type="text"
                                                placeholder="Введите позицию товара"
                                                id="sort_order"
                                                name="sort_order"
                                                value="{{ old('sort_order') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('sort_order') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label" for="unit">Единица измерения</label>
                                            <input
                                                class="form-control"
                                                type="text"
                                                placeholder="Введите единицу измерения"
                                                id="unit"
                                                name="unit"
                                                value="{{ old('unit') }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('unit') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>


                                <div class="form-group">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="hidden"
                                                   name="hidden"
                                            />Активный
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="tile-footer">
                                <div class="row d-print-none mt-2">
                                    <div class="col-12 text-right">
                                        <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Сохранить продукт</button>
                                        <a class="btn btn-danger" href="{{ route('admin.products.index') }}"><i class="fa fa-fw fa-lg fa-arrow-left"></i>Назад</a>
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
    <script type="text/javascript" src="{{ Asset::load('backend/js/plugins/select2.min.js', true) }}"></script>
    <script>
        $( document ).ready(function() {
            $('#categories').select2();
        });
    </script>
@endpush
