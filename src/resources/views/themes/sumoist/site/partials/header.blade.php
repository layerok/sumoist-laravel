<header class=" flex flex-column items-center  ">

    <nav class="drawer-nav" role="navigation">
        <div class="pr3">
            <ul class="list flex flex-column items-end">
                @foreach($menu as $page)
                    <li  class="mv1 flex-shrink-0"><!---->
                        <a href="/{{ $page->slug }}"  class="f6 @if (\Request::path() == $page->slug) bg-dark-red white @else white @endif  ph3 pv1 br-pill link mr2 hover-bg-dark-red hover-black  ">
                            <span class="ph1">{{ $page->name }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <!-- Телефоны -->
            <div  class=" w-100 flex flex-column justify-center tr mr3">
                @empty(!config('settings.phone'))
                    @foreach(explode(';', config('settings.phone')) as $phone)
                        <div class="flex items-center justify-end mb2">
                            <a href="tel:{{ preg_replace('/\D/','', $phone) }}" target="_blank" class="mr2 link white hover-dark-red f6 f5-ns">{{ $phone }}</a>
                            <div class="nested-img "><img src="{{ Asset::load('img/phone.svg') }}" alt=""></div>
                        </div>
                    @endforeach
                @endempty
                @empty(!config('settings.social_instagram'))
                    <div class="flex items-center justify-end mb2">
                        <a href="https://www.instagram.com/{{config('settings.social_instagram')}}/" target="_blank" class="mr2 f6 f5-ns white hover-dark-red link">{{ config('settings.social_instagram') }}</a>
                        <div class="nested-img  "><img src="{{ Asset::load('img/instagram.svg') }}" alt=""></div>
                    </div>
                @endempty
            </div>
        </div>



    </nav>
    <div class="w-100 mw7 ph3  relative pt3">
        <button type="button" class="drawer-toggle drawer-hamburger border border-secondary" style="background-color:black">
            <span class="sr-only">toggle navigation</span>
            <span class="drawer-hamburger-icon white"></span>
        </button>
        <section class="w-100 flex flex-column  flex-wrap flex-nowrap-l justify-between mb2 relative">


            <!-- Лого -->
            <div class="w-100 flex justify-between relative">
                <div class="flex">
                    <a class="w5 h3" href="/">
                        <img style="height:100%"  src="{{ Asset::load('img/logo.png') }}" alt="">
                    </a>
                </div>
                <div class="dn db-l">
                    <ul class="list flex items-end">
                        @foreach($menu as $page)
                            <li  class="mv1 flex-shrink-0"><!---->
                                <a href="/{{ $page->slug }}"  class="f6 @if (\Request::path() == $page->slug) bg-dark-red white @else white @endif   pv1 br-pill link  hover-bg-dark-red hover-white  ">
                                    <span class="ph1">{{ $page->name }}</span>
                                </a>
                            </li>

                        @endforeach

                    </ul>
                </div>
            </div>

            <div class=" w-100  flex   relative">
                <div class="flex flex-column ">
                    <div class="flex flex-column flex-row-ns">
                        <div>
                            <p><span class="dark-red">Час роботи:</span> {{ config('settings.start_working') }}-{{ config('settings.finish_working') }}</p>
                        </div>


                    </div>

                    <!-- Телефоны -->
                    <div  class=" w-100 flex-l dn   tr mr3">
                        @empty(!config('settings.phone'))
                            @foreach(explode(';', config('settings.phone')) as $phone)
                                <div class="flex items-center mr3  mb2">
                                    <div class="mr2 nested-img "><img src="{{ Asset::load('img/phone.svg') }}" alt=""></div>
                                    <a href="tel:{{ preg_replace('/\D/','', $phone) }}" target="_blank" class=" link white hover-dark-red f6 f5-ns">{{ $phone }}</a>
                                </div>
                            @endforeach
                        @endempty
                        @empty(!config('settings.social_instagram'))
                            <div class="flex items-center mb2">
                                <div class="nested-img mr2"><img src="{{ Asset::load('img/instagram.svg') }}" alt=""></div>
                                <a href="https://www.instagram.com/{{config('settings.social_instagram')}}/" target="_blank" class=" f6 f5-ns white hover-dark-red link">{{ config('settings.social_instagram') }}</a>
                            </div>
                        @endempty
                    </div>
                </div>
            </div>

            <div class="absolute h-auto bottom-0 right-0">
                @include('theme::site.partials.searchForm')
            </div>



        </section>
        <div ></div>
        <div  class="left-0 right-0 top-0 flex justify-center z-4">
            <div class="mw7 w-100 ">
                <div class="bg-black flex ">
                    @include('theme::site.partials.nav')
                </div>
            </div>

        </div>
        @if(Request::is('/'))
            @include('theme::site.partials.slider')
        @endif



    </div>

</header>

