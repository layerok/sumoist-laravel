<template>
    <div>
        <products-list :items="products"></products-list>

    </div>
</template>

<script>

    export default {
        data() {
            return {
                products: null,
                error: null,
                loading: null
            }
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
            setData(data) {
                console.log(data);
                this.products = data.products

            }
        }
    }

</script>
