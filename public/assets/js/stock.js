var appStock = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.stock-input').on('change', appStock.changeQuantityProductInStock)
    },


    /**
    * *****************************
    * F U N C T I O N S
    * *****************************
    */
    changeQuantityProductInStock: (e) => {
        let quantity     = $(e.currentTarget).val();
        let productId    = $(e.currentTarget).data('id');
        let arrayProduct = {};

        arrayProduct['id']       = productId;
        arrayProduct['quantity'] = quantity;

        // Update stock in Backend
        $.ajax(
            {
                url   : Routing.generate('change_stock_quantity'),
                method: "POST",
                data: JSON.stringify(arrayProduct)
            }).done(function (response) {

                M.toast({
                    html: 'Stock mis à jour !', classes: 'rounded'
                })

                // Remove activ red class on item
                appStock.changeActivRed(e);

            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },

    changeActivRed: (e) => {
        $(e.currentTarget).val() > 0 ? $(e.currentTarget).removeClass('activ-red') : $(e.currentTarget).addClass('activ-red');
    },
}

document.addEventListener('DOMContentLoaded', appStock.init)
