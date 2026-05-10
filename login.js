document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const message = params.get("message");
  if (message) {
    alert(message);
  }

  const passwordInput = document.getElementById("login-password");
  const toggleBtn = document.getElementById("toggle-login-password");
  const eyeIcon = document.getElementById("login-eye-icon");
  if (passwordInput && toggleBtn && eyeIcon) {
    toggleBtn.addEventListener("click", function () {
      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.textContent = "visibility";
      } else {
        passwordInput.type = "password";
        eyeIcon.textContent = "visibility_off";
      }
    });
  }
});
