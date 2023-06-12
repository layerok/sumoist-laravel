<template>
    <div class="flex justify-center">
        <div class="mw7 ph3 w-100">
            <div v-if="$store.state.cart.length > 0">
                <h2 class="tc f2 fw4">Оформление заказа</h2>
                <div class="flex flex-column flex-row-l">
                    <div class="order-2 order-1-l w-40-l pr3-l mt4 mt0-l">

                        <div ><span class="red">Телефон</span> - обязательное поле</div>
                        <form @submit="send" class="mt2" id="checkoutForm" action="" method="post">
                            <div class="flex mb3 pb2">
                                <input v-model="form.deliveryMethod" id="deliveryMethod1" class="checked-bg-dark-red checked-white dn" type="radio" name="deliveryMethod" value="Заказ на вынос" checked >
                                <label class="w-100 bg-white dark-red pa2 tc br2 br--left  pointer" for="deliveryMethod1">Заказ на вынос</label>
                                <input v-model="form.deliveryMethod" id="deliveryMethod2" class="checked-bg-dark-red checked-white dn" type="radio" name="deliveryMethod" value="Доставка">
                                <label class="w-100 bg-white dark-red pa2 tc br2 br--right dark-red pointer" for="deliveryMethod2">Доставка</label>
                            </div>
                            <div class="flex mb3 pb2">
                                <input v-model="form.name" name="name" class=" ph3 pv2 w-100 br2 bn placeholder-dark-red dark-red" type="text" placeholder="Имя">
                            </div>
                            <div class="flex mb3 pb2">
                                <input v-model="form.email" name="email" class=" ph3 pv2 w-100 br2 bn placeholder-dark-red dark-red" type="text" placeholder="Email">
                            </div>
                            <div class="flex flex-column mb3 pb2">
                                <ValidationProvider v-slot="v" rules="validPhone">
                                    <the-mask v-model="form.phone" mask="+38(0##) ###-##-##" value="" type="tel" :masked="false" class=" ph3 pv2 w-100 br2 bn placeholder-dark-red dark-red" placeholder="Телефон"></the-mask>
                                    <span  class="dark-red f6 mt2">{{ v.errors[0] }}</span>
                                </ValidationProvider>

                            </div>
                            <div class="flex mb3 pb2">
                                <input v-model="form.address" name="address" class=" ph3 pv2 w-100 br2 bn placeholder-dark-red dark-red" type="text" placeholder="Адрес доставки">
                            </div>
                            <div class="flex mb3 pb2">
                                <input v-model="form.comment" name="comment" class=" ph3 pv2 w-100 br2 bn placeholder-dark-red dark-red" type="text" placeholder="Комментарий к заказу">
                            </div>
                            <div class="flex mb3 pb2">
                                <input v-model="form.paymentMethod" id="paymentMethod1" class="checked-bg-dark-red checked-white dn" type="radio" name="paymentMethod" value="Наличные"  >
                                <label class="w-100 bg-white dark-red pa2 tc br2 br--left  pointer" for="paymentMethod1">Наличные</label>
                                <input v-model="form.paymentMethod" id="paymentMethod2" class="checked-bg-dark-red checked-white dn" type="radio" name="paymentMethod" value="Картой">
                                <label class="w-100 bg-white dark-red pa2 tc br2 br--right dark-red pointer" for="paymentMethod2">Картой</label>
                            </div>
                            <div class="flex mb3 pb2">
                                <input v-model="form.change" name="change" class=" ph3 pv2 w-100 br2 bn placeholder-dark-red dark-red" type="text" placeholder="Приготовить сдачу с">
                            </div>
                            <div class="flex items-center">
                                <button type="submit"  class="link db w4 bg-dark-red tc white pa3 bn br-pill bg-animate hover-bg-red pointer">
                                    <span v-if="!sending">Оформить</span>
                                    <div v-else style="top: -16px;left: -8px;transform: scale(0.25);width: auto;height: auto;" class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                                </button>
                                <div  class="ml4 fw5 mv0"><span class="f2">{{ totalPrice }}</span> <span class="f4"> грн.</span></div>
                            </div>
                            <div class="mt3 dark-red" v-if="error" v-text="error"></div>



                        </form>
                    </div>
                    <div class="order-1 order-2-l w-100 w-60-l pa2 ba b--dark-red overflow-y-scroll self-start" style="max-height: 520px " >
                        <!-- products container -->
                        <div class="flex flex-column f3 ">
                            <h3 class="dn-ns f3 fw5 mt0 pl2">Мой заказ:</h3>
                            <div v-for="item in $store.state.cart" class="flex relative mb3">
                                <div @click.prevent="removeFromCart(item)" class="white absolute right-0 top-0 pointer">&times;</div>
                                <div class="w-40 nested-img dn db-ns">
                                    <img :src="item.poster_image" style="max-height:150px; width:auto" :alt="item.name">
                                </div>
                                <div class="w-100 w-60-ns pl2 flex flex-column-ns flex-row justify-between">
                                    <div>
                                        <h3 class="f5 f3-ns fw5 mt0 mb2-ns mb0">{{ item.name }}</h3>
                                        <p class="fw5 mv0 f5 dn-ns">{{ item.quantity }} шт</p>
                                    </div>
                                    <p v-if="item.ingredients.length > 0" class="f6 fw5 mt0 mb3 dn db-ns">{{ item.ingredients }}</p>
                                    <div class="flex justify-between items-end-ns items-start">
                                        <div class=" fw5 mv0 mr4 mr0-ns"><span class="f2-ns f4">{{ parseFloat(item.price).toFixed(0) }}</span> <span class="f4-ns f6"> грн.</span></div>
                                        <p class="fw5 mv0 f5 dn db-ns">{{ item.quantity }} шт</p>
                                    </div>
                                    <p class="f4 mv0 dn db-ns">{{ parseFloat(item.weight) }} г</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 v-else class="tc f2 fw4">Корзина пуста :(</h2>
        </div>
    </div>
</template>

<script>

    import {TheMask} from "vue-the-mask";
    import { ValidationProvider, extend } from 'vee-validate';
    extend('validPhone', value => {

        if(/^[0-9]{9}$/.test(value)){
            return true;
        }

        return 'Введите действительный номер';
    });

    export default {
        components: {TheMask, ValidationProvider},
        data() {
            return {
                error: false,
                sending: false,
                sent: false,
                form: {
                    name: null,
                    email: null,
                    phone: null,
                    comment: null,
                    address: null,
                    change: null,
                    deliveryMethod: 'Заказ на вынос',
                    paymentMethod: 'Наличные'
                }

            }
        },
        methods: {
            removeFromCart(item) {
                this.$store.commit('removeFromCart', item);
            },
            send(){
                this.sending = true;
                event.preventDefault();


                const str = JSON.stringify(
                    {
                        formData: this.form,
                        cartData: this.$store.state.cart,
                        spot_slug: this.$route.params.spot,
                    }
                );

                axios.post('/api/orders/send', str)
                    .then((response) => {
                        this.sending = false;
                        let json = JSON.parse(response.data);

                        if(json.hasOwnProperty('error')){
                            this.error = json.message;
                        }
                    })
                    .catch((error) => {
                        this.sending = false;
                        console.log(error);
                    });
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
