@extends('admin.app')
@section('title') {{ $pageTitle }} @endsection
@section('content')
    <div class="app-title">
        <div>
            <h1><i class="far fa-file-alt"></i> {{ $pageTitle }}</h1>
        </div>
    </div>
    @include('admin.partials.flash')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="tile">
                <h3 class="tile-title">{{ $subTitle }}</h3>
                <form action="{{ route('admin.pages.update') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    <div class="tile-body">
                        <div class="form-group">
                            <label class="control-label" for="name">Имя <span class="m-l-5 text-danger"> *</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $targetRecord->name) }}"/>
                            <input type="hidden" name="id" value="{{ $targetRecord->id }}">
                            @error('name') {{ $message }} @enderror
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="name">Ссылка <span class="m-l-5 text-danger"> *</span></label>
                            <input class="form-control @error('slug') is-invalid @enderror" type="text" name="slug" id="slug" value="{{ old('slug', $targetRecord->slug) }}"/>
                            @error('slug') {{ $message }} @enderror
                        </div>

                        <div class="form-group">
                            <textarea name="content" id="editor">{{ old('content', $targetRecord->content) }}</textarea>
                        </div>


                        <div class="form-group">
                            <div class="toggle-flip">
                                <label>
                                    <input type="checkbox" name="hidden" {{ $targetRecord->hidden == 0 ? 'checked' : '' }} >
                                    <span class="flip-indecator" data-toggle-on="Вкл" data-toggle-off="Выкл"></span>
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="tile-footer">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Обновить</button>
                        &nbsp;&nbsp;&nbsp;
                        <a class="btn btn-secondary" href="{{ route('admin.pages.index') }}"><i class="fa fa-fw fa-lg fa-times-circle"></i>Отменить</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
        <script>ClassicEditor
            .create( document.querySelector( '#editor' ), {

                toolbar: {
                    items: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'link',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'indent',
                        'outdent',
                        '|',
                        'imageUpload',
                        'blockQuote',
                        'insertTable',
                        'mediaEmbed',
                        'undo',
                        'redo',
                        'removeFormat',
                        'alignment',
                        'CKFinder'
                    ]
                },
                language: 'ru',
                image: {
                    toolbar: [
                        'imageTextAlternative',
                        'imageStyle:full',
                        'imageStyle:side'
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                },
                licenseKey: '',

            } )
            .then( editor => {
                window.editor = editor;








            } )
            .catch( error => {
                console.error( 'Oops, something went wrong!' );
                console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
                console.warn( 'Build id: owx6h1t0wf2m-4xdnzge4pfj5' );
                console.error( error );
            } );
    </script>
    </script>
@endpush
