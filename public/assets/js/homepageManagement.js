var appHomepage = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.categoy-order-input').on('keyup', appHomepage.changeCategoryOrder)
      
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
    }
    
}

document.addEventListener('DOMContentLoaded', appHomepage.init)
