/**
  * Fetches programs from the database and populates the program select element
  * @author Fabe
*/

const programSelect = document.getElementById('program');

programSelect.addEventListener('focus', function () {
    if (programSelect.options.length <= 1) {
        fetch('../actions/fetch_programs.php')
            .then(response => response.json())
            .then(data => {
                programSelect.innerHTML = '<option value="">Select Program</option>';
                data.forEach(program => {
                    const option = document.createElement('option');
                    option.value = program.program_id;
                    option.textContent = program.program_description;
                    programSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching programs:', error));
    }
}, { once: true });


document.getElementById('program').addEventListener('change', function() {
    var programId = this.value;
    fetchClasses(programId);
});

function fetchClasses(programId) {
    fetch(`../actions/fetch_classes.php?program_id=${programId}`)
        .then(response => response.json())
        .then(data => {
            var classSelect = document.getElementById('restrict');
            data.forEach(function(classItem) {
                var option = document.createElement('option');
                option.value = classItem.class_code;
                option.text = `${classItem.class_code} ${classItem.class_number} - ${classItem.class_description}`;
                classSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching classes:', error));
}
