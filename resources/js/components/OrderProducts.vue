<template>
    <div>
        <div class="tile">
            <h3 class="tile-title">Товары в заказе</h3>
            <div class="tile-body">
                <div class="table-responsive">

                    <table class="table table-sm">
                        <thead>
                        <tr class="text-center">
                            <th>Наименование</th>
                            <th>Значение аттрибута</th>
                            <th>Кол-во</th>
                            <th>Цена</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="op in orderProducts">
                            <td style="width: 25%" class="text-center">{{ op.product.name}}</td>
                            <td style="width: 25%" class="text-center"  >{{ op.attribute_value !== null ? op.attribute_value.value : 'отсутствует' }}</td>
                            <td style="width: 20%" class="text-center">{{ op.quantity}}</td>
                            <td style="width: 20%" class="text-center">{{ op.price}}</td>
                            <td style="width: 10%" class="text-center">

                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "order-products",
        props: ['orderid'],
        data() {
            return {
                orderProducts: [],

            }
        },
        created: function() {
            this.loadOrderProducts(this.orderid);
        },
        methods: {
            loadOrderProducts(id) {
                let _this = this;
                axios.post('/admin/orders/products', {
                    id: id
                }).then (function(response){
                    console.log(response.data);
                    _this.orderProducts = response.data.products;

                }).catch(function (error) {
                    console.log(error);
                });
            }
        }

    }
</script>
