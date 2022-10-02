var app = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('a:not(.not-anim').on('click', app.loadingAnim);

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
    },


    /**
    * *****************************
    * F U N C T I O N S
    * *****************************
    */
    loadingAnim: () => {
        $('.animation-loading-container').fadeIn().css('display', 'block');
    }
}

document.addEventListener('DOMContentLoaded', app.init)
