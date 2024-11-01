// auth.js
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
  
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
  
    // Mock authentication (replace with real authentication logic)
    if (email === "user@example.com" && password === "password") {
      // Store auth token in localStorage (or sessionStorage)
      localStorage.setItem('authToken', 'sampleToken123');
      // Redirect to index.html after successful login
      window.location.href = 'index.html';
    } else {
      alert('Invalid credentials. Please try again.');
    }
  });
  