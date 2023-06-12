<template>
    <footer class=" flex justify-center mv5">
        <div class="w-100 mw7 ph3 bg-black flex flex-row-l flex-column items-center justify-center">
            <!-- Лого -->
            <a href="" class="nested-img mb3 pb2 mb0-l pb0-l">
                <img src="/img/logosumoist.png" alt="">
            </a>

            <!-- Телефоны -->
            <div v-if="$store.state.currentSpot.id == 1" class=" ml4-l pl3-l flex flex-column">
                <div class="flex items-center mb2">
                    <div class="nested-img mr2"><img src="/img/phone.png" alt=""></div>
                    <a href="tel:+380673732670" target="_blank" class="link white hover-dark-red f6 f5-ns">+380 67‒373‒26‒70</a>
                </div>
                <div class="flex items-center mb2">
                    <div class="nested-img mr2"><img src="/img/phone.png" alt=""></div>
                    <a href="tel:+380991776303" target="_blank" class="link white hover-dark-red f6 f5-ns">+380 99‒177‒63‒03</a>
                </div>
                <div class="flex items-center mb2">
                    <div class="nested-img mr2 "><img src="/img/instagram.png" alt=""></div>
                    <a href="https://www.instagram.com/sumoist_od/" target="_blank" class="f6 f5-ns white hover-dark-red link">@sumoist_od</a>
                </div>

            </div>
            <div v-else>
                <div class="flex items-center mb2">
                    <div class="nested-img mr2 "><img src="/img/phone.png" alt=""></div>
                    <a href="tel:+380981348550" target="_blank" class="link white hover-dark-red f6 f5-ns">+380 98‒134‒85‒50</a>

                </div>
                <div class="flex items-center mb2">
                    <div class="nested-img mr2 "><img src="/img/phone.png" alt=""></div>
                    <a href="tel:+380665228581" target="_blank" class="link white hover-dark-red f6 f5-ns">+380 66‒522‒85‒81</a>
                </div>
                <div class="flex items-center mb2">
                    <div class="nested-img mr2 "><img src="/img/instagram.png" alt=""></div>
                    <a href="https://www.instagram.com/sumoist_2.0/" target="_blank" class="f6 f5-ns white hover-dark-red link">@sumoist_2.0</a>
                </div>
            </div>



        </div>



    </footer>
</template>
