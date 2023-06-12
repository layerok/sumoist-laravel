<template>
    <div :style="([ 'home', 'notFound'].indexOf($route.name) > -1) ? {backgroundImage: 'url(/img/bg.jpg)'} : ''">
        <sumoist-header v-if="!([ 'home', 'notFound'].indexOf($route.name) > -1)"></sumoist-header>
            <div v-if="(['home'].indexOf($route.name) > -1)" class="flex flex-column justify-center items-center vh-100">
                <h1>Выберите ресторан</h1>
                <div class="w-100 mw5 flex justify-between">
                    <a class="f3 pointer purple underline" @click="handleClick(spot)" v-for="spot in $store.state.spots" >{{ spot.name }}</a>
                </div>

            </div>
            <router-view></router-view>
        <sumoist-footer v-if="!(['home', 'notFound'].indexOf($route.name) > -1)"></sumoist-footer>
    </div>


</template>

<script>
    export default {
        props:[],
        data(){
            return {

            }
        },
        created() {
            this.fetchData();
        },
        methods: {
            handleClick(item){
                this.$store.commit('setCurrentSpot', item );
                this.$store.commit('clearCart');
                this.$router.push({name: 'spot', params: { spot: item.slug}});
            },
            fetchData() {
                this.error = this.users = null;
                this.loading = true;
                axios
                    .get('/api/spots/get')
                    .then(response => {
                        console.log(response.data)
                        this.$store.commit('loadSpots', response.data);

                        console.log(this.$store.state.spots);

                    }).catch(error => {
                    this.error = error.response.data.message || error.message;
                });
            }
        }
    }
</script>
