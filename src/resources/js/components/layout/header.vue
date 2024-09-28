<template>
    <header class=" flex flex-column items-center  ">
        <div class="w-100 mw7 ph3  relative pt3">
            <section class="w-100 flex flex-wrap flex-nowrap-l justify-between mb2">
                <!-- Телефоны -->
                <div v-if="$store.state.currentSpot.id == 1" class="order-0 w-50 flex flex-column">
                    <div class="flex items-center mb2">
                        <div class="nested-img mr2 "><img src="/img/phone.png" alt=""></div>
                        <a href="tel:+380673732670" target="_blank" class="link white hover-dark-red f6 f5-ns">+380 67‒373‒26‒70</a>

                    </div>
                    <div class="flex items-center mb2">
                        <div class="nested-img mr2 "><img src="/img/phone.png" alt=""></div>
                        <a href="tel:+380991776303" target="_blank" class="link white hover-dark-red f6 f5-ns">+380 99‒177‒63‒03</a>
                    </div>
                    <div class="flex items-center mb2">
                        <div class="nested-img mr2 "><img src="/img/instagram.png" alt=""></div>
                        <a href="https://www.instagram.com/sumoist_od/" target="_blank" class="f6 f5-ns white hover-dark-red link">@sumoist_od</a>
                    </div>
                </div>
                <!-- Телефоны -->
                <div v-else class="order-0 w-50 flex flex-column">
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
                <!-- Лого -->
                <div class="w-100 order-1 order-0-l flex justify-center">
                    <div class="flex flex-column items-center">
                        <router-link :to="{name: 'spot', params: {spot: $route.params.spot }}">
                            <img src="/img/logosumoist.png" alt="">
                        </router-link>
                        <p><span class="dark-red">Время работы:</span> 11:00-22:00</p>
                    </div>

                </div>
                <sumoist-cart v-if="!isHeaderFixed"></sumoist-cart>

            </section>
            <div :style="isHeaderFixed ? 'height: 108px' : ''"></div>
            <div :class="isHeaderFixed ? 'fixed' : ''" class="left-0 right-0 top-0 flex justify-center z-4">
                <div :class="isHeaderFixed ? 'ph3-l' : ''" class="mw7 w-100 ">
                    <div class="bg-black flex ">
                        <sumoist-nav  :isHeaderFixed="isHeaderFixed"  ></sumoist-nav>
                    </div>

                </div>

            </div>

            <sumoist-slider v-if="!(['checkout'].indexOf($route.name) > -1) "></sumoist-slider>
        </div>

    </header>
</template>

<script>

    export default {
        props:[],
        data() {
            return{
                isHeaderFixed: false,
            }
        },
        mounted() {
            var ref = this;
            window.onscroll = function () {
                if(window.pageYOffset > 108){
                    ref.isHeaderFixed = true;
                }else {
                    ref.isHeaderFixed = false;
                }
            }
        },
        methods: {

        }
    }

</script>
