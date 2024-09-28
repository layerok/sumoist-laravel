<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sumoist</title>
    <link rel="stylesheet" href="{{ asset('frontend/css/tachyons.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('frontend/css/custom-tachyons.css') }}"/>
</head>
<body class="bg-black white roboto">

<div id="app">

        <app></app>


</div>

<script>
    function addScript(src){
        var script = document.createElement('script');
        script.src = src;
        script.async = false; // чтобы гарантировать порядок
        document.body.appendChild(script);
    }

    addScript('{{ mix('js/app1.js') }}'); // загружаться эти скрипты начнут сразу
    addScript('{{  asset('frontend/js/script.js') }}'); // но, гарантированно, в порядке 1 -> 2 -> 3



</script>




</body>
</html>
