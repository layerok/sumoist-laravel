
<template>
    <!-- Корзина -->
    <div :class="isHeaderFixed ? 'w-auto flex-shrink-0 mh3 pv2' : 'w-50' " class="order-0 flex items-center justify-end relative">
        <div class="flex">
            <div class="nested-img pointer" @click="isPopUpOpened = !isPopUpOpened">
                <img src="/img/cart.png" alt="Корзина">
            </div>
            <div v-if="!isHeaderFixed" class="flex flex-column justify-between ml3-ns pl2">
                <div class="f4">{{ totalPrice }} грн.</div>
                <div>{{ $store.state.cartCount }} товаров</div>
            </div>
            <div class="absolute" style="right:-1px;top:-1px" v-else>
                <div class="f7 red bg-white br-100 flex justify-center items-center" style="width:24px;height:24px; ">
                    {{ $store.state.cartCount }}
                </div>

            </div>
        </div>
        <div class="absolute right-0 top-3 z-4">
            <sumoist-cart-pop-up @closePopUp="isPopUpOpened = false" :isPopUpOpened="isPopUpOpened"></sumoist-cart-pop-up>

        </div>


    </div>
</template>

<script>
    export default {
        props:['isHeaderFixed'],
        data() {
            return {
                isPopUpOpened: false
            }
        },
        computed: {
            totalPrice() {
                let total = 0;

                for (let item of this.$store.state.cart) {
                    total += item.totalPrice;
                }

                return total;
            }
        }
    }
</script>
