/**
  * This script is used to handle the survey form.
  * @authors Bravo, Fabe
*/
const maxLength = 20;
let warningShown = false;

const textarea = document.getElementById('name');
const charCount = document.getElementById('charCount2');

textarea.addEventListener('input', function () {
    const content = textarea.value;
    const contentLength = content.length;

    charCount.textContent = `${contentLength} / ${maxLength}`;

    if (contentLength >= maxLength) {
        charCount.classList.remove('bg-secondary');
        charCount.classList.add('bg-danger');
        if (!warningShown) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: `Maximum length of ${maxLength} characters reached.`,
                timer: 1500,
                showConfirmButton: false
            });
            warningShown = true;
        }
        textarea.value = content.substring(0, maxLength);
    } else {
        charCount.classList.remove('bg-danger');
        charCount.classList.add('bg-secondary');
        warningShown = false;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('restrict');
    const selectedClassesContainer = document.getElementById('selected-classes');
    const programSelectElement = document.getElementById('program');

    programSelectElement.addEventListener('change', function() {
        fetchClassesForProgram(this.value);
    });

    selectElement.addEventListener('change', function() {
        updateSelectedClasses();
    });

    function fetchClassesForProgram(programId) {
        // Clear existing options
        selectElement.innerHTML = '<option value="no_restrict">No classes</option>';

        // Fetch new classes based on the selected program
        fetch(`../actions/fetch_classes.php?program_id=${programId}`)
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    data.forEach(classItem => {
                        if (classItem.id && classItem.name) {
                            const option = document.createElement('option');
                            option.value = classItem.id;
                            option.text = classItem.name;
                            selectElement.appendChild(option);
                        } else {
                            console.error('Invalid class item:', classItem);
                        }
                    });
                } else {
                    console.error('Unexpected response format:', data);
                }
            })
            .catch(error => console.error('Error fetching classes:', error));
    }

    function updateSelectedClasses() {
        selectedClassesContainer.innerHTML = '';
        const selectedOptions = Array.from(selectElement.selectedOptions);
        const noRestrictOption = selectedOptions.find(option => option.value === 'no_restrict');

        if (noRestrictOption) {
            selectElement.selectedOptions.forEach(option => {
                if (option.value !== 'no_restrict') {
                    option.selected = false;
                }
            });
            selectedClassesContainer.innerHTML = '<span class="badge" style="display: inline-block; padding: 0.5em 0.75em; margin: 0.25em; font-size: 0.875em; font-weight: 600; color: #fff; background-color: #007bff; border-radius: 0.25rem;">No restrictions</span>';
        } else {
            selectedOptions.forEach(option => {
                const badge = document.createElement('span');
                badge.className = 'badge';
                badge.style.cssText = 'display: inline-block; padding: 0.5em 0.75em; margin: 0.25em; font-size: 0.875em; font-weight: 600; color: #fff; background-color: #007bff; border-radius: 0.25rem;';
                badge.textContent = option.text;
                const removeIcon = document.createElement('span');
                removeIcon.className = 'remove-badge';
                removeIcon.style.cssText = 'margin-left: 0.5em; cursor: pointer;';
                removeIcon.innerHTML = '&times;';
                removeIcon.addEventListener('click', function() {
                    option.selected = false;
                    updateSelectedClasses();
                });
                badge.appendChild(removeIcon);
                selectedClassesContainer.appendChild(badge);
            });
        }
    }
});

document.getElementById('page1Link').addEventListener('click', function () {
    document.getElementById('page1').style.display = 'block';
    document.getElementById('page2').style.display = 'none';
    document.querySelector('.page-item.active').classList.remove('active');
    this.parentElement.classList.add('active');
});

document.getElementById('page2Link').addEventListener('click', function () {
    document.getElementById('page1').style.display = 'none';
    document.getElementById('page2').style.display = 'block';
    document.querySelector('.page-item.active').classList.remove('active');
    this.parentElement.classList.add('active');
});
