<template>
    <div>
        <products-list :items="products"></products-list>
    </div>
</template>

<script>
    export default {
        props: ['spot'],
        data(){
            return {
                products: null
            }
        },
        created() {
            this.fetchData();

        },
        methods: {
            fetchData() {
                this.error = this.users = null;
                this.loading = true;
                if(this.$route.params.spot){

                    axios
                        .get('/api/products/'+ this.$route.params.spot)
                        .then(response => {
                            this.loading = false;
                            this.products = response.data;


                        }).catch(error => {
                        this.loading = false;
                        this.error = error.response.data.message || error.message;
                    });
                }

            }
        }
    }
</script>
