// JavaScript to handle AJAX for adding/removing favorites
document.querySelectorAll('.favorite-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(this); // Get the form data

        fetch('favorites.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Process the JSON response
        .then(data => {
            if (data.status === "success") {
                const heartButton = this.querySelector('.favorite-btn');
                heartButton.classList.toggle('favorited'); // Toggle the visual state of the heart icon
            }
        })
        .catch(error => {
            console.error('Error processing favorite action:', error); // Error handling
        });
    });
});
