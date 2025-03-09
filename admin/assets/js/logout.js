/**
  * Logout script
  * @author Fabe
*/
const element = document.getElementById('logout');

element.addEventListener('click', function () {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 1000
    });

    Toast.fire({
        icon: 'info',
        title: "Logging out..."
    }).then(() => {
        setTimeout(() => {
            window.location.href = '../auth/logout.php';
        }, 200);
    });

});