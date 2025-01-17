document.querySelector(".php-email-form").addEventListener("submit", function (event) {
    event.preventDefault();
  
    // Show loading animation
    document.querySelector(".loading").style.display = "block";
    document.querySelector(".error-message").style.display = "none";
    document.querySelector(".sent-message").style.display = "none";
  
    // Prepare form data
    var formData = new FormData(this);
  
    // Send the form data to PHP
    fetch('forms/contact.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      // Hide loading animation
      document.querySelector(".loading").style.display = "none";
  
      // Check for success or error status
      if (data.status === 'success') {
        // Show success message
        document.querySelector(".sent-message").style.display = "block";
        document.querySelector(".sent-message").textContent = 'Your message has been sent. Thank you!';
      } else {
        // Show error message
        document.querySelector(".error-message").textContent = data.message;
        document.querySelector(".error-message").style.display = "block";
      }
    })
    .catch(error => {
      // Show error message in case of failure
      document.querySelector(".loading").style.display = "none";
      document.querySelector(".error-message").textContent = "An error occurred. Please try again later.";
      document.querySelector(".error-message").style.display = "block";
    });
  });
  