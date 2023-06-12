@extends('theme::site.app')
@section('title', 'Оплата')

@section('content')

    <div class="flex justify-center">
        <div class="mw7 ph3 w-100">

            <div data-cart-not-empty class="@if(Cart::isEmpty()) dn  @endif"  >
                <h2 class="tc f2 fw4">Оформлення замовлення</h2>
                <div class="flex flex-column flex-row-l">
                    <div class="order-2 order-1-l w-40-l pr3-l mt4 mt0-l">

                        <div ><span class="gold">Телефон</span> - обязательное поле</div>
                        <form  class="mt2 checkout-form"  action="{{ route('order.send') }}" method="post">
                            @csrf
                            <div class="flex mb3 pb2">
                                @foreach($delivery as $key => $record)
                                    <input  id="deliveryMethod{{ $record->id }}" class="checked-bg-orange checked-black dn" type="radio" name="delivery_id" value="{{ $record->id }}" @if($loop->first) checked @endif >
                                    <label class="w-100 bg-white black pa2 tc @if($loop->first) br2  br--left @endif @if($loop->last) br2 br--right  @endif  pointer" for="deliveryMethod{{$record->id}}">{{ $record->name }}</label>
                                @endforeach
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <input  name="name" class=" ph3 pv2 w-100 br2 bn placeholder-black black" type="text" placeholder="Имя">
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <input  name="email" class=" ph3 pv2 w-100 br2 bn placeholder-black black" type="text" placeholder="Email">
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <input name="phone" data-type="phone"  value="" type="tel"  class=" ph3 pv2 w-100 br2 bn placeholder-black black" placeholder="Телефон">
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <input name="address" class=" ph3 pv2 w-100 br2 bn placeholder-black black" type="text" placeholder="Адрес доставки">
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <input  name="comment" class=" ph3 pv2 w-100 br2 bn placeholder-black black" type="text" placeholder="Комментарий к заказу">
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <input  name="sticks" class=" ph3 pv2 w-100 br2 bn placeholder-black black" type="text" placeholder="Палочки на сколько персон?">
                            </div>
                            <div class="flex mb3 pb2">
                                @foreach($payment as $key => $record)
                                    <input id="paymentMethod{{ $record->id }}" class="checked-bg-orange checked-black dn" type="radio" name="payment_id" value="{{ $record->id }}" @if($loop->first) checked @endif  >
                                    <label class="w-100 bg-white black pa2 tc  @if($loop->first) br2 br--left @endif @if($loop->last) br2 br--right  @endif  pointer" for="paymentMethod{{$record->id}}">{{ $record->name }}</label>
                                @endforeach
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <input name="change" class=" ph3 pv2 w-100 br2 bn placeholder-black black" type="text" placeholder="Приготовить сдачу с">
                            </div>
                            <div class="flex items-center">
                                <button id="checkout-loader" class="dn relative link w4 bg-orange tc black pa3 bn br-pill bg-animate hover-bg-gold pointer">
                                    <span style="opacity:0" >Оформить</span>
                                    <div style="transform: scale(0.3); position: absolute ;top: -14px;left: 26px;" class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                    {{--<div  style="top: -16px;left: -8px;transform: scale(0.25);width: auto;height: auto;" class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>--}}
                                </button>
                                <button id="place-order" type="submit"  class="relative link db w4 bg-orange tc black pa3 bn br-pill bg-animate hover-bg-gold pointer">
                                    <span >Оформить</span>
                                    {{--<div  style="top: -16px;left: -8px;transform: scale(0.25);width: auto;height: auto;" class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>--}}
                                </button>
                                <div  class="ml4 fw5 mv0"><span data-cart-total class="f2">{{ Cart::getTotal() }}</span> <span class="f4"> грн. </span></div>
                            </div>
                           {{-- <div class="mt3 dark-red" >Ошибка</div>--}}


                        </form>
                        <div id="form-holder" class="mt3"></div>
                    </div>
                    <div class="order-1 order-2-l w-100 w-60-l pa2 ba b--orange overflow-y-scroll self-start" style="max-height: 520px " >
                        <!-- products container -->
                        <div class="flex flex-column f3 ">
                            <h3 class="dn-ns f3 fw5 mt0 pl2">Мой заказ:</h3>
                            <!-- product container -->
                            <div data-cart-checkout-items data-cart-clear>
                                @foreach($cart_items as $product)
                                    <form action="/cart/manipulate" method="post" data-buy  class="flex relative mb3">
                                        @csrf
                                        <input type="hidden" name="uid" value="{{ $product->attributes->uid }}" >
                                        <button type="submit" name="action" value="remove"  class="ph0 self-start white absolute right-0 top-0 pointer bg-transparent bn">&times;</button>

                                        <div class="w-40 nested-img dn db-ns">
                                            <img src="{{ Image::getPath($product) }}" style="max-height:150px; width:auto" >
                                        </div>
                                        <div class="w-100 w-60-ns pl2 flex flex-column-ns flex-row justify-between">
                                            <div>
                                                <h3 class="f5 f3-ns fw5 mt0 mb2-ns mb0">{{ $product->name }}</h3>
                                                <p class="fw5 mv0 f5 dn-ns">{{ $product->quantity }} шт</p>
                                            </div>
                                            <p  class="f6 fw5 mt0 mb3 dn db-ns">
                                                {{ $product->attributes->ingredients }}
                                            </p>
                                            <div class="flex justify-between items-end-ns items-start">
                                                <div class=" fw5 mv0 mr4 mr0-ns"><span class="f2-ns f4">{{ $product->price }}</span> <span class="f4-ns f6"> грн.</span></div>
                                                <p class="fw5 mv0 f5 dn db-ns">{{ $product->quantity }} шт</p>
                                            </div>
                                            <p class="f4 mv0 dn db-ns">{{ $product->associatedModel->weight }} г</p>
                                        </div>
                                    </form>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <h2 data-cart-empty class="@if(!Cart::isEmpty()) dn @endif tc f2 fw4">Корзина пуста :(</h2>

        </div>

    </div>


@stop

@push('scripts')

@endpush
