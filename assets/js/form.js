// form.js

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.php-email-form'); // Select your form
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(form); // Collect form data

            fetch('forms/contact.php', {
                method: 'POST',
                body: formData // Send the form data as the request body
            })
            .then(response => {
                console.log(response); // Log the response from the PHP script
                return response.text(); // Proceed to parse response text
            })
            .then(data => {
                alert(data); // Show the response from PHP (success or error)
            })
            .catch(error => {
                console.error('Error:', error); // Log any errors
            });
            
        });
    }
});
