/**
  * Profile page script
  * @author Fabe
*/
$(document).ready(function() {
    $('#profileLink').on('click', function() {
        $('#profileModal').modal('show');
    });

    $('#profileLink').on('click', function() {
        $.ajax({
            url: '../actions/fetch_user_details.php',
            method: 'GET',
            success: function(data) {
                $('#firstName').val(data.first_name);
                $('#lastName').val(data.last_name);
                $('#email').val(data.email);
                $('#password').val(data.password);
                if (data.image) {
                    $('#imagePreview').attr('src', 'data:image/jpeg;base64,' + data.image);
                }
                $('#profileModal').modal('show');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch user details'
                });
            }
        });
    });

    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: '../actions/update_user_details.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 1000
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Profile updated successfully',
                }).then((result) => {
                    location.reload();
                });
                $('#profileModal').modal('hide');
            },
            error: function(xhr) {
                Toast.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update profile'
                });
            }
        });
    });
});

document.getElementById('image').addEventListener('change', function(event) {
    var reader = new FileReader();
    reader.onload = function() {
        var output = document.getElementById('imagePreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
});