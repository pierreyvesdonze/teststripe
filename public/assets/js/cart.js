var appCart = {

    initCart: () => {

        console.log("init cart");
        //sessionStorage.clear()

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.add-to-cart-btn').on('click', appCart.addProductToCart);
        $('#cart-nav').on('click', appCart.createCart);
        $('#cart-validate').on('click', appCart.validateCart);
        $('.remove-one-product-btn').on('click', appCart.updateCartBackend);
        $('.add-one-product-btn').on('click', appCart.updateCartBackend);
    },

    save: (cart) => {
        sessionStorage.setItem('cart', JSON.stringify(cart));
        appCart.createCart();
    },

    getCart: () => {
        let cart = sessionStorage.getItem('cart');

        if (cart == null) {
            return [];
        } else {
            return JSON.parse(cart);
        }
    },

    addProductToCart: (product) => {
        M.toast({
            html: 'Article ajouté au panier !', classes: 'rounded'
        })
        let cart = appCart.getCart();
        let productId = product.currentTarget.dataset.id;
        let productName = product.currentTarget.dataset.name;
        let productPrice = parseFloat(product.currentTarget.dataset.price);
        let productInCart = cart.filter(c => c.id === productId);

        if (productInCart.length > 0) {
            if (productInCart[0].quantity >= 1) {
                productInCart[0].quantity += 1;
            }
        } else {
            let newProduct = { 'id': productId, 'name': productName, 'price': productPrice, 'quantity': 1 }
            cart.push(newProduct);
        }
        appCart.save(cart);
    },

    updateCartBackend: (e) => {
        let updateType = $(e.currentTarget).data('type');
        let productId = $(e.currentTarget).parent().data('id');
        let cartLineId = $(e.currentTarget).parent().data('cartline');
        let cartArray = {};

        cartArray['cartline'] = cartLineId;
        cartArray['type'] = updateType;

        $.ajax(
            {
                url: Routing.generate('update_cart'),
                method: "POST",
                data: JSON.stringify(cartArray)
            }).done(function (response) {

                // Update cart in front
                appCart.updateCartFrontend(e, updateType, productId);

            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    updateCartFrontend: (e, updateType, productId) => {
        let cartline = $(e.currentTarget).closest('.cartline-frontend');
        let cart = appCart.getCart();
        let product = cart.filter(c => c.id == productId);
        let productLineQuantity = $(e.currentTarget).parent().find($('.cart-quantity'));
        let totalCartline = $(e.currentTarget).parent().parent().next().find($('.total-cartline'));
        let cartlinePrice = parseFloat(totalCartline.data('price'));
        let quantity = parseInt($(productLineQuantity).text());
        let totalCart = parseFloat($('.total-cart').text()).toFixed(2);

        if (updateType == "add") {
            $(productLineQuantity.text(quantity += 1));
            totalCartline.text(quantity * cartlinePrice + ' €');

            //Update in session
            product[0].quantity += 1;
            appCart.save(cart);
        } else {
            $(productLineQuantity.text(quantity -= 1));
            totalCartline.text(quantity * cartlinePrice + ' €');
        
            //Update in session
            product[0].quantity -= 1;
            appCart.save(cart);
        }

        totalCart = appCart.sumTotalCart();
        $('.total-cart').text(totalCart);
        
        if (quantity === 0) {
            appCart.removeFromCart(e, cartline)
        }
    },

    sumTotalCart: () => {
        let cart = appCart.getCart();
        let totalCart = 0;
        $(cart).each(function (index, value) {
            totalCart += value.price * value.quantity
        })
        return totalCart;
    },

    createCart: () => {
        let cart = Object.values(appCart.getCart())
        let dropdownCart = $('.cart-modal-content');
        dropdownCart.empty();

        if (cart.length == 0) {
            $('<h4/>', {
                text: 'Votre panier est vide.',
                class: 'modal-empty-cart'
            }).appendTo(dropdownCart);

            $('#cart-validate').addClass('disabled');

        } else {

            $('#cart-validate').removeClass('disabled');

            // Create Link to Cart if Cart is validated by user
            sessionStorage.setItem('cartIsValid', true);

            $(cart).each(function (index, value) {

                // Product name
                $('<h5/>', {
                    text: value.name,
                    class: 'cart-product-front --title'
                }).appendTo(dropdownCart);

                // Product price
                $('<span/>', {
                    text: 'Prix : ' +
                        value.price + ' €',
                    class: 'cart-product-front --price'
                }).appendTo(dropdownCart);

                // Add input hidden for index product
                $('<input/>', {
                    type: 'hidden',
                }).attr('data-index', index).appendTo(dropdownCart);

                // Remove product from cart
                $('<span/>', {
                    text: 'Retirer le produit',
                    class: 'cart-product-front remove-from-cart-session',
                }).attr('data-index', value.id).appendTo(dropdownCart);

                // Separator
                $('<br/>', {
                }).appendTo(dropdownCart);

                // Add quantity of product
                $('<span/>', {
                    text: 'Quantité : ' + value.quantity,
                    class: 'cart-product-front qtyProductInCart',
                }).attr('data-quantity', value.quantity).appendTo(dropdownCart);

                // Separator
                $('<hr/>', {
                    class: 'hr-cart',
                }).appendTo(dropdownCart);
            })

            // Add listener for removing product from cart
            $('.remove-from-cart-session').on('click', appCart.removeFromCart);

            // Calculate total of prices
            let total = appCart.sumTotalCart();

            // Inject total in modal
            $('<div/>', {
                text: 'Total : ' + total + ' €',
                class: 'cart-front-total-price'
            }).appendTo(dropdownCart);
        }
    },

    removeFromCart: (product, cartline) => {

        // Remove elements from cart
        $(cartline).remove();
        
        // Update cart in session
        let cartLineId = $(product.currentTarget).parent().data('cartline');

        let cart = appCart.getCart();
        let productId = $(product.currentTarget).data('index');
        cart = cart.filter(c => c.id != productId);

        $.ajax(
            {
                url: Routing.generate('remove_from_cart'),
                method: "POST",
                data: JSON.stringify(cartLineId)
            }).done(function (response) {

                // If no product in cart...
                if ($.trim($(".cart ul").html()) == '') {
                    appCart.clearCart()
                }

            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });

        appCart.save(cart);
    },

    clearCart: () => {
        let cartSection = $('.cart');
        cartSection.empty();

        $('<h1/>', {
            text: 'Votre panier est vide',
            class: 'empty-cart'
        }).appendTo(cartSection);
    },

    validateCart: () => {
        let cart = appCart.getCart();
        let arrayProductsId = [];
        cart.forEach(element => {
            arrayProductsId.push(element)
        });

        $.ajax(
            {
                url: Routing.generate('validate_session_cart'),
                method: "POST",
                data: JSON.stringify(arrayProductsId)
            }).done(function (response) {
                if ('false' === response) {
                    alert('Merci de vous connecter avant de valider le panier.')
                } else {
                    //Set cart valid
                    sessionStorage.setItem('cartIsValid', true);
                    // Create link to backend Cart
                    appCart.redirectToBackendCart()
                }
            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    redirectToBackendCart: () => {
        let userId = $('#cart-validate').data('user');
        window.location.replace("http://localhost:8000/cart/show/" + userId)
    }
}

document.addEventListener('DOMContentLoaded', appCart.initCart)