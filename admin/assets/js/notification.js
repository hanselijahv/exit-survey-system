/**
  * This file contains the code for the notification system.
  * @author Fabe
*/
document.addEventListener('DOMContentLoaded', function () {
    const successMessage = document.getElementById('successMessage').value;
    if (successMessage) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 1000
        });

        Toast.fire({
            icon: 'success',
            title: successMessage
        }).then(() => {
            setTimeout(() => {
                window.location.href = '../home/dashboard.php';
            }, 200);
        });
    }

    const errorMessage = document.getElementById('errorMessage').value;
    if(errorMessage){
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 1000
        });

        Toast.fire({
            icon: 'error',
            title: errorMessage
        });
    }
});