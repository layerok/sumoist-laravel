<!DOCTYPE HTML>
<html lang="en">
<head>
    <link rel="shortcut icon" href="{{ Asset::load('img/favicon.ico') }}"  >
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Выбор ресторана - {{ config('settings.site_name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ Asset::load('frontend/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ Asset::load('frontend/css/tachyons.min.css') }}"/>
    <link rel="stylesheet" href="{{ Asset::load('frontend/css/custom-tachyons.css') }}"/>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js" defer></script>
</head>
<body class="bg-black white roboto">

<style>
    @media screen and (min-width: 30em) {
        .w-45-ns{
            width: 45%;
        }
    }

</style>

<div x-data="getSpots()">
    <div style="background-image: url('{{ Asset::load('img/bg.jpg') }}')">

        <div class="flex flex-column justify-center items-center vh-100 ph3">
            <h1 class="tc all-right">Выберите ресторан</h1>
            <div class="w-100 mw6 flex flex-column flex-row-ns items-center justify-between tc">
                <template x-for="spot in spots" :key="spot.id">
                    <div class="bg-animate hover-bg-red pointer pa3 bg-dark-red w-100 w-45-ns mb3 h4 br2 ba b--dark-red flex justify-center items-center ">
                        <a :href="spot.link" x-text="spot.address" class="f3 tc white link"></a>
                    </div>

                </template>

            </div>
        </div>

    </div>

</div>

<script>
    function getSpots(){
        return {
            spots: [
                {
                    id: 1,
                    address: 'Жемчужная 9, жк 49 жемчужина',
                    link: 'http://127.0.0.1:8000/homepage',
                },
                {
                    id: 2,
                    address: 'Маршала Малиновского, 55',
                    link: 'http://kador.sumoist.com.ua',
                }
            ]
        }
    }
</script>



</body>
</html>
