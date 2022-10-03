
var stripePublicKey = '';

var appPayment = {

    /**
       ******************************
     S T R I P E
       ******************************
    */
    init: () => {

        // Check env
        if (location.hostname === 'localhost') {
            domainUrl = 'http://localhost:8000/commande/nouvelle';

            //Public key
            stripePublicKey = 'pk_test_51LkS7tD6oSKKF23AkwqKjUVwWWcYWxH7WdCsptsBAfMxEDcypWJa6aQYTLfBvbZvqUQ4kGKq218uso9NNA1JERJF00enUJsnIm';
        } else {
            domainUrl = 'https://pydonze.fr/mymarket/public/commande/nouvelle'
            
            //Public key
            stripePublicKey = 'pk_live_51LkS7tD6oSKKF23Ar3IQLt7rCW1nVgTWKyNwFaNzW2RzADfNNo8mxfimReuIyF10mcWRzi0342kP7rz4yHRYjkCt002uO1uJef';
        }
        let stripe = Stripe(stripePublicKey)
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
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: { name: cardHolderName.value }
                }
            }
            ).then((result) => {
                if (result.error) {
                    document.getElementById("errors").innerText = result.error.message
                } else {
                    if (location.hostname === 'localhost') {
                        domainUrl = 'http://localhost:8000/commande/nouvelle';
                    } else {
                        domainUrl = 'https://pydonze.fr/mymarket/public/commande/nouvelle'
                    }
                    window.location.href = domainUrl;
                }
            })
        })
    }
}

document.addEventListener('DOMContentLoaded', appPayment.init)