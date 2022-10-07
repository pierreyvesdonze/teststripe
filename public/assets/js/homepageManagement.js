var appHomepage = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.categoy-order-input').on('keyup', appHomepage.changeCategoryOrder);
        $('.starring-product-input').on('keyup', appHomepage.starringProduct)

    },


    /**
    * *****************************
    * F U N C T I O N S
    * *****************************
    */
    changeCategoryOrder: (e) => {
        e.preventDefault();

        if (e.keyCode === 13) {
            let categId = $(e.currentTarget).data('id');
            let orderValue = parseInt($(e.currentTarget).val());
            let categArr = {};

            categArr['categId'] = categId;
            categArr['orderValue'] = orderValue;

            if (orderValue) {
                $.ajax(
                    {
                        url: Routing.generate('change_category_order'),
                        method: "POST",
                        data: JSON.stringify(categArr)
                    }).done(function (response) {

                        M.toast({
                            html: 'Ordre modifié !', classes: 'rounded'
                        })

                    }).fail(function (jqXHR, textStatus, error) {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(error);
                    });
            } else {
                M.toast({
                    html: 'Valeur invalide!', classes: 'rounded'
                })
            }
        }
    },

    starringProduct: (e) => {
        e.preventDefault();

        if (e.keyCode === 13) {
            let productId = parseInt($(e.currentTarget).val());
            console.log(productId);

            let message = '';

            if (productId) {
                $.ajax(
                    {
                        url: Routing.generate('starring_product', {'id':productId}),
                        method: "POST",
                        data: JSON.stringify(productId)
                    }).done(function (response) {
                        if (true == response) {
                            $('#starringProductId').text(productId).removeClass('activ-red').addClass('activ-green');
                            message = 'Produit mis en avant !'
                        } else {
                            message = 'Produit non trouvé...'
                        }
                        M.toast({
                            html: message,
                            classes: 'rounded'
                        })

                    }).fail(function (jqXHR, textStatus, error) {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(error);
                    });
            } else {
                M.toast({
                    html: 'Valeur invalide!', classes: 'rounded'
                })
            }
        }
    }
    
}

document.addEventListener('DOMContentLoaded', appHomepage.init)
