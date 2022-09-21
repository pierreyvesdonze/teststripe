var app = {

    init: function () {

        /**
        * *****************************
        * L I S T E N E R S
        * *****************************
        */
        $('.sidenav').sidenav();

        
        /**
        * *****************************
        * S T R I P E
        * *****************************
        */
        
        //Public key
        let stripe = Stripe('pk_test_51LkS7tD6oSKKF23AkwqKjUVwWWcYWxH7WdCsptsBAfMxEDcypWJa6aQYTLfBvbZvqUQ4kGKq218uso9NNA1JERJF00enUJsnIm')
        let elements = stripe.elements()

        // Page objects
        let cardHolderName = document.getElementById("cardholder-name")
        let cardButton = document.getElementById("card-button")
        let clientSecret = cardButton.dataset.secret;

        // Create card elements
        let card = elements.create("card")
        card.mount("#card-elements")

        // Check inputs
        card.addEventListener("change", (event) => {
            let displayError = document.getElementById("card-errors")
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = "";
            }
        })

        // Payment
        cardButton.addEventListener("click", () => {
            stripe.handleCardPayment(
                clientSecret, card, {
                payment_method_data: {
                    billing_details: { name: cardHolderName.value }
                }
            }
            ).then((result) => {
                if (result.error) {
                    document.getElementById("errors").innerText = result.error.message
                } else {
                    console.log('ça marche');
                    window.location.href = 'http://localhost:8000/pay/thanks/confirmation'
                }
            })
        })
    },
}

document.addEventListener('DOMContentLoaded', app.init)
