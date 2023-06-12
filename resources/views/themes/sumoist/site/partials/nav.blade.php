
<div id="nav" class="w-100 flex-column z-4 relative" style="top:-1px">

    <nav class="w-auto bt bb b--dark-red bg-black mb2 flex ">
        <ul class="w-100 ma0 pv3 list flex justify-between items-center ph1 mr4 mr0-l overflow-y-auto">
            @foreach($categories as $category)
                <li  class="mv1 flex-shrink-0"><!---->
                    <a href="/category/{{ $category->slug }}"  class="@isset($slug)@if($slug == $category->slug) bg-dark-red white @else white @endif @else white @endisset ph3-ns ph1 pv1 br-pill link mr2-ns mr1 hover-bg-dark-red hover-white  ">
                        <span class="ph1">{{ $category->name }}</span>
                    </a>
                </li>

            @endforeach

        </ul>
        @php $is_second_popup = true; @endphp
        <div class="cart-in-menu ">
            <div class="mh3-l pv2  items-center justify-end relative flex">
                @include('theme::site.partials.cart')
            </div>

        </div>

    </nav>
</div>






