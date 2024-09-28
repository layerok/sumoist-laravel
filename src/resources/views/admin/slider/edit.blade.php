
@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('backend/js/plugins/dropzone/dist/min/dropzone.min.css') }}"/>
@endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fab fa-slideshare"></i> {{ $pageTitle }} - {{ $subTitle }}</h1>
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
                        <form action="{{ route('admin.slider.update') }}" method="POST" role="form" enctype="multipart/form-data" >
                            @csrf
                            <h3 class="tile-title">Данные слайда</h3>
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
                                        value="{{ old('name', $record->name) }}"
                                    />

                                    <input type="hidden" name="id" value="{{ $record->id }}">
                                    <div class="invalid-feedback active">
                                        <i class="fa fa-exclamation-circle fa-fw"></i> @error('name') <span>{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label" for="slug">Ссылка</label>
                                    <input
                                        class="form-control @error('slug') is-invalid @enderror"
                                        type="text"
                                        placeholder="Enter attribute name"
                                        id="slug"
                                        name="slug"
                                        value="{{ old('slug', $record->slug) }}"
                                    />

                                    <div class="invalid-feedback active">
                                        <i class="fa fa-exclamation-circle fa-fw"></i> @error('slug') <span>{{ $message }}</span> @enderror
                                    </div>
                                </div>



                                <div class="form-group">
                                    <div class="toggle-flip">
                                        <label>
                                            <input type="checkbox" name="hidden" {{ $record->hidden == 0 ? 'checked' : '' }} >
                                            <span class="flip-indecator" data-toggle-on="Вкл" data-toggle-off="Выкл"></span>
                                        </label>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12 col-sm-3 ">
                                        @if ($record->image != null)
                                            <img style="width: 100%"  src="{{  asset('storage/'.$record->image) }}" id="logoImg" style="width: 80px; height: auto;">
                                        @else
                                            <img src="https://via.placeholder.com/80x80?text=Placeholder+Image" id="logoImg" style="width: 80px; height: auto;">
                                        @endif
                                    </div>
                                    <div class="col-12 col-sm-9 ">
                                        <div class="form-group">
                                            <label class="control-label">Изображение слайда</label>
                                            <input class="form-control  @error('image') is-invalid @enderror" type="file" name="image" onchange="loadFile(event,'logoImg')"/>
                                            @error('image') {{ $message }} @enderror
                                        </div>
                                    </div>
                                </div>


                            </div>
                            <div class="tile-footer">
                                <div class="row d-print-none mt-2">
                                    <div class="col-12 text-right">
                                        <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Обновить слайд</button>
                                        <a class="btn btn-danger" href="{{ route('admin.slider.index') }}"><i class="fa fa-fw fa-lg fa-arrow-left"></i>Назад</a>
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

    <script>
        loadFile = function(event, id) {
            var output = document.getElementById(id);
            output.src = URL.createObjectURL(event.target.files[0]);
        };
    </script>

@endpush
