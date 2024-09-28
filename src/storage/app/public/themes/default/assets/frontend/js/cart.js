$(document).ready(function(){

    function initTemplates(){
        $.get( PATH_TO_THEME_ASSETS +  "frontend/js/templates/cart.html", function(data){
            /* Compile markup string as a named template */
            $.template( "cartTemplate", data);
        });

        $.get( PATH_TO_THEME_ASSETS +  "frontend/js/templates/cart-in-checkout.html", function(data){
            /* Compile markup string as a named template */
            $.template( "cartInCheckoutTemplate", data );
        });
    }

    function initBindigs(){
        $('[data-buy]').each(function() {
            $(this).validate({
                submitHandler: function (form, event) {
                    event.preventDefault();
                    $(form).ajaxSubmit({
                        dataType: 'json',
                        success: function (res) {
                            console.log(res);
                            $.notify(res.message, res.status);
                            getCart();

                            // if (res.status != 'error') {
                            //     if (res.hasOwnProperty('liqpay')) {
                            //         let liqPayForm = $.parseHTML($.trim(res.html_str))[0];
                            //         $('body').append(liqPayForm);
                            //         liqPayForm.submit();
                            //     } else {
                            //         window.location.href = "/orders/thankyou";
                            //     }
                            // }

                        },
                        error: function (data) {
                            console.log(data);
                            $.notify('Ошибка', 'error')
                        }
                    });
                },
            })
        });


    }

    function updateStats(cartData){
        $('[data-cart-total]').each(function(){
            $(this).html(cartData.total );
        })

        $('[data-cart-total-quantity]').each(function(){
            $(this).html(cartData.totalQuantity);
        })

        if(cartData.products.length < 1){
            $("[data-cart-empty]").removeClass('dn');
            $("[data-cart-not-empty]").addClass('dn');

        }else{
            $("[data-cart-not-empty]").removeClass('dn');
            $("[data-cart-empty]").addClass('dn');
        }

        let products = $('[data-product-controls]');
        console.log(cartData.products);
        products.each(function(){
            let product_id = $(this).attr('data-product-controls');

            if(typeof cartData.products[product_id] === 'undefined') {
                $(this).find('[data-control-add]').removeClass('dn');
                $(this).find('[data-control-update]').addClass('dn');
            }
            else {
                console.log(product_id);
                // does exist
                $(this).find('[data-control-add]').addClass('dn');
                $(this).find('[data-control-quantity]').text(cartData.products[product_id]['quantity']);
                $(this).find('[data-control-update]').removeClass('dn');
            }
        })

        $("[data-cart-clear]").html("");
    }

    function getCart(){
        $.ajax({
            url: "/cart/get",
            dataType: 'json',
            success: function( cartData ) {
                console.log(cartData);

                updateStats(cartData);

                /* Render the named templates */
                $.tmpl( "cartTemplate", Object.values(cartData.products) ).appendTo( "[data-cart-popup-items]" );
                $.tmpl( "cartInCheckoutTemplate", Object.values(cartData.products) ).appendTo( "[data-cart-checkout-items]" );

                initBindigs();
            }
        } );
    }


    initBindigs();
    initTemplates();
    let modificators = document.querySelectorAll('[data-modificator]');
    modificators.forEach(function(elem){
        elem.addEventListener('click', handleClick);
    });

    console.log(modificators)


    function handleClick(e) {
        let productForm = e.target.closest('form');
        let controls = productForm.querySelectorAll('[data-product-controls]');
        console.log(productForm, controls);

        controls.forEach(function(elem){
            if(controls[ e.target.value] == elem ){
                elem.classList.remove('dn');
                elem.classList.add('flex');
            }else{
                elem.classList.add('dn');
                elem.classList.remove('flex');
            }
        })

    }







})

