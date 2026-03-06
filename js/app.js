const modal = document.getElementById('modal');
const modalBtnOpen = document.getElementById('btn-change-ad-pswrd');
const modalBtnClose = document.querySelector('.modal-btn-close');
const updatePasswordForm = document.getElementById('form-change-ad-pswrd');
const errorContainer = document.getElementById('error-container');
const input = document.querySelector('.form-field input');
const formLabel = document.querySelector('.form-field .form-label');

document.getElementById('year').textContent = new Date().getFullYear();

const toggleModalAndReset = () => {
    modal.classList.toggle('is-hidden');
    modalBtnOpen.classList.toggle('is-hidden');
    updatePasswordForm.reset();
    errorContainer.textContent = '';

    document.querySelectorAll('.form-field input').forEach(input => {
        const label = input.closest('.form-field').querySelector('.form-label');
        label.style.transform = 'translate(0, 0)';
        label.style.color = '#757575';
    });

    document.querySelectorAll(".toggle-icon-pswrd").forEach((icon) => {
        const passwordInput = this.previousElementSibling;
        icon.textContent = "🔒";
        if (passwordInput && passwordInput.tagName === "INPUT") {
            passwordInput.setAttribute("type", "password");
        }
    });
};

modalBtnOpen.addEventListener('click', toggleModalAndReset);
modalBtnClose.addEventListener('click', toggleModalAndReset);

document.querySelectorAll('.form-field input').forEach(input => {
    input.addEventListener('focusin', () => {
        const label = input.closest('.form-field').querySelector('.form-label');
        label.style.transform = 'translate(-5px, -28px)';
        label.style.color = '#313131';
    });

    input.addEventListener('blur', () => {
        const label = input.closest('.form-field').querySelector('.form-label');
        if (!input.value.trim()) {
            label.style.transform = 'translate(0, 0)';
            label.style.color = '#757575';
        }
    });

    input.addEventListener('input', () => {
        const label = input.closest('.form-field').querySelector('.form-label');
        label.style.transform = 'translate(-5px, -28px)';
        label.style.color = '#313131';
    });
});

document.querySelectorAll(".toggle-icon-pswrd").forEach((icon) => {
    icon.addEventListener("click", function () {
        const passwordInput = this.previousElementSibling;

        if (passwordInput && passwordInput.tagName === "INPUT") {
            const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
            passwordInput.setAttribute("type", type);
            this.textContent = type === "password" ? "🔒" : "🔓";
        }
    });
});

updatePasswordForm.addEventListener('submit', (e) => {
    e.preventDefault();

    errorContainer.textContent = '';

    let login = document.getElementById('login').value.trim();
    let password = document.getElementById('pswrd').value.trim();
    let new_password = document.getElementById('new-pswrd').value.trim();
    let confirm_password = document.getElementById('confirm-pswrd').value.trim();

    if (!login || !password || !new_password || !confirm_password) {
        errorContainer.textContent = 'Please fill in all fields';
        return;
    }

    if (new_password !== confirm_password) {
        errorContainer.textContent = 'New password and confirmation do not match';
        return;
    }

    fetch('ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            login,
            password,
            new_password
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Password changed successfully');
                modalBtnClose.click();
                updatePasswordForm.reset();
            } else {
                errorContainer.textContent = data.message || 'An error occurred.';
            }
        })
        .catch(() => {
            errorContainer.textContent = 'An error occurred during the request.';
        });
});
