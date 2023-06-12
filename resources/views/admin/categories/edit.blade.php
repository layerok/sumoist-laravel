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
                <form action="{{ route('admin.categories.update') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="tile-body">
                        <div class="form-group">
                            <label class="control-label" for="name">Имя <span class="m-l-5 text-danger"> *</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $targetCategory->name) }}"/>
                            <input type="hidden" name="id" value="{{ $targetCategory->id }}">
                            @error('name') {{ $message }} @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="sort_order">Порядок сортировки <span class="m-l-5 text-danger"> *</span></label>
                            <input class="form-control @error('sort_order') is-invalid @enderror" type="text" name="sort_order" id="sort_order" value="{{ old('sort_order', $targetCategory->sort_order) }}" />
                            @error('sort_order') {{ $message }} @enderror
                        </div>
                        {{--<div class="form-group">
                            <label for="parent">Parent Category <span class="m-l-5 text-danger"> *</span></label>
                            <select id=parent class="form-control custom-select mt-15 @error('parent_id') is-invalid @enderror" name="parent_id">
                                <option value="0">Select a parent category</option>
                                @foreach($categories as $key => $category)
                                    @if ($targetCategory->parent_id == $key)
                                        <option value="{{ $key }}" selected> {{ $category }} </option>
                                    @else
                                        <option value="{{ $key }}"> {{ $category }} </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id') {{ $message }} @enderror
                        </div>--}}

                        <div class="form-group">
                            <div class="toggle-flip">
                                <label>
                                    <input type="checkbox" name="hidden" {{ $targetCategory->hidden == 0 ? 'checked' : '' }} >
                                    <span class="flip-indecator" data-toggle-on="Вкл" data-toggle-off="Выкл"></span>
                                </label>
                            </div>

                        </div>
{{--                        <div class="form-group">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-2">--}}
{{--                                    @if ($targetCategory->image != null)--}}
{{--                                        <figure class="mt-2" style="width: 80px; height: auto;">--}}
{{--                                            <img src="{{ asset('storage/'.$targetCategory->image) }}" id="categoryImage" class="img-fluid" alt="img">--}}
{{--                                        </figure>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                                <div class="col-md-10">--}}
{{--                                    <label class="control-label">Category Image</label>--}}
{{--                                    <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image"/>--}}
{{--                                    @error('image') {{ $message }} @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Обновить категорию</button>
                        &nbsp;&nbsp;&nbsp;
                        <a class="btn btn-secondary" href="{{ route('admin.categories.index') }}"><i class="fa fa-fw fa-lg fa-times-circle"></i>Отменить</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
