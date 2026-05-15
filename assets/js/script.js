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

function nextRegisterStep() {
    document.getElementById("registerStep1").classList.remove("active");
    document.getElementById("registerStep1").style.display = "none";

    document.getElementById("registerStep2").classList.add("active");
    document.getElementById("registerStep2").style.display = "block";
}

function prevRegisterStep() {
    document.getElementById("registerStep2").classList.remove("active");
    document.getElementById("registerStep2").style.display = "none";

    document.getElementById("registerStep1").classList.add("active");
    document.getElementById("registerStep1").style.display = "block";
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