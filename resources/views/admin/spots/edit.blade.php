@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-tags"></i> {{ $pageTitle }}</h1>
        </div>
    </div>
    @include('admin.partials.flash')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="tile">
                <h3 class="tile-title">{{ $subTitle }}</h3>
                <form action="{{ route('admin.ingredients.update') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="tile-body">
                        <div class="form-group">
                            <label class="control-label" for="name">Имя на сайте <span class="m-l-5 text-danger"> *</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $targetIngredient->name) }}"/>
                            <input type="hidden" name="id" value="{{ $targetIngredient->id }}">
                            @error('name') {{ $message }} @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="poster_name">Имя в постере <span class="m-l-5 text-danger"> *</span></label>
                            <input class="form-control @error('poster_name') is-invalid @enderror" type="text" name="poster_name" id="poster_name" value="{{ old('poster_name', $targetIngredient->poster_name) }}" readonly/>
                            <input type="hidden" name="id" value="{{ $targetIngredient->id }}">
                            @error('poster_name') {{ $message }} @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" id="status" name="status"
                                        {{ $targetIngredient->status == 1 ? 'checked' : '' }}
                                    />Показывать в меню
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    @if ($targetIngredient->image != null)
                                        <figure class="mt-2" style="width: 80px; height: auto;">
                                            <img src="{{ asset('storage/'.$targetIngredient->image) }}" id="ingredientImage" class="img-fluid" alt="img">
                                        </figure>
                                    @endif
                                </div>
                                <div class="col-md-10">
                                    <label class="control-label">Ingredient Image</label>
                                    <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image"/>
                                    @error('image') {{ $message }} @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Обновить Ингридиент</button>
                        &nbsp;&nbsp;&nbsp;
                        <a class="btn btn-secondary" href="{{ route('admin.ingredients.index') }}"><i class="fa fa-fw fa-lg fa-times-circle"></i>Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
