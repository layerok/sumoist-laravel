<template>
    <div v-if="!loading"  class="w-100 flex-column">
        <div class="bt b--dark-red flex justify-center ph3">
            <p class="mr2">Текущий ресторан: {{ $store.state.currentSpot.name }}</p>
            <router-link class="pv3" :to="{name: 'home'}">Изменить</router-link>
        </div>
        <nav class="w-100 bt bb b--dark-red mb2 flex ">
            <ul class="w-100 ma0 pv3 list flex justify-between items-center ph2 overflow-y-auto">

                <li v-for="category in categories" class="mv1 flex-shrink-0">
                    <router-link exact-active-class="bg-dark-red" :to="{name: 'categories.show', params: { spot: $route.params.spot, slug: category.slug } }" class="ph3-ns ph1 pv1 br-pill link mr2-ns mr1   hover-bg-dark-red hover-white white ">
                        <span class="ph1">{{ category.name }}</span>
                    </router-link>
                </li>

            </ul>
            <sumoist-cart :isHeaderFixed="isHeaderFixed" v-if="isHeaderFixed"></sumoist-cart>
        </nav>
    </div>
    <div v-else class="flex w-100 justify-center items-center">
            <div class="lds-facebook"><div></div><div></div><div></div></div>
    </div>



</template>


<script>

export default {
    props: ['isHeaderFixed'],
    data() {
        return {
            loading: true,
            categories: null,
            error: null,
            spot:{
                name: 'Маршал'
            }
        };
    },
    created() {
        this.loading = true;
        this.fetchData();
    },
    beforeRouteEnter:function(to, from, next)  {

        let uri = '/api/categories/'+ to.params.spot +'/' + to.params.slug ;

        axios.get(uri).then((response) => {
            next(vm => {

                vm.setData(response.data)
            })

        });
    },
    beforeRouteUpdate: function(to, from, next) {


        let uri = '/api/categories/'+ to.params.spot +'/' + to.params.slug ;
        axios.get(uri).then((response) => {
            this.setData(response.data);
            next();
        });

    },
    methods: {
        fetchData() {
            this.error = this.users = null;
            this.loading = true;
            if(this.$route.params.spot){
                axios
                    .get('/api/categories/'+ this.$route.params.spot)
                    .then(response => {
                        this.loading = false;
                        this.categories = response.data;


                    }).catch(error => {
                    if (error.response.status === 404) {
                        this.$router.push({ name: 'notFound' })
                    }

                });
            }
        },
    }
}
</script>

