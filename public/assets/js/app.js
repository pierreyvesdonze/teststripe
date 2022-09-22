var app = {

    init: function () {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.sidenav').sidenav();
        $('.dropdown-trigger').dropdown();
        $('select').formSelect();

    },
}

document.addEventListener('DOMContentLoaded', app.init)
