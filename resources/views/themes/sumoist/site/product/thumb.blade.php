<div   class="pa3-ns pa2 w-100 w-50-ns w-33-l ">
    <div class="h-100">
        <div class="flex flex-column pb1 pb2 h-100 relative">
            <div class="nested-img flex flex-shrink-0 justify-center "  >
                    <div class="w-100 contain  bg-left" style="height: 12rem; background-image: url('{{ Image::getPath($product)  }}')">

                    </div>
            </div>
            <form action="{{ route('cart.manipulate') }}" method="post" data-buy class="flex flex-column justify-between h-100">
                @csrf
                <div>
                    <h3  class="f3-ns f4 fw8 mt3 mb0">{{ $product->name }}</h3>
                    <div class="f6-ns f7 fw3 mb2 mt1">

                            @php
                                $modificators = [];
                                $ingredients = [];
                                $sharpness = [];

                            @endphp
                            @foreach($product->attributes as $productAttribute)

                                @foreach($productAttribute->attributeValues as $key => $attributeValue)

                                    @if($attributeValue->attribute_id == 3)

                                        @php $ingredients[] = $attributeValue->value @endphp
                                    @endif
                                    @if($attributeValue->attribute_id == 6)

                                        @php $sharpness[] = $attributeValue @endphp
                                    @endif

                                    @if($attributeValue->attribute->frontend_type == 'radio')
                                        @php

                                            $modificators[] = [
                                                        'id'                    => $attributeValue->poster_id,
                                                        'price'                 => $productAttribute->price,
                                                        'value'                 => $attributeValue->value,
                                                        'attribute_value_id'    => $attributeValue->id
                                                    ]
                                        @endphp




                                    @endif
                                @endforeach

                            @endforeach
                        @if(count($modificators) > 0)

                        <p class="flex flex-column flex-row-l mb3 bg-white-10 br2 modificator bg-black">
                            @foreach($modificators as $key => $modificator)
                            <input data-modificator id="modificator_{{ $modificator["id"] }}" class="checked-bg-dark-red checked-white dn" type="radio" name="active_modificator" value="{{ $key }}"  checked >
                            <label class=" ma2 w-100-l bg-white black pa2 tc br-pill shadow-1 pointer flex items-center justify-center" for="modificator_{{ $modificator["id"] }}">{{ $modificator["value"] }}</label>
                            @endforeach
                        </p>
                        @endif

                        <p class="ma0">{{ $product->description }}</p>

                        <input type="hidden" name="ingredients" value="{{ implode(', ', $ingredients) }}">
                    </div>
                </div>

                @foreach($sharpness as $sharp)
                    @if($sharp->id == 1172)
                        <div class="bg-black pa1 w2 nested-img absolute top-1 right-1 br-100">
                            <img src="{{ '/storage/img/chili.svg' }}" alt="">
                        </div>

                    @endif

                    @if($sharp->id == 1173)
                        <div class="bg-black pa1 w2 nested-img absolute top-1 right-1 br-100">
                            <img src="{{ '/storage/img/herb.svg' }}" alt="">
                        </div>

                    @endif
                @endforeach

                <div class="flex flex-column">
                    <p  class="self-end f7 fw3 mt2 mb1">{{ number_format($product->weight, '0', ',', ' ') . ' ' . $product->unit }}</p>
                    @if(count($modificators) > 0 )
                        @foreach($modificators as $key => $modificator)
                            <div data-product-controls="99{{ $modificator['id'] }}99" class=" @if(count($modificators) != $key + 1 ) dn  @else flex @endif flex-column flex-row-ns justify-between items-center">
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="uid[]" value="99{{ $modificator['id'] }}99">
                                <input type="hidden" name="modificator_id[]" value="{{ $modificator['id'] }}">
                                <input type="hidden" name="attribute_value_id[]" value="{{ $modificator['attribute_value_id'] }}">
                                <input type="hidden" name="modificator_value[]" value="{{ $modificator['value'] }}">
                                <input type="hidden" name="modificator_price[]" value="{{ $modificator['price'] }}">

                                <button data-control-add type="submit" name="action" value="add" class="@if(Cart::get('99'.$modificator['id']. '99' )) dn @endif mt2 mt0-ns order-2 order-1-ns w4 bg-dark-red tc white pa2 bn br-pill bg-animate hover-bg-red pointer">
                                    В кошик
                                </button>

                                <div data-control-update class="mt2 mt0-ns order-2 order-1-l @if(!Cart::get('99'.$modificator['id']. '99')) dn @endif" >
                                    <div class="flex bg-dark-red w4 white br-pill overflow-hidden ">
                                        <button name="action" value="decrease" type="submit" class="w-third bn  pv2 ph2 bg-inherit white bg-animate hover-bg-red pointer">-</button>
                                        <div data-control-quantity class="w-third tc pv2 white">
                                            @if(Cart::get('99' . $modificator['id'] . '99'))
                                                {{ Cart::get( '99' .$modificator['id'] . '99')['quantity']  }}
                                            @endif
                                        </div>
                                        <button name="action" value="increase" type="submit" class="w-third bn  pv2 ph2 bg-inherit white bg-animate hover-bg-red pointer">+</button>
                                    </div>
                                </div>

                                <div class="order-1 order-2-ns fw5 tl tl-ns"><span class="f2-ns f4">{{ number_format($modificator['price'], '0', ',', ' ') }}</span> <span class="f4"> грн.</span></div>
                            </div>
                        @endforeach
                    @else
                        <div data-product-controls="{{ $product->id }}" class="flex flex-row justify-between items-end items-end-ns">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="uid" value="{{ $product->id }}">

                            <button data-control-add type="submit" name="action" value="add" class="@if(Cart::get($product->id)) dn @endif w4 bg-dark-red tc white pa2 bn br-pill bg-animate hover-bg-red pointer mt2 mt0-ns">
                                В кошик
                            </button>

                            <div data-control-update class="mt2 mt0-ns @if(!Cart::get($product->id)) dn @endif" >
                                <div class="flex bg-dark-red w4 black br-pill overflow-hidden ">
                                    <button name="action" value="decrease" type="submit" class="w-third bn  pv2 ph2 bg-inherit white bg-animate hover-bg-red pointer">-</button>
                                    <div data-control-quantity class="w-third tc pv2 white">
                                        @if(Cart::get($product->id))
                                            {{ Cart::get($product->id)['quantity'] }}
                                        @endif
                                    </div>
                                    <button name="action" value="increase" type="submit" class="w-third bn  pv2 ph2 bg-inherit white bg-animate hover-bg-red pointer">+</button>
                                </div>
                            </div>

                            <div class="fw5 tr ml3"><span class="f2">{{ number_format($product->price, '0', ',', ' ') }}</span> <span class="f4"> грн.</span></div>
                        </div>
                    @endif

                </div>
            </form>




        </div>
    </div>
</div>
