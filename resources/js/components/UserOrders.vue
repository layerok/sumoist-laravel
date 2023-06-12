<template>
    <div>
        <div class="tile">
            <h3 class="tile-title">Заказы пользователя</h3>
            <div class="tile-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr >
                            <th>Номер заказа</th>
                            <th>Сумма</th>
                            <th>Создан</th>
                            <th class="text-center">Отправлен на постер</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="order in userOrders">
                            <td style="width: 25%" ><a title="Просмотреть" :href="'/admin/orders/edit/' + order.id">#{{ order.id }}</a>  </td>
                            <td style="width: 25%" >     {{ order.sum }}</td>
                            <td style="width: 20%" >     {{ order.created_at }}</td>
                            <td class="text-center" style="width: 20%" >     {{ order.is_sent_to_poster == 1 ? 'Да': 'Нет' }}</td>

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
        name: "user-orders",
        props: ['userid'],
        data() {
            return {
                userOrders: [],

            }
        },
        created: function() {
            this.loadUserOrders(this.userid);

        },
        methods: {
            loadUserOrders(id) {
                let _this = this;
                axios.post('/admin/users/' + _this.userid + '/orders', {
                    id: id
                }).then (function(response){
                    console.log(response.data);
                    _this.userOrders = response.data;

                }).catch(function (error) {
                    console.log(error);
                });
            }
        }

    }
</script>
