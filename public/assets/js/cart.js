var appCart = {
    
    initCart: () => {

        console.log("init cart");
        //sessionStorage.clear()
      
        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.add-to-cart-btn').on('click', appCart.add);
        $('#cart-nav').on('click', appCart.createCart);
        $('#cart-validate').on('click', appCart.validateCart);
    },

    save: (cart) => {
        appCart.createCart();
        sessionStorage.setItem('cart', JSON.stringify(cart));
    },

    getCart: () => {
        let cart = sessionStorage.getItem('cart');

        if (cart == null) {
            return [];
        } else {
            return JSON.parse(cart);
        }
    },

    add: (product) => {
        M.toast({
            html: 'Article ajouté au panier !', classes: 'rounded'
        })
        let productId = product.currentTarget.dataset.id;
        let productName = product.currentTarget.dataset.name;
        let productPrice = parseInt(product.currentTarget.dataset.price);

        let newProduct = { 'id': productId, 'name': productName, 'price': productPrice, 'sessionId': Date.now() }

        let cart = appCart.getCart();

        cart.push(newProduct);
        appCart.save(cart);
    },

    createCart: () => {
        let cart = Object.values(appCart.getCart())
        let dropdownCart = $('.cart-modal-content');
        dropdownCart.empty();
        let totalArray = [];

        if (cart.length == 0) {
            $('<p/>', {
                text: 'Votre panier est vide ou votre session a expiré.',
                class: 'modal-empty-cart'
            }).appendTo(dropdownCart);

            $('<p/>', {
                text: 'Si vous avez validé un panier, connectez-vous et rendez vous dans la rubrique "Mon compte" pour voir votre panier en cours.',
                class: 'modal-empty-cart'
            }).appendTo(dropdownCart);

            $('#cart-validate').addClass('disabled');
        } else {
            $('#cart-validate').removeClass('disabled');

            $(cart).each(function (index, value) {

                // Product name
                $('<h5/>', {
                    text: value.name,
                    class: 'cart-product-front --title'
                }).appendTo(dropdownCart);

                // Product quantity
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
                }).attr('data-index', value.sessionId).appendTo(dropdownCart);

                // Separator
                $('<hr/>', {
                    class: 'hr-cart',
                }).appendTo(dropdownCart);

                // Add prices to array for sum
                totalArray.push(value.price)
            })

            // Add listener for removing product from cart
            $('.remove-from-cart-session').on('click', appCart.removeFromCart);

            // Calculate total of prices
            let total = appCart.calculateCartTotalSum(totalArray);

            // Inject total in modal
            $('<div/>', {
                text: 'Total : ' + total + ' €',
                class: 'cart-front-total-price'
            }).appendTo(dropdownCart);

            sessionStorage.getItem('cartIsValid') == true ? appCart.showBackendLinkToCart() : sessionStorage.setItem('cartIsValid', false);
        }
    },

    calculateCartTotalSum: (array) => {
        var sum = array.reduce(function (a, b) {
            return a + b;
        }, 0);

        return sum;
    },

    removeFromCart: (product) => {
        let cart = appCart.getCart();
        cart = cart.filter(c => c.sessionId != product.currentTarget.dataset.index)
        appCart.save(cart);
        appCart.createCart();
    },

    validateCart: () => {
        let cart = appCart.getCart();
        let arrayProductsId = [];
        cart.forEach(element => {
            arrayProductsId.push(element['id'])
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
                    sessionStorage.setItem('cartIsVald', true);
                    // Create link to backend Cart
                    appCart.showBackendLinkToCart()
                }
                console.log(response);
            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    showBackendLinkToCart: () => {
        M.toast({
            html: 'Panier enregistré !', classes: 'rounded'
        })

        let userId = $('#cart-validate').data('user');
        // window.location.assign = 'http://localhost:8000/cart/show/' + userId;

        let footerCart = $('#modal-footer-cart');

        // Link to cart in modal
        $('<a/>', {
            'href': "http://localhost:8000/cart/show/"+userId,
            text: 'Voir le panier',
            class: 'waves-effect waves-green btn-flat'
        }).appendTo(footerCart);
    }
}

document.addEventListener('DOMContentLoaded', appCart.initCart)