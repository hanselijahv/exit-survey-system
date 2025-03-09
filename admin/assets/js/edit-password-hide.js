/**
  * Function to toggle password visibility
  * @author Briones
*/
function togglePassword() {
    const passwordField = document.getElementById("password");
    const toggleIcon = document.getElementById("togglePasswordIcon");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("ti-eye-off");
        toggleIcon.classList.add("ti-eye");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("ti-eye");
        toggleIcon.classList.add("ti-eye-off");
    }
}
