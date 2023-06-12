@extends('theme::site.app')
@section('title', $page->name)

@section('content')

    <div class="flex justify-center">
        <div class="mw7 ph3 w-100 flex justify-center">
            <div class="flex flex-column items-center mw6">
                <h3 class="f2 white fw4 mt4 tc">Благодарим Вас за заказ!</h3>
                <img src="{{ Asset::load('img/check-mark.png') }}" alt="Благодарим">
                <p class="f3 mt5 tc">"Ваш заказ успешно принят и отправлен в работу!"</p>
                <p class="f5 tc">В ближайшее время Вам перезвонит менеджер для подтверждения заказа. Затем заказ будет подготовлен и отправлен на указанный Вами адрес</p>
            </div>
        </div>
    </div>


@stop
