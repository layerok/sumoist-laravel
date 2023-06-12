<template>
    <!-- Попап -->
    <div :class="isPopUpOpened ? '' : 'dn overflow-hidden'" >
        <div class="w6 " >
            <div class="flex justify-between bg-dark-red ba br--top br2 b--dark-red ">
                <p class="f4 white mv2 pv1 mh3">Корзина</p>
                <p @click="$emit('closePopUp')" class="mv0 white mv2 pv1 mh3 pointer">&times;</p>
            </div>
            <!-- products container -->
            <div v-if="$store.state.cart.length > 0">
                <div  class=" overflow-y-scroll bg-white" style="max-height: 230px">
                    <!-- Product -->
                    <div v-for="item in $store.state.cart"
                         :key="item.id" class="flex justify-between ph3 pb2 pt2 mt1 bb b--red">
                        <div class="w-80 flex items-start">
                            <div class="w-40 flex-shrink-0">
                                <img :src="(item.image) ? '/storage/' + item.image : item.poster_image" alt="">
                            </div>
                            <div class="flex flex-column dark-red f6 ml2 pl1">
                                <p  class="mt0 mb1">{{ item.name }}</p>
                                <p class="mt0 mb1">{{ item.totalPrice }} грн</p>
                                <p class="mt0 mb1">{{ item.quantity }} шт.</p>
                            </div>
                        </div>
                        <div @click.prevent="removeFromCart(item)"  class="flex justify-end">
                            <p @click.prevent="removeFromCart(item)" class="dark-red mv0 pointer f4">&times;</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white ba br--bottom br2 b--white ">
                    <div class="">
                        <div class="mh3 mv2 pv1" @click="$emit('closePopUp');$store.commit('hideThankYouPage');">
                            <router-link :to="{name: 'checkout', params: { spot: $route.params.spot }}"  class="link db w4 bg-dark-red tc white pa3 bn br-pill bg-animate hover-bg-red pointer">Оформить</router-link>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white ba br--bottom br2 b--white ">
                <div class="ph3 pb2 pt2 mv2">
                    <p class="dark-red mv0">Корзина пуста</p>
                </div>

            </div>
        </div>
    </div>

</template>
<script>
    export default {

        props: {
            isPopUpOpened: {
                default: false
            }
        },
        methods: {
            removeFromCart(item) {
                this.$store.commit('removeFromCart', item);
            }
        },

    }
</script>

