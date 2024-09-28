<template>
    <div>
        <div class="tile">
            <h3 class="tile-title">Аттрибуты товара</h3>
            <div class="tile-body">
                <div class="table-responsive">
                    <div class="tile">
                        <h3 class="tile-title">Аттрибуты</h3>
                        <hr>
                        <div class="tile-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="parent">Выберите аттрибут <span class="m-l-5 text-danger"> *</span></label>
                                        <select id=parent class="form-control custom-select mt-15" v-model="attribute" @change="selectAttribute(attribute)">
                                            <option :value="attribute" v-for="attribute in attributes"> {{ attribute.name }} </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tile" v-if="attributeSelected">
                        <h3 class="tile-title">Добавить аттрибут к продукту</h3>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="values">Выберите значение <span class="m-l-5 text-danger"> *</span></label>
                                    <select id=values class="form-control custom-select mt-15" v-model="value" @change="selectValue(value)">
                                        <option :value="value" v-for="value in attributeValues"> {{ value.value }} </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="valueSelected">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="quantity">Кол-во</label>
                                    <input class="form-control" type="number" id="quantity" v-model="currentQty"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="price">Цена</label>
                                    <input class="form-control" type="text" id="price" v-model="currentPrice"/>
                                    <small class="text-danger">Эта цена будет добавлена к главное цене в публичной части сайта</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-sm btn-primary" @click="addProductAttribute()">
                                    <i class="fa fa-plus"></i> Добавить
                                </button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm">
                        <thead>
                        <tr class="text-center">
                            <th>id</th>
                            <th>Значение</th>

                            <th>Кол-во</th>
                            <th>Цена</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="pa in productAttributes">
                            <td style="width: 5%" class="text-center">{{ pa.attribute_values[0] != undefined ? pa.attribute_values[0].id : "???" }}</td>
                            <td style="width: 25%" class="text-center">{{ pa.attribute_values[0] != undefined ? pa.attribute_values[0].value : "???" }}</td>
                            <td style="width: 20%" class="text-center">{{ pa.quantity}}</td>
                            <td style="width: 25%" class="text-center">{{ pa.price}}</td>
                            <td style="width: 25%" class="text-center">
                                <button class="btn btn-sm btn-danger" @click="deleteProductAttribute(pa)">
                                    <i class="fa fa-trash"></i>
                                </button>
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
        name: "product-attributes",
        props: ['productid'],
        data() {
            return {
                productAttributes: [],
                attributes: [],
                attribute: {},
                attributeSelected: false,
                attributeValues: [],
                value: {},
                valueSelected: false,
                currentAttributeId: '',
                currentValue: '',
                currentAttributeValueId: '',
                currentQty: '',
                currentPrice: '',
            }
        },
        created: function() {
            this.loadAttributes();
            this.loadProductAttributes(this.productid);
        },
        methods: {
            loadProductAttributes(id) {
                let _this = this;
                axios.post('/admin/products/attributes', {
                    id: id
                }).then (function(response){
                    _this.productAttributes = response.data.attributes;
                    console.log(response.data);
                }).catch(function (error) {
                    console.log(error);
                });
            },
            loadAttributes() {
                let _this = this;
                axios.get('/admin/products/attributes/load').then (function(response){
                    _this.attributes = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            selectAttribute(attribute) {
                let _this = this;
                this.currentAttributeId = attribute.id;
                axios.post('/admin/products/attributes/values', {
                    id: attribute.id
                }).then (function(response){
                    _this.attributeValues = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
                this.attributeSelected = true;
            },
            selectValue(value) {
                this.valueSelected = true;
                this.currentValue = value.value;
                this.currentAttributeValueId = value.id;
                this.currentQty = value.quantity;
                this.currentPrice = value.price;
            },
            addProductAttribute() {
                if (this.currentQty === null || this.currentPrice === null) {
                    this.$swal("Error, Some values are missing.", {
                        icon: "error",
                    });
                } else {
                    let _this = this;
                    let data = {
                        attribute_id: this.currentAttributeId,
                        id:  this.currentAttributeValueId,
                        quantity: this.currentQty,
                        price: this.currentPrice,
                        product_id: this.productid,
                    };
                    console.log(data);

                    axios.post('/admin/products/attributes/add', {
                        data: data
                    }).then (function(response){
                        _this.$swal("Success! " + response.data.message, {
                            icon: "success",
                        });
                        _this.currentValue = '';
                        _this.currentQty = '';
                        _this.currentPrice = '';
                        _this.valueSelected = false;
                    }).catch(function (error) {
                        console.log(error);
                    });
                    this.loadProductAttributes(this.productid);
                }
            },
            deleteProductAttribute(pa) {
                let _this = this;
                this.$swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        console.log(pa.id);
                        axios.post('/admin/products/attributes/delete', {
                            id: pa.id,
                        }).then (function(response){
                            if (response.data.status === 'success') {
                                _this.$swal("Success! Product attribute has been deleted!", {
                                    icon: "success",
                                });
                                _this.loadProductAttributes(_this.productid);
                            } else {
                                _this.$swal("Your Product attribute not deleted!");
                            }
                        }).catch(function (error) {
                            console.log(error);
                        });

                    } else {
                        this.$swal("Action cancelled!");
                    }
                });
            }
        }

    }
</script>
