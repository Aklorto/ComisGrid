const loginForm = document.getElementById('loginForm');
const registerForm = document.getElementById('registerForm');
const forgotForm = document.getElementById('forgotForm');

const showRegister = document.getElementById('showRegister');
const showLogin = document.getElementById('showLogin');
const showForgot = document.getElementById('showForgot');
const backToLogin = document.getElementById('backToLogin');

// Client-side register validation: confirm password
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        const pwd = document.getElementById('registerPassword');
        const confirm = document.getElementById('registerConfirmPassword');
        if (pwd && confirm && pwd.value !== confirm.value) {
            e.preventDefault();
            alert('Passwords do not match.');
            confirm.focus();
        }
    });
}

function setActiveForm(targetForm) {
    const forms = [loginForm, registerForm, forgotForm];

    forms.forEach(form => {
        form.classList.remove('active', 'slide-left');
    });

    if (targetForm === registerForm) {
        loginForm.classList.add('slide-left');
    }

    if (targetForm === forgotForm) {
        loginForm.classList.add('slide-left');
    }

    targetForm.classList.add('active');
}

showRegister.addEventListener('click', () => setActiveForm(registerForm));
showLogin.addEventListener('click', () => setActiveForm(loginForm));
showForgot.addEventListener('click', () => setActiveForm(forgotForm));
backToLogin.addEventListener('click', () => setActiveForm(loginForm));
