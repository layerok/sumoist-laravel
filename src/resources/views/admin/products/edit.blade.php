
@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/js/plugins/dropzone/dist/min/dropzone.min.css') }}"/>
@endsection
@section('content')
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
                    <li class="nav-item"><a class="nav-link" href="#images" data-toggle="tab">Изображение</a></li>
                    <li class="nav-item"><a class="nav-link" href="#attributes" data-toggle="tab">Аттрибуты</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane active" id="general">
                    <div class="tile">
                        <form action="{{ route('admin.products.update') }}" method="POST" role="form">
                            @csrf
                            <h3 class="tile-title">Данные товара</h3>
                            <hr>
                            <div class="tile-body">
                                <div class="form-group">
                                    <label class="control-label" for="name">Имя</label>
                                    <input
                                        class="form-control @error('name') is-invalid @enderror"
                                        type="text"
                                        placeholder="Enter attribute name"
                                        id="name"
                                        name="name"
                                        value="{{ old('name', $product->name) }}"
                                    />

                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
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
                                        value="{{ old('poster_id', $product->poster_id) }}"
                                    />
                                    <div class="invalid-feedback active">
                                        <i class="fa fa-exclamation-circle fa-fw"></i> @error('poster_id') <span>{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="image">Ссылка на изображение</label>
                                    <input
                                        class="form-control @error('image') is-invalid @enderror"
                                        type="text"
                                        placeholder="Введить ссылку на изображение"
                                        id="image"
                                        name="image"
                                        value="{{ old('image', $product->image) }}"
                                    />
                                    <div class="invalid-feedback active">
                                        <i class="fa fa-exclamation-circle fa-fw"></i> @error('image') <span>{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="image">Описание</label>
                                    <textarea
                                        class="form-control @error('description') is-invalid @enderror"
                                        id="description"
                                        name="description"
                                    >{{ old('description', $product->description) }}</textarea>
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
                                                    @php $check = in_array($category->id, $product->categories->pluck('id')->toArray()) ? 'selected' : ''@endphp
                                                    <option value="{{ $category->id }}" {{ $check }}>{{ $category->name }}</option>
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
                                                value="{{ old('price', $product->price) }}"
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
                                                placeholder="Enter product weight"
                                                id="weight"
                                                name="weight"
                                                value="{{ old('weight', $product->weight) }}"
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
                                                value="{{ old('sort_order', $product->sort_order) }}"
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
                                                value="{{ old('unit', $product->unit) }}"
                                            />
                                            <div class="invalid-feedback active">
                                                <i class="fa fa-exclamation-circle fa-fw"></i> @error('unit') <span>{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>

                                </div>


                                <div class="form-group">
                                    <div class="toggle-flip">
                                        <label>
                                            <input type="checkbox" name="hidden" {{ $product->hidden == 0 ? 'checked' : '' }}>
                                            <span class="flip-indecator" data-toggle-on="Вкл" data-toggle-off="Выкл">

                                            </span>
                                        </label>
                                    </div>


                            </div>
                            <div class="tile-footer">
                                <div class="row d-print-none mt-2">
                                    <div class="col-12 text-right">
                                        <button class="btn btn-success" name="action" type="submit" value="save"><i class="fa fa-fw fa-lg fa-check-circle"></i>Обновить продукт</button>
                                        <button class="btn btn-success" name="action" type="submit" value="close">
                                            <i class="fa fa-fw fa-lg fa-check-circle"></i>Обновить продукт и <span style="color: darkred">выйти</span>
                                        </button>

                                        <a class="btn btn-danger" href="{{ route('admin.products.index') }}"><i class="fa fa-fw fa-lg fa-arrow-left"></i>Назад</a>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="images">
                <div class="tile">
                    <h3 class="tile-title">Загрузить изображение</h3>
                    <hr>
                    <div class="tile-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form action="" class="dropzone" id="dropzone" style="border: 2px dashed rgba(0,0,0,0.3)">
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </div>
                        <div class="row d-print-none mt-2">
                            <div class="col-12 text-right">
                                <button class="btn btn-success" type="button" id="uploadButton">
                                    <i class="fa fa-fw fa-lg fa-upload"></i>Загрузить изображение
                                </button>
                            </div>
                        </div>
                        @if ($product->images)
                            <hr>
                            <div class="row">
                                @foreach($product->images as $image)
                                    <div class="col-md-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <img src="{{ asset('storage/'.$image->full) }}" id="brandLogo" class="img-fluid" alt="img">
                                                <a class="card-link float-right text-danger" href="{{ route('admin.products.images.delete', $image->id) }}">
                                                    <i class="fa fa-fw fa-lg fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
                <div class="tab-pane" id="attributes">
                    <product-attributes :productid="{{ $product->id }}"></product-attributes>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ Asset::load('backend/js/plugins/select2.min.js', true)}}"></script>
    <script>
        $( document ).ready(function() {
            $('#categories').select2();

        });
    </script>
    <script type="text/javascript" src="{{ Asset::load('backend/js/plugins/dropzone/dist/min/dropzone.min.js', true) }}"></script>
    <script type="text/javascript" src="{{ Asset::load('backend/js/plugins/bootstrap-notify.min.js', true) }}"></script>
    <script>
        Dropzone.autoDiscover = false;

        $( document ).ready(function() {
            $('#categories').select2();


            let myDropzone = new Dropzone("#dropzone", {
                paramName: "image",
                addRemoveLinks: false,
                maxFilesize: 12,
                parallelUploads: 2,
                uploadMultiple: false,
                url: "{{ route('admin.products.images.upload') }}",
                autoProcessQueue: false,
            });
            myDropzone.on("queuecomplete", function (file) {
                window.location.reload();
                showNotification('Completed', 'All product images uploaded', 'success', 'fa-check');
            });
            $('#uploadButton').click(function(){
                if (myDropzone.files.length === 0) {
                    showNotification('Error', 'Please select files to upload.', 'danger', 'fa-close');
                } else {
                    myDropzone.processQueue();
                }
            });
            function showNotification(title, message, type, icon)
            {
                $.notify({
                    title: title + ' : ',
                    message: message,
                    icon: 'fa ' + icon
                },{
                    type: type,
                    allow_dismiss: true,
                    placement: {
                        from: "top",
                        align: "right"
                    },
                });
            }
        });
    </script>

    <script type="text/javascript" src="{{ Asset::load('backend/js/app.js', true) }}"></script>
@endpush
