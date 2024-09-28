@extends('theme::site.app')
@section('title', $page->name)

@section('content')

    <div class="flex justify-center">
        <div class="mw7 ph3 w-100 flex justify-center">
            <div class="flex flex-column items-center mw6">
                <h3 class="f2 white fw4 mt4 tc">Дякуємо Вам за замовлення!</h3>
                <img src="{{ Asset::load('img/check-mark.png') }}" alt="Благодарим">
                <p class="f3 mt5 tc">"Ваше замовлення успішно прийнято та відправлено в роботу!"</p>
                <p class="f5 tc">Найближчим часом Вам зателефонує менеджер для підтвердження замовлення. Потім замовлення буде підготовлено та надіслано на вказану Вами адресу</p>
            </div>
        </div>
    </div>


@stop
