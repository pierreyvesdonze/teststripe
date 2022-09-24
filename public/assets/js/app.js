var app = {

    init: () => {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */

        /**
         * Materialize init
         */
        $('.sidenav').sidenav();
        $('.dropdown-trigger').dropdown();
        $('select').formSelect();
        $('.modal').modal();

        /**
         * Functions
         */
    },
}

document.addEventListener('DOMContentLoaded', app.init)
