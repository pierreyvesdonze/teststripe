var app = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */

        /**
         * Materialize init
         */
        $('.sidenav').sidenav();
        $('.dropdown-trigger').dropdown();
        $('select').formSelect();
        $('.modal').modal();

        /**
         * Functions
         */
        //$('.add-to-cart-btn').on('click', app.addToCart)
        //$('#cart-nav').on('click', app.createFrontCart)
    },

    addToCart: (e) => {

        M.toast({
            html: 'Article ajouté au panier !', classes: 'rounded'
        })
        let productId = e.currentTarget.dataset.id;
        $.ajax(
            {
                url: Routing.generate('add_to_cart'),
                method: "POST",
                dataType: "json",
                data: productId,
            }).done(function (response) {
                console.log(response);
            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    createFrontCart: () => {
        let dropdownCart = $('.cart-modal-content');
        dropdownCart.empty();
        let totalArray = [];
        $.ajax(
            {
                url: Routing.generate('get_session_cart'),
                method: "POST",
            }).done(function (response) {
                let cartInfos = Object.entries(response);
                console.log(cartInfos);
                console.log(sessionStorage.getItem('totalCart'));

                $(cartInfos).each(function (index, value) {
                    console.log($('.add-to-cart-btn[data-id=' + value[0] + ']'[0]).data('name'));

                    // Product name
                    $('<div/>', {
                        text:
                            $('.add-to-cart-btn[data-id=' + value[0] + ']'[0]).data('name'),
                        class: 'cart-product-front --title'
                    }).appendTo(dropdownCart);

                    // Product quantity
                    $('<div/>', {
                        text: 'Quantité : ' +
                            value[1],
                        class: 'cart-product-front --quantity'
                    }).appendTo(dropdownCart);

                    // Product price
                    $('<div/>', {
                        text: 'Prix : ' +
                            $('.add-to-cart-btn[data-id=' + value[0] + ']'[0]).data('price') * value[1] + ' €',
                        class: 'cart-product-front --price'
                    }).appendTo(dropdownCart);

                    // Separator
                    $('<hr/>', {
                        class: 'hr-cart'
                    }).appendTo(dropdownCart);

                    // Add prices to array for sum
                    totalArray.push($('.add-to-cart-btn[data-id=' + value[0] + ']'[0]).data('price') * value[1])
                })

                // Calculate total of prices
                let total = app.calculateCartTotalSum(totalArray);
               
                // Inject total in modal
                $('<div/>', {
                    text: 'Total : ' + total + ' €',
                    class: 'cart-front-total-price'
                }).appendTo(dropdownCart);



            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    calculateCartTotalSum: (array) => {
        let total = array.reduce((pv, cv) => {
            return pv + (parseFloat(cv) || 0);
        }, 0);

        return total;
    }
}

document.addEventListener('DOMContentLoaded', app.init)
