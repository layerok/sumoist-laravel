<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title') - {{ config('app.name') }}</title>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="{{ Asset::load('favicon.ico', true) }}"  >
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::load('backend/css/main.css', true) }}" />
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js" defer></script>
    <!-- Place your kit's code here -->
    <script src="https://kit.fontawesome.com/09179831a3.js" crossorigin="anonymous"></script>
    <style>
        .tile{
            overflow-y:auto;
        }
    </style>
</head>
<body class="app sidebar-mini rtl">
@include('admin.partials.header')
@include('admin.partials.sidebar')
<main class="app-content" id="app">
    @yield('content')
</main>
<script src="{{ Asset::load('backend/js/jquery-3.2.1.min.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/popper.min.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/bootstrap.min.js', true) }}"></script>

<script src="{{ Asset::load('backend/js/ckeditor5/build/ckeditor.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/main.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/plugins/pace.min.js', true) }}"></script>
<script type="text/javascript" src="{{ Asset::load('backend/js/plugins/jquery.dataTables.min.js', true) }}"></script>
<script type="text/javascript" src="{{ Asset::load('backend/js/plugins/dataTables.bootstrap.min.js', true) }}"></script>
<script>

    // Disable search and ordering by default
    $.extend( $.fn.dataTable.defaults, {
        stateSave: true,
        language: {
            url: '{{ Asset::load('backend/js/plugins/dataTables/localizations/Russian.json', true) }}'
        }
    } );

    const table = $('#sampleTable').DataTable();

    table.on( 'click', function (event) {
        const target = event.target.closest(".flip-indecator");

        if (target) {
            const input = $(target).siblings("[name='hidden']");
            let hidden = input.prop("checked");
            let id = input.data("id");
            let table = input.data("table");

            console.log(hidden, id, table);

            $.ajax({
                type: "post",
                url: "/admin/ajax/change-state",
                data: {
                    id: id,
                    table: table,
                    hidden: hidden,
                    "_token": "{{ csrf_token() }}",
                }
            })
        }

    } );



</script>
@stack('scripts')


</body>
</html>
