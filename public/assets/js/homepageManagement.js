var appHomepage = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.categoy-order-input').on('keyup', appHomepage.changeCategoryOrder);
        $('.starring-product-switch input:checkbox').on('change', appHomepage.starringProductSwitch);
        $('.starring-product-input').on('keyup', appHomepage.starringProduct);
        $('.bannertop-switch input:checkbox').on('change', appHomepage.bannerTopSwitch);
        $('#home-top-banner-input').on('keyup', appHomepage.bannerTop);
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

    starringProductSwitch: (e) => {
        let isChecked = e.currentTarget.checked;

        $.ajax(
            {
                url: Routing.generate('switch_starring_product_state'),
                method: "POST",
                data: JSON.stringify(isChecked)
            }).done(function (response) {
                if (true == response) {
                    message = 'Mise en avant activée !'
                } else {
                    message = 'Mise en avant désactivée !'
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
    },

    starringProduct: (e) => {
        e.preventDefault();
        console.log(e.type)
        if (e.keyCode === 13) {
            let productId = parseInt($(e.currentTarget).val());
            console.log(productId);

            let message = '';

            if (productId) {
                $.ajax(
                    {
                        url: Routing.generate('starring_product', { 'id': productId }),
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
    },

    bannerTopSwitch: (e) => {
        let isChecked = e.currentTarget.checked;

        $.ajax(
            {
                url: Routing.generate('switch_bannertop_state'),
                method: "POST",
                data: JSON.stringify(isChecked)
            }).done(function (response) {
                if (true == response) {
                    message = 'Bannière top activée !'
                } else {
                    message = 'Bannière top désactivée !'
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
    },

    bannerTop: (e) => {
        if (e.keyCode === 13) {
            let userInput = $('#home-top-banner-input').val()
            console.log(userInput)
            $.ajax(
                {
                    url: Routing.generate('admin_edit_bannertop'),
                    method: "POST",
                    data: JSON.stringify(userInput)
                }).done(function (response) {

                    M.toast({
                        html: 'Message promo mis à jour !',
                        classes: 'rounded'
                    })

                }).fail(function (jqXHR, textStatus, error) {
                    console.log(jqXHR);
                    console.log(textStatus);
                    console.log(error);
                });
        }
    }


}

document.addEventListener('DOMContentLoaded', appHomepage.init)
