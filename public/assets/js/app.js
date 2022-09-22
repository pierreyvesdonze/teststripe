var app = {

    init: function () {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.sidenav').sidenav();
        $('.dropdown-trigger').dropdown();

    },
}

document.addEventListener('DOMContentLoaded', app.init)
