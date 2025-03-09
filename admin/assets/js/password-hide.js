/**
  * This function toggles the password visibility in the password field.
  * @author Fabe
*/
function togglePassword() {
    const passwordField = document.getElementById("exampleInputPassword1");
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

