<!-- Попап -->
<div @isset($is_second_popup)id="show-sign-modal_2" @else id="show-sign-modal" @endisset data-layerok-modal class="dn overflow-hidden " >
    <div class="w6 " >
        <div class="flex justify-between bg-dark-red ba br--top br2 b--dark-red ">
            <p class="f4 white mv2 pv1 mh3">Кошик</p>
            <p data-layerok-dismiss  class="mv0 black mv2 pv1 mh3 pointer">&times;</p>
        </div>
        <!-- products container -->
        <div >
            <div data-cart-popup-items data-cart-not-empty data-cart-clear class="@if(Cart::isEmpty()) dn @endif overflow-y-scroll bg-white" style="max-height: 230px">
                <!-- Product -->
                @foreach(Cart::getContent() as $product)
                    <form action="/cart/manipulate" method="post" data-buy  class="flex justify-between ph3 pb2 pt2 mt1 bb b--black">
                        <div class="w-80 flex items-start">

                            <div class="w-40 flex-shrink-0">
                                <img src="{{  Image::getPath($product) }}" alt="">
                            </div>
                            <div class="flex flex-column black f6 ml2 pl1">
                                <p  class="mt0 mb1">{{ $product->name }}</p>
                                <p class="mt0 mb1">{{ $product->price }} грн</p>
                                <p class="mt0 mb1">{{ $product->quantity }} шт.</p>
                            </div>
                        </div>
                        <div class="flex justify-end">

                            <input type="hidden" name="uid" value="{{ $product->attributes->uid }}" >
                            <button type="submit" name="action" value="remove" class="self-start black mv0 pointer f4 bg-transparent bn">&times;</button>
                        </div>
                    </form>
                @endforeach
            </div>

            <div data-cart-not-empty class="@if(Cart::isEmpty()) dn @endif bg-white ba br--bottom br2 b--white ">
                <div class="">
                    <div class="flex justify-between mh3 mv2 black">
                        <div >Всього</div>
                        <div ><span data-cart-total>{{ Cart::getTotal() }}</span> грн</div>
                    </div>
                    <div class="mh3 mv2 pv1 flex justify-center" >
                        <a href="{{ route('order.index') }}"  class="link db w4 bg-dark-red tc white pa2 bn br-pill bg-animate hover-bg-red pointer">Оформити</a>
                    </div>
                </div>
            </div>
        </div>

        <div data-cart-empty class="@if(!Cart::isEmpty()) dn @endif bg-white ba br--bottom br2 b--white ">
            <div class="ph3 pb2 pt2 mv2">
                <p class="black mv0">Кошик порожній</p>
            </div>

        </div>
    </div>
</div>
