var domainUrl = '';

var appCart = {

    initCart: () => {

        // Check env
        if (location.hostname === 'localhost') {
            domainUrl = 'localhost:8000';
        } else {
            domainUrl = 'pydonze.fr/mymarket/public'
        }

        console.log("init cart");

        // Clear Cart after payment validation
        if (window.location.pathname === '/paiement/confirmation') {
            sessionStorage.clear()
        }
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
        $('.select-address-btn').on('click', appCart.selectAddress);
        $('.find-discount-btn').on('click', appCart.applyDiscount);
    },

    getCart: () => {
        let cart = sessionStorage.getItem('cart');

        if (cart == null) {
            return [];
        } else {
            return JSON.parse(cart);
        }
    },

    save: (cart) => {
        sessionStorage.setItem('cart', JSON.stringify(cart));
        appCart.createCart();
    },

    addProductToCart: (e) => {
        M.toast({
            html: 'Article ajouté au panier !', classes: 'rounded'
        })
        let cart          = appCart.getCart();
        let productId     = e.currentTarget.dataset.id;
        let productName   = e.currentTarget.dataset.name;
        let productPrice  = parseFloat(e.currentTarget.dataset.price);
        let productInCart = cart.filter(c => c.id === productId);
        let quantity = 1;

        // Check source of click event
        $(e.currentTarget).hasClass('btn-floating') ? 
            quantity = 1 : quantity = parseInt($('#product-quantity-input').val());

        if (productInCart.length > 0) {
            if (productInCart[0].quantity >= 1) {
                productInCart[0].quantity += quantity;
            }
        } else {
            let newProduct = { 'id': productId, 'name': productName, 'price': productPrice, 'quantity': quantity }
            cart.push(newProduct);
        }
        appCart.save(cart);
    },

    updateCartBackend: (e) => {
        let updateType = $(e.currentTarget).data('type');
        let productId  = $(e.currentTarget).data('index')
        let cartLineId = $(e.currentTarget).data('cartline');
        let cartArray  = {};

        cartArray['cartline'] = cartLineId;
        cartArray['type']     = updateType;

        $.ajax(
            {
                url   : Routing.generate('update_cart'),
                method: "POST",
                data  : JSON.stringify(cartArray)
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

        location.reload();
        app.loadingAnim();
 
        let cartline            = $(e.currentTarget).closest('.cartline-frontend');
        let cart                = appCart.getCart();
        let product             = cart.filter(c => c.id == productId);
        let productLineQuantity = $(e.currentTarget).parent().find($('.cart-quantity'));
        let totalCartline       = $(e.currentTarget).parent().parent().next().find($('.total-cartline'));
        let cartlinePrice       = parseFloat(totalCartline.data('price'));
        let quantity            = parseInt($(productLineQuantity).text());
        let discountAmount      = parseInt($('.discount-amount').data('amount'));
        let totalCart           = parseFloat($('.total-cart').text()).toFixed(2);

        if (updateType == "add") {
            $(productLineQuantity.text(quantity += 1));
            totalCartline.text(parseFloat(quantity * cartlinePrice + ' €').toFixed(2));

            //Update in session
            product[0].quantity += 1;
            appCart.save(cart);
        } else {

            $(productLineQuantity.text(quantity -= 1));
            totalCartline.text(parseFloat(quantity * cartlinePrice + ' €').toFixed(2));

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
        let cart      = appCart.getCart();
        let totalCart = 0;

        $(cart).each(function (index, value) {
            totalCart += value.price * value.quantity;
        })
        return parseFloat(totalCart).toFixed(2);
    },

    createCart: () => {
        let cart         = Object.values(appCart.getCart())
        let dropdownCart = $('.cart-modal-content');
        dropdownCart.empty();

        if (cart.length == 0) {
            $('#dropdown-cart').addClass('is-empty');
            $('<h4/>', {
                text : 'Votre panier est vide.',
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
                    text : value.name,
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
                    text : 'Retirer le produit',
                    class: 'cart-product-front remove-from-cart-session',
                }).attr('data-index', value.id).appendTo(dropdownCart);

                // Separator
                $('<br/>', {
                }).appendTo(dropdownCart);

                // Add quantity of product
                $('<span/>', {
                    text : 'Quantité : ' + value.quantity,
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
                text : 'Total : ' + total + ' €',
                class: 'cart-front-total-price'
            }).appendTo(dropdownCart);
        }
    },

    removeFromCart: (product, cartline) => {

        // Update cart in session
        let cart      = appCart.getCart();
        let productId = $(product.currentTarget).data('index');
            cart      = cart.filter(c => c.id != productId);
        
        // Remove elements from cart
        $(cartline).remove();

        $.ajax(
            {
                url   : Routing.generate('remove_from_cart'),
                method: "POST",
                data  : JSON.stringify(productId)
            }).done(function (response) {
                // Update cart in cart page
                if (window.location.href.indexOf("panier") > -1) {
                    app.loadingAnim();
                    location.reload();
                }

                // If no product in cart in modal 
                if ($('#dropdown-cart').hasClass('is-empty')) {
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
            text : 'Votre panier est vide',
            class: 'empty-cart'
        }).appendTo(cartSection);
    },

    selectAddress: (e) => {
        let address = $(e.currentTarget).data('address');

        $.ajax(
            {
                url: Routing.generate('set_address_session'),
                method: "POST",
                data: JSON.stringify(address)
            }).done(function (response) {

                // Select Card
                let cardDefault = $('.card');
                let card = $(e.currentTarget).closest('.card');

                cardDefault.removeClass('card-address-selected');

                $(card).addClass('card-address-selected');

                // Enable goToOrder btn
                $('.go-to-order').removeClass('disabled');
           
            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    validateCart: () => {
        let cart            = appCart.getCart();
        let arrayProductsId = [];

        cart.forEach(element => {
            arrayProductsId.push(element)
        });

        $.ajax(
            {
                url   : Routing.generate('validate_session_cart'),
                method: "POST",
                data  : JSON.stringify(arrayProductsId)
            }).done(function (response) {
                if ('false' === response) {
                    alert('Merci de vous connecter avant de valider le panier.')
                } else {
                    //Set cart valid
                    sessionStorage.setItem('cartIsValid', true);
                    // Create link to backend Cart
                    appCart.redirectToBackendCart();
                }
            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    applyDiscount: () => {
        let userInputDiscount = $('.discount-input').val();
        let cartId = $('.cart').data('id');
        $.ajax(
            {
                url   : Routing.generate('show_cart', {id:cartId}),
                method: "POST",
                data  : JSON.stringify(userInputDiscount)
            }).done(function (response) {
                // Update cart in front
                location.reload();

            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
                M.toast({
                    html: 'Code promo non trouvé', classes: 'rounded'
                })
            });
    },

    redirectToBackendCart: () => {
        let userId = $('#cart-validate').data('user');
        window.location.replace("http://" + domainUrl + "/panier/voir/" + userId)
    }
}

document.addEventListener('DOMContentLoaded', appCart.initCart)