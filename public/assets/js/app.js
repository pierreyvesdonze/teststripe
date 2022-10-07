var app = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.add-product-btn').on('submit', app.loadingAnim);
        $('.delete-img').on('click', app.deleteImg);
        $(':submit').click(app.loadingAnim);

        /**
         * Materialize init
         */
        $('.sidenav').sidenav();
        $('.dropdown-trigger').dropdown();
        $('select').formSelect();
        $('.modal').modal();
        $('.collapsible').collapsible();
        $('.parallax').parallax();
        $('.carousel').carousel({
            indicators: true
        });

        // Fade out flash messages
        setTimeout(() => {
            $('.alert').fadeOut('fast')
        }, 3000);

        // If Spinner anim, disabled it onload
        app.closeLoadingAnim();
    },


    /**
    * *****************************
    * F U N C T I O N S
    * *****************************
    */
    loadingAnim: () => {
        $('.animation-loading-container').fadeIn().css('display', 'block');
    },

    closeLoadingAnim: () => {
        setTimeout(() => {
            $('.animation-loading-container').fadeIn().css('display', 'none');
        }, 2000);
    },

    deleteImg: (e) => {
        let imgName = $(e.currentTarget).data('imgname');
        let imgnb   = $(e.currentTarget).data('imgnb');
        let imgArr  = {};

        imgArr['imgname'] = imgName;
        imgArr['imgnb']   = imgnb;

        $.ajax(
            {
                url: Routing.generate('delete_product_img'),
                method: "POST",
                data: JSON.stringify(imgArr)
            }).done(function (response) {
             
                $(e.currentTarget).remove()

            }).fail(function (jqXHR, textStatus, error) {
                console.log(jqXHR);
                console.log(textStatus);
                console.log(error);
            });
    },
}

document.addEventListener('DOMContentLoaded', app.init)
