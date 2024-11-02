<div class="ph3-l ph2">
    @php
        $sort_array = [
             1 => [
                 'text' => 'За замовчуванням'
             ],
             2 => [
                 'text' => 'Найдорожчі'
             ],
             3 => [
                 'text' => 'Найдешевші'
             ],
             4 => [
                 'text' => 'Спочатку нові'
             ],
              5 => [
                 'text' => 'Спочатку старі'
             ]
       ];
    @endphp


    <form data-dropdown class="w5-ns w-auto mb2 mt1 pt3 mt2-ml pt4-ml " method="post" action="#products" id="sort_form" >
        @csrf
        <div class="w-100  bg-transparent ba b--dark-red br2 pointer f6 h40  relative flex items-center nested-list-reset "
             style="box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05) inset">
            <input type="hidden" name="catalog_sort_type" value="" data-submit-on-change="true">
            <div data-dropdown-trigger class="flex justify-between items-center w-100 h-100 ph3">
                <div class="current-option">{{ $sort_array[$_SESSION['catalog_sort_type']]['text'] }}</div>
                <div  class="h0 p0 pa1 b--white bw1 bb br bl-0 bt-0 rotate-45  check-mark ease-in-fast mb1"></div>
            </div>
            <ul data-dropdown-list class="  top-100 ba b--dark-red br2 list shadow-2 bg-black mt1 pv1  hidden absolute z-999 left-0 right-0" >
                @foreach($sort_array as $key => $value)

                    <li class="ease-in-fast flex items-center h40 tc ph3 pv2 hover-bg-near-black  @if ($_SESSION['catalog_sort_type'] == $key) fw6 bg-near-black @endif "
                        data-value="{{ $key }}">
                        {{ $value['text'] }}
                    </li>

                @endforeach

            </ul>
        </div>
    </form>
</div>

<script>
    (function(){

        function $(selector, scope = document) {
            return scope.querySelector(selector);
        }

        function listen(type, selector, callback) {
            document.addEventListener(type, event => {
                const target = event.target.closest(selector);

                if (target) {
                    callback(event, target);
                }
            });
        }

        async function enter(element, transition, toggleClass = ['hidden']) {
            toggleClass.forEach(function(elem){
                element.classList.toggle(elem);
            })

            element.classList.remove(`${transition}-leave-end`);

            element.classList.add(`${transition}-enter`);
            element.classList.add(`${transition}-enter-start`);

            // Wait until the transition is over...
            await afterTransition(element);

            element.classList.remove(`${transition}-enter-start`);
            element.classList.add(`${transition}-enter-end`);

            // Wait until the transition is over...
            await afterTransition(element);


            element.classList.remove(`${transition}-enter`);


        }


        async function leave(element, transition, toggleClass = ['hidden']) {



            element.classList.remove(`${transition}-enter-end`);

            element.classList.add(`${transition}-leave`);
            element.classList.add(`${transition}-leave-start`);

            // Wait until the transition is over...
            await afterTransition(element);

            element.classList.remove(`${transition}-leave-start`);
            element.classList.add(`${transition}-leave-end`);

            // Wait until the transition is over...
            await afterTransition(element);


            element.classList.remove(`${transition}-leave`);


            toggleClass.forEach(function(elem){
                element.classList.toggle(elem);
            })


        }

        function afterTransition(element) {
            return new Promise(resolve => {
                const duration = Number(
                    getComputedStyle(element)
                        .transitionDuration
                        .replace('s', '')
                ) * 1000;

                setTimeout(() => {
                    resolve();
                }, duration);
            });
        }

        function nextFrame() {
            return new Promise(resolve => {
                requestAnimationFrame(() => {
                    requestAnimationFrame(resolve);
                });
            });
        }

        // Dropdown

        listen('click', '[data-dropdown-trigger]', openDropdown);
        listen('click', '[data-dropdown-list]', sortProducts);

        function openDropdown(event, dropdownTrigger) {
            const dropdownList = $(
                '[data-dropdown-list]',
                dropdownTrigger.closest('[data-dropdown]')
            );


            const arrowButton = $(
                '.check-mark',
                dropdownTrigger.closest('[data-dropdown]')
            )

            if (!dropdownList.classList.contains('hidden')) {
                return;
            }

            enter(dropdownList, 'fade');
            enter(arrowButton, 'rotate', ['placeholder']);

            function handleClick(event) {
                if (!dropdownList.contains(event.target)) {
                    leave(dropdownList, 'fade');
                    leave(arrowButton, 'rotate', ['placeholder']);

                    window.removeEventListener('click', handleClick);
                }
            }

            window.requestAnimationFrame(() => {
                window.addEventListener('click', handleClick);
            });


        }

        function sortProducts(event, dropdownTrigger){
            const form = dropdownTrigger.closest('[data-dropdown]');
            const input = $('[name="catalog_sort_type"]', form);
            const currentOptionHolder = $('.current-option', form);

            if(!event.target.classList.contains('fw5') && event.target.tagName === 'LI') {

                input.value = event.target.dataset.value;

                currentOptionHolder.innerHTML = event.target.innerHTML;
                console.log(input.dataset.submitOnChange);
                // if(input.dataset.changeAction) {
                //     dropdownList.closest('form').setAttribute('action', dropdownList.closest('form').getAttribute('action')+'/filter/'+input.value)
                //
                // }

                if(input.dataset.submitOnChange){

                    event.preventDefault();
                    console.log(form);
                    form.submit();
                }

            }
        }

    })();

</script>

<style>
    .hidden{
        display: none!important;
    }
    .fade-enter,
    .fade-leave {
        transition: all 0.15s ease;
    }

    .fade-enter-start,
    .fade-leave-end {
        opacity: 0;
    }

    .rotate-enter,
    .rotate-leave {
        transition: all 0.15s ease;
    }

    .rotate-enter-start{
        transform: rotate(-135deg);

    }
    .rotate-enter-end {
        transform: rotate(-135deg)
    }

    .rotate-leave-start{
        transform: rotate(45deg);

    }
    .rotate-leave-end {
        transform: rotate(45deg)
    }




</style>
