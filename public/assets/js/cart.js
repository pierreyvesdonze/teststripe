var appCart = {

    initCart: () => {

        //cart.getCart;
        console.log("init cart");
        //sessionStorage.clear()

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.add-to-cart-btn').on('click', appCart.add);
        $('#cart-nav').on('click', appCart.createCart);

    },

    save: (cart) => {
        console.log(cart);
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
        let productId = product.currentTarget.dataset.id;
        let productName = product.currentTarget.dataset.name;
        let productPrice = parseInt(product.currentTarget.dataset.price);
        
        let newProduct = {'id':productId, 'name':productName, 'price': productPrice}

        let cart = appCart.getCart();

        cart.push(newProduct);
        appCart.save(cart);
    },

    createCart: () => {
        let cart = Object.values(appCart.getCart())
        let dropdownCart = $('.cart-modal-content');
        dropdownCart.empty();
        let totalArray = [];
        console.log(cart);

        $(cart).each(function (index, value) {
            
            // Product name
            $('<div/>', {
                text: value.name,
                class: 'cart-product-front --title'
            }).appendTo(dropdownCart);

            // Product quantity
            $('<div/>', {
                text: 'Prix : ' +
                    value.price + ' €',
                class: 'cart-product-front --price'
            }).appendTo(dropdownCart);

            // Separator
            $('<hr/>', {
                class: 'hr-cart'
            }).appendTo(dropdownCart);

            // Add prices to array for sum
            totalArray.push(value.price)
        })
        
        console.log(totalArray);

        // Calculate total of prices
        let total = app.calculateCartTotalSum(totalArray);

        // Inject total in modal
        $('<div/>', {
            text: 'Total : ' + total + ' €',
            class: 'cart-front-total-price'
        }).appendTo(dropdownCart);
    },

    calculateCartTotalSum: (array) => {
        var sum = array.reduce(function (a, b) {
            return a + b;
        }, 0);

        return sum;
    },

    remove: (product) => {
        let cart = appCart.getCart();
        cart = cart.filter(c => c.id != product.id)
        appCart.save(cart);
    },

    changeQuantity: (product, quantity) => {
        let cart = cart.getCart();
        let foundProduct = cart.find(c => c.id == product.id);
        if (foundProduct != undefined) {
            foundProduct.quantity += quantity;
            if (foundProduct.quantity <= 0) {
                appCart.remove(foundProduct);
            } else {
                appCart.save(cart);
            }
        }
    },

    getNumberOfProduct: () => {
        let cart = appCart.getCart();
        let number = 0;
        for (let product of cart) {
            number += product.quantity
        }
        return number;
    },

    getTotalPrice: () => {
        let cart = appCart.getCart();
        let total = 0;
        for (let product of cart) {
            total += product.quantity * product.price
            console.log(product.quantity);
            console.log(product.price);
        }
        return total;
    }
}

document.addEventListener('DOMContentLoaded', appCart.initCart)