document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const email = loginForm.querySelector('input[type="email"]').value.trim();
      const password = loginForm.querySelector('input[type="password"]').value;
      const res = await fetch("login.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `email=${email}&password=${password}`,
      });
      const data = await res.json();
      alert(data.message);
      if (data.success) {
        window.location.href = "index.html";
      }
    });
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
