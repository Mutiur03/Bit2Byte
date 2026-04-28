document.addEventListener("DOMContentLoaded", function () {
  const signupForm = document.getElementById("registerForm");
  if (signupForm) {
    signupForm.addEventListener("submit", async function (e) {
      e.preventDefault();
      const name = signupForm.querySelector('input[type="text"]').value.trim();
      const email = signupForm
        .querySelector('input[type="email"]')
        .value.trim();
      const password = signupForm.querySelector("#signup-password").value;
      const res = await fetch("signup.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `name=${name}&email=${email}&password=${password}`,
      });
      const data = await res.json();
      alert(data.message);
      if (data.success) {
        window.location.href = "login.html";
      }
    });
  }

  const passwordInput = document.getElementById("signup-password");
  const toggleBtn = document.getElementById("toggle-signup-password");
  const eyeIcon = document.getElementById("signup-eye-icon");
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
