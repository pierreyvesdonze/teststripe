var app = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('a:not(.not-anim').on('click', app.loadingAnim);
        $('form').on('submit', app.loadingAnim);
        $('input').on('mouseDown', app.closeLoadingAnim);
        $('form').on('mouseDown', app.closeLoadingAnim);

        /**
         * Materialize init
         */
        $('.sidenav').sidenav();
        $('.dropdown-trigger').dropdown();
        $('select').formSelect();
        $('.modal').modal();
        $('.collapsible').collapsible();
        $('.carousel').carousel({
            indicators: true
        });

            // Fade out flash messages
            setTimeout(() => {
                $('.alert').fadeOut('fast')
            }, 2000);
        
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
    }
}

document.addEventListener('DOMContentLoaded', app.init)
