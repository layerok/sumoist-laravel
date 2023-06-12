<footer class=" flex justify-center items-center mv5">
    <div class="w-100 mw7 ph3 bg-black flex flex-row-l flex-column items-center justify-center">
        <!-- Лого -->
        <a href="/" class=" mb3 pb2 mb0-l pb0-l">
            <img class="w5  " src="{{ Asset::load('img/logo.jpg') }}" alt="">
        </a>

        <!-- Телефоны -->
        <div  class=" ml4-l pl3-l flex flex-column">
            @empty(!config('settings.phone'))
                @foreach(explode(';', config('settings.phone')) as $phone)
                <div class="flex items-center mb2">
                    <div class="nested-img mr2"><img src="{{ Asset::load('img/phone.svg') }}" alt=""></div>
                    <a href="tel:{{ preg_replace('/\D/','', $phone) }}" target="_blank" class="link white hover-dark-red f6 f5-ns">{{ $phone }}</a>
                </div>
                @endforeach
            @endempty

            @empty(!config('settings.social_instagram'))
                <div class="flex items-center mb2">
                    <div class="nested-img mr2 "><img src="{{ Asset::load('img/instagram.svg') }}" alt=""></div>
                    <a href="https://www.instagram.com/{{config('settings.social_instagram')}}/" target="_blank" class="f6 f5-ns white hover-dark-red link">{{ config('settings.social_instagram') }}</a>
                </div>
            @endempty


        </div>




    </div>


</footer>
