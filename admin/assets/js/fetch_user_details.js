/**
  * Fetches the user details from the server and updates the profile info.
  * @author Fabe
*/
document.addEventListener('DOMContentLoaded', function() {
    function updateProfileInfo(data) {
        if (data.error) {
            console.error(data.error);
            return;
        }
        document.getElementById('profile-name').textContent = `${data.first_name} ${data.last_name}`;
        document.getElementById('profile-email').textContent = data.email;
    }

    function fetchUserDetails() {
        fetch('../actions/fetch_user_details.php')
            .then(response => response.json())
            .then(data => updateProfileInfo(data))
            .catch(error => console.error('Error fetching user details:', error));
    }

    fetchUserDetails();
});