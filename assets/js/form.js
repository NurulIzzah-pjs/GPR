document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('.php-email-form');
  if (form) {
      form.addEventListener('submit', function (e) {
          e.preventDefault(); // Prevent default form submission

          const formData = new FormData(form);

          fetch('forms/contact.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json()) // Parse as JSON
          .then(data => {
              if (data.status === 'success') {
                  form.reset(); // Reset the form fields
              }
          })
          .catch(error => {
              console.error('Error:', error); // Log any errors
          });
      });
  }
});
