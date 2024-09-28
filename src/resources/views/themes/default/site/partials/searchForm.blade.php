<form class="search-bar
             flex items-center
             h-auto absolute bottom-0 right-0 outline-0
             br-pill ba b--orange b--top b--left b--right b--bottom
             bg-black white overflow-hidden"
      method="post"
      action="/#products"
      autocomplete="off"
      style="">
    @csrf
    <input name="word"
           type="text"
           class="search-bar__input white outline-0 placeholder-white  pa3 flex-grow-1 bg-transparent bn"
           placeholder="Пошук" @isset($_POST['word']) value="{{ $_POST['word'] }}" @endisset">

    <button class="search-bar__submit bn bg-orange br-100 " type="submit">
        <svg width="20" height="20" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="black" d="M6.97073 0C10.8144 0 13.9415 3.12707 13.9415 6.97073C13.9415 8.65403 13.3417 10.1998 12.3447 11.4058L17 16.0611L16.0611 17L11.4058 12.3447C10.1998 13.3417 8.65403 13.9415 6.97073 13.9415C3.12707 13.9415 0 10.8144 0 6.97073C0 3.12707 3.12704 0 6.97073 0ZM6.97073 12.6137C10.0823 12.6137 12.6137 10.0823 12.6137 6.97073C12.6137 3.8592 10.0823 1.32776 6.97073 1.32776C3.8592 1.32776 1.32776 3.8592 1.32776 6.97073C1.32776 10.0823 3.8592 12.6137 6.97073 12.6137Z" ></path>
        </svg>
    </button>
</form>
