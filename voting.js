document.addEventListener('DOMContentLoaded', function() {
    const likeButtons = document.querySelectorAll('.like-button');
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            button.classList.toggle('clicked');
            button.textContent = button.classList.contains('clicked') ? 'Liked' : 'Like';
            updateLikeCount(button);
            if (button.classList.contains('clicked')) {
                const pictureId = button.getAttribute('data-picture-id');
                executeRecaptcha(pictureId);
            }
        });
    });
});

function updateLikeCount(button) {
    const likeCount = button.nextElementSibling;
    let count = parseInt(likeCount.textContent);
    count = button.classList.contains('clicked') ? count + 1 : count - 1;
    likeCount.textContent = count;
}

function executeRecaptcha(pictureId) {
    grecaptcha.ready(function() {
        grecaptcha.execute('6LejK4IpAAAAAJva1ThSSGXdiSX4ur_VWApibjSZ', { action: 'submit' })
            .then(function(token) {
                likePicture(pictureId, token);
            })
            .catch(function(error) {
                console.error('Error executing reCAPTCHA:', error);
            });
    });
}

function likePicture(pictureId, token) {
    const formData = new FormData();
    formData.append('picture_id', pictureId);
    formData.append('g-recaptcha-response', token);
    fetch('vote.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Picture liked successfully!');
        } else {
            console.error('Error liking picture:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
