<!-- Корзина -->

    <div class="flex flex-shrink-0"  @isset($is_second_popup) data-layerok="#show-sign-modal_2" @else data-layerok="#show-sign-modal" @endisset>

        <div class="nested-img pointer " >
            <img src="/storage/img/cart.svg" alt="Корзина">
        </div>
        <div class=" flex-column justify-between ml3-ns pl2 dn">
            <div  class="f4"><span data-cart-total>{{ Cart::getTotal() }}</span>&nbsp;грн.</div>
            <div ><span data-cart-total-quantity>{{ Cart::getTotalQuantity() }}</span> тов.</div>
        </div>
        <div class="ba b--black absolute bg-orange black br-pill ph2 pv1 right-0" style="top:2px"><span data-cart-total-quantity>{{ Cart::getTotalQuantity() }} </span></div>

    </div>
    <div class="absolute right-0 top-3 z-4">
        @include('theme::site.partials.cartPopUp')
    </div>



