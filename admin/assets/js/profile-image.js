/**
  * This file is used to fetch the profile image of the user from the database.
  * @authors Briones, Fabe
*/
document.addEventListener('DOMContentLoaded', function() {
    function changeImage(newSrc) {
        const imageElement = document.getElementById('fetched-image');
        if (imageElement) {
            imageElement.src = newSrc;
        }
        const imageElement2 = document.getElementById('fetched-image2');
        if (imageElement2) {
            imageElement2.src = newSrc;
        }
    }

    function fetchImage() {
        fetch('../actions/fetch_image.php')
            .then(response => response.json())
            .then(data => {
                if (data.image) {
                    const imageSrc = 'data:image/png;base64,' + data.image;
                    changeImage(imageSrc);
                } else {
                    console.error('Image not found');
                }
            })
            .catch(error => console.error('Error fetching image:', error));
    }

    fetchImage();
});

