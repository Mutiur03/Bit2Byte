function show_password() {
  const passwordInput = document.getElementById("login-password");
  const eyeIcon = document.getElementById("login-eye-icon");

  if (!passwordInput || !eyeIcon) {
    return;
  }

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    eyeIcon.textContent = "visibility";
  } else {
    passwordInput.type = "password";
    eyeIcon.textContent = "visibility_off";
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  const message = params.get("message");
  if (message) {
    alert(message);
    const newUrl =
      window.location.pathname + (params.toString() ? "?" + params.toString() : "");
    window.history.replaceState({}, "", newUrl);
  }
});
