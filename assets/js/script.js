const formSlider = document.getElementById("formSlider");

function showLogin() {
    formSlider.style.transform = "translateX(0)";
}

function showRegister() {
    formSlider.style.transform = "translateX(-33.3333%)";
}

function showForgot() {
    formSlider.style.transform = "translateX(-66.6666%)";
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;

    if (input.type === "password") {
        input.type = "text";
        button.textContent = "Hide";
    } else {
        input.type = "password";
        button.textContent = "Show";
    }
}