<template>
    <section  class="flex flex-column items-center pt3 ph2 ph0-l">
        <div class="w-100 mw7 flex flex-wrap">

            <div v-for="item in items" :key="item.id" class="pa3-l pa2 w-50 w-50-ns w-33-l ">
                <div class="br3 ba b--red pa3-l pa2 h-100">
                    <div class="flex flex-column pb1 pb2 h-100">
                        <div class="nested-img flex flex-shrink-0 justify-center "  >
                            <img class="self-center align-center-start" style=" width:auto; max-height:220px" :src="(item.image) ? '/storage/' + item.image : item.poster_image" alt="">
                            <!--<div class="h4 contain" :style="{backgroundImage: 'url(' + item.poster_image + ')'}"  ></div>-->
                        </div>
                        <div class="flex flex-column justify-between h-100">
                            <div>
                                <h3 v-text="item.name" class="f3-l f5 fw5 mv3 mb0"></h3>
                                <p v-if="item.ingredients.length > 0" class="f6-l f7 fw5 mb2">{{ item.ingredients }}</p>
                            </div>

                            <div class="flex flex-column">
                                <p v-if="item.weight !== '0.00'" class="self-end f4-l f7 mt2 mb1">{{ parseFloat(item.weight).toFixed(0) }} г</p>
                                <div class="flex flex-column flex-row-l justify-between items-center-l">

                                    <button v-if="!isInCart(item)" @click="addToCart(item)" class="order-2 order-1-l w4 bg-dark-red tc white pa3 bn br-pill bg-animate hover-bg-red pointer">В корзину</button>

                                    <div class="order-2 order-1-l" v-else>
                                        <div class="flex bg-dark-red w4 white br-pill overflow-hidden ">
                                            <button @click="substractFromCart(item)" class="w-third bn  pv3 ph2 bg-inherit white bg-animate hover-bg-red pointer">-</button>
                                            <div class="w-third tc pv3">{{ getItemQuantity(item) }}</div>
                                            <button @click="addToCart(item)" class="w-third bn  pv3 ph2 bg-inherit white bg-animate hover-bg-red pointer">+</button>
                                        </div>
                                    </div>
                                    <div class="order-1 order-2-l fw5 tc tl-ns"><span class="f2">{{ parseFloat(item.price).toFixed(0) }}</span> <span class="f4"> грн.</span></div>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    props: ['items'],
    methods: {
        addToCart(item) {
            this.$store.commit('addToCart', item);
        },
        substractFromCart(item) {
            this.$store.commit('substractFromCart', item);
        },
        isInCart(item) {
            let itemInCart = this.$store.state.cart.filter((elem) => {
                return elem.id === item.id;
            })
            if(itemInCart.length > 0){
                return true;
            }else{
                return false;
            }
        },
        getItemQuantity(item){
            let itemInCart = this.$store.state.cart.filter((elem)=>{ return elem.id === item.id});
            return itemInCart[0].quantity;
        }
    }
}
</script>
