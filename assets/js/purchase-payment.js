const stripe = Stripe(stripePublicKey);

let elements;

initialize();

document
    .querySelector("#payment-form")
    .addEventListener("submit", handleSubmit);

async function initialize() {
    elements = stripe.elements({ clientSecret });

    const paymentElement = elements.create("payment", {
        layout: "accordion",
    });

    paymentElement.mount("#payment-element");
}

async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    const { error } = await stripe.confirmPayment({
        elements,
        confirmParams: {
            return_url: redirectAfterSuccessUrl,
        },
    });

    if (error.type === "card_error" || error.type === "validation_error") {
        showMessage(error.message);
    } else {
        showMessage("Une erreur inattendue est survenue.");
    }

    setLoading(false);
}

function showMessage(messageText) {
    const messageContainer = document.querySelector("#payment-message");

    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;

    setTimeout(() => {
        messageContainer.classList.add("hidden");
        messageContainer.textContent = "";
    }, 4000);
}

function setLoading(isLoading) {
    document.querySelector("#submit").disabled = isLoading;
    document.querySelector("#spinner").classList.toggle("hidden", !isLoading);
    document.querySelector("#button-text").classList.toggle("hidden", isLoading);
}
