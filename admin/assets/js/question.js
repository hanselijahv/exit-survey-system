/**
  * This file contains the JavaScript code for the questions page.
  * @authors Bravo, Fabe
*/

fetchQuestions();

/**
 * Assigning functions
 */
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('searchInput').addEventListener('input', filterQuestions);
    document.getElementById('filterCategory').addEventListener('change', filterQuestions);
});

/**
 *  Fetch API for fetching Categories
 */
document.addEventListener("DOMContentLoaded", function () {
    fetch('../actions/fetch_categories.php')
        .then(response => response.json())
        .then(categories => {
            const categorySelect = document.getElementById('category');
            const filterCategorySelect = document.getElementById('filterCategory');

            categories.forEach(category => {
                const option1 = document.createElement('option');
                option1.value = category.category_id;
                option1.textContent = category.category_name;
                categorySelect.appendChild(option1);

                const option2 = document.createElement('option');
                option2.value = category.category_name;
                option2.textContent = category.category_name;
                filterCategorySelect.appendChild(option2);

            });
        })
        .catch(error => console.error('Error fetching categories:', error));
});

/**
 * Insertion of Questions with Sweet-Alert
 */
document.addEventListener('DOMContentLoaded', function () {
    const createQuestionForm = document.querySelector('form[action="../actions/insert.php"]');
    createQuestionForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(createQuestionForm);
        fetch('../actions/insert.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-right',
                        showConfirmButton: false,
                        timer: 1000
                    });

                    Toast.fire({
                        icon: 'success',
                        title: "Question created successfully!"
                    }).then(() => {
                        setTimeout(() => {
                            window.location.href = '../home/questions.php';
                        }, 200);
                    });
                } else {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-right',
                        showConfirmButton: false,
                        timer: 1000
                    });

                    Toast.fire({
                        icon: 'error',
                        title: "Error creating question."
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 1000
                });

                Toast.fire({
                    icon: 'error',
                    title: "Error creating question."
                });
            });
    });
});


function populateEditModal(row) {
    const questionId = row.dataset.questionId;
    const category = row.cells[0].textContent;
    const question = row.cells[1].textContent;
    const questionType = row.cells[2].textContent;
    console.log(category);

    document.getElementById('editQuestionId').value = questionId;
    document.getElementById('editQuestion').value = question;

    fetch('../actions/fetch_categories.php')
        .then(response => response.json())
        .then(categories => {
            const editCategorySelect = document.getElementById('editCategory');
            editCategorySelect.innerHTML = '';
            categories.forEach(categoryObj => {
                const option = document.createElement('option');
                option.value = categoryObj.category_id;
                option.textContent = categoryObj.category_name;
                editCategorySelect.appendChild(option);
            });
            const selectedOption = Array.from(editCategorySelect.options).find(option => option.textContent === category);
            if (selectedOption) {
                editCategorySelect.value = selectedOption.value;
            }
        })
        .catch(error => console.error('Error fetching categories:', error));

    if (questionType === 'multiple_choice') {
        editChoicesList.innerHTML = '';
        fetch(`../actions/fetch_choices.php?question_id=${questionId}`)
            .then(response => response.json())
            .then(choices => {
                choices.forEach(choice => {
                    addChoiceInput(choice);
                });
            });
        document.getElementById('editChoicesContainer').style.display = 'block';
        addEditChoiceButton.style.display = 'block';
    } else if (['boolean', 'satisfaction', 'relevance', 'quality'].includes(questionType)) {
        editChoicesList.innerHTML = '';
        document.getElementById('editChoicesContainer').style.display = 'block';
        addEditChoiceButton.style.display = 'none';
    } else {
        document.getElementById('editChoicesContainer').style.display = 'none';
    }
    $('#editQuestionModal').modal('show');
}


/**
 * Fetching of questions for the table
 */
function fetchQuestions() {
    fetch('../actions/fetch_questions.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('questionsTable').querySelector('tbody');
            tableBody.innerHTML = '';
            data.forEach(question => {
                const row = document.createElement('tr');
                row.dataset.questionId = question.question_id;
                row.dataset.categoryId = question.category_id;
                row.innerHTML = `
                        <td>${question.category_name}</td>
                        <td>${question.question}</td>
                        <td>${question.question_type}</td>
                        <td><button class="btn btn-secondary m-1"><i class="ti ti-edit"></i> Edit </button></td>
                    `;
                tableBody.appendChild(row);
            });

            document.querySelectorAll('.btn.btn-secondary.m-1').forEach(button => {
                button.addEventListener('click', function () {
                    const row = this.closest('tr');
                    populateEditModal(row);
                });
            })
        }).catch(error => console.error('Error fetching questions:', error));
}

/**
 * Edit Question with Sweet-Alert
 */
document.addEventListener("DOMContentLoaded", function () {
    const editQuestionModal = document.getElementById('editQuestionModal');
    const closeModal = document.getElementsByClassName('btn-close')[0];
    const editQuestionForm = document.getElementById('editQuestionForm');
    const deleteQuestionButton = document.getElementById('deleteQuestionButton');
    const addEditChoiceButton = document.getElementById('addEditChoiceButton');

    closeModal.onclick = function () {
        $('#editQuestionModal').modal('hide');
    };

    window.onclick = function (event) {
        if (event.target == editQuestionModal) {
            $('#editQuestionModal').modal('hide');
        }
    };

    addEditChoiceButton.addEventListener('click', function () {
        addChoiceInput('');
    });

    editQuestionForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(editQuestionForm);

        const questionTypeSelect = document.getElementById('question_type_modal');
        if (questionTypeSelect) {
            formData.append('question_type_modal', questionTypeSelect.value);
        }

        if (formData.get('question_type_modal') === 'multiple_choice') {
            const choiceInputs = document.querySelectorAll('input[name="choices[]"]');
            formData.delete('choices[]');
            choiceInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    formData.append('choices[]', input.value.trim());
                }
            });
        }

        fetch('../actions/update_question.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-right',
                        showConfirmButton: false,
                        timer: 1000
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Question updated successfully.'
                    }).then(() => {
                        $('#editQuestionModal').modal('hide');
                        setTimeout(() => {
                            window.location.href = '../home/questions.php';
                        }, 200);
                    });
                } else {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-right',
                        showConfirmButton: false,
                        timer: 1000
                    });

                    Toast.fire({
                        icon: 'error',
                        title: 'Error updating question.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top',
                    showConfirmButton: false,
                    timer: 1000
                });

                Toast.fire({
                    icon: 'error',
                    title: 'Error updating question.'
                });
            });
    });

    deleteQuestionButton.addEventListener('click', function () {
        const questionId = document.getElementById('editQuestionId').value;
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`../actions/delete_question.php?question_id=${questionId}`, {
                    method: 'GET'
                })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your file has been deleted.",
                                icon: "success",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            $('#editQuestionModal').modal('hide');
                            fetchQuestions();
                            populateEditModal();
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Error deleting question.",
                                icon: "error"
                            });
                        }
                    });
            }
        });
    });

});

/**
 * Filter of questions in the table
 */
function filterQuestions() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const filterCategory = document.getElementById('filterCategory').value;
    const tableBody = document.getElementById('questionsTable').querySelector('tbody');
    const rows = tableBody.querySelectorAll('tr');

    rows.forEach(row => {
        const category = row.cells[0].textContent.toLowerCase();
        const question = row.cells[1].textContent.toLowerCase();
        const matchesSearch = question.includes(searchInput);
        const matchesCategory = !filterCategory || category === filterCategory.toLowerCase();
        if (matchesSearch && matchesCategory) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function addChoiceInput(value, isUneditable = false) {
    const choiceContainer = document.createElement('div');
    choiceContainer.className = 'list-group-item d-flex justify-content-between align-items-center mb-2 choice-container';
    const choiceInput = document.createElement('input');
    choiceInput.type = 'text';
    choiceInput.name = 'choices[]';
    choiceInput.className = 'form-control';
    choiceInput.value = value;
    choiceInput.style.marginBottom = '10px';
    if (isUneditable) {
        choiceInput.classList.add('uneditable');
        choiceInput.disabled = true;
    }
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.className = 'btn btn-danger delete-btn';
    deleteButton.innerHTML = "&times;";
    deleteButton.style.marginLeft = '10px';
    deleteButton.style.marginBottom = '10px';
    deleteButton.addEventListener('click', function () {
        choiceContainer.remove();
    });
    choiceContainer.appendChild(choiceInput);
    if(!isUneditable) {
        choiceContainer.appendChild(deleteButton);
    }
    editChoicesList.appendChild(choiceContainer);
}

/**
 * Add Choice for Multiple Choice Questions
 */
document.addEventListener("DOMContentLoaded", function () {
    const questionTypeSelect = document.getElementById('question_type');
    const questionTypeSelect2 = document.getElementById('question_type_modal');
    const choicesContainer = document.getElementById('choicesContainer');
    const choicesContainer2 = document.getElementById('choicesContainerModal');
    const addChoiceButton = document.getElementById('addChoiceButton');
    const addChoiceButton2 = document.getElementById('addChoiceButtonModal');
    const choicesList = document.getElementById('choicesList');
    const choicesList2 = document.getElementById('choicesListModal');
    questionTypeSelect.addEventListener('change', function () {
        if (this.value === 'multiple_choice') {
            choicesContainer.style.display = 'block';
        } else {
            choicesContainer.style.display = 'none';
        }
    });
    questionTypeSelect2.addEventListener('change', function () {
        if (this.value === 'multiple_choice') {
            choicesContainer2.style.display = 'block';
        } else {
            choicesContainer2.style.display = 'none';
        }
    });
    addChoiceButton.addEventListener('click', function () {
        const newChoiceInput = document.createElement('input');
        newChoiceInput.type = 'text';
        newChoiceInput.name = 'choices[]';
        newChoiceInput.className = 'form-control';
        newChoiceInput.placeholder = 'Enter choice';
        newChoiceInput.style.marginBottom = '10px';
        choicesList.appendChild(newChoiceInput);
    });
    addChoiceButton2.addEventListener('click', function () {
        const newChoiceInput2 = document.createElement('input');
        newChoiceInput2.type = 'text';
        newChoiceInput2.name = 'choices[]';
        newChoiceInput2.className = 'form-control';
        newChoiceInput2.placeholder = 'Enter choice';
        newChoiceInput2.style.marginBottom = '10px';
        choicesList2.appendChild(newChoiceInput2);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const editPage1 = document.getElementById('editPage1');
    const editPage2 = document.getElementById('editPage2');
    const editPage1Link = document.getElementById('editPage1Link');
    const editPage2Link = document.getElementById('editPage2Link');

    editPage1Link.addEventListener('click', function() {
        editPage1.style.display = 'block';
        editPage2.style.display = 'none';
        editPage1Link.closest('li').classList.add('active');
        editPage2Link.closest('li').classList.remove('active');
    });

    editPage2Link.addEventListener('click', function() {
        editPage1.style.display = 'none';
        editPage2.style.display = 'block';
        editPage1Link.closest('li').classList.remove('active');
        editPage2Link.closest('li').classList.add('active');
    });
});

/**
 * Character count for textarea with Sweet-Alert
 */
const maxLength = 150;
let warningShown = false;
const textarea = document.getElementById('question');
const charCount = document.getElementById('charCount');
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

const textarea2 = document.getElementById('editQuestion');
const charCount2 = document.getElementById('charCount2');
textarea2.addEventListener('input', function () {
    const content = textarea2.value;
    const contentLength = content.length;

    charCount2.textContent = `${contentLength} / ${maxLength}`;

    if (contentLength >= maxLength) {
        charCount2.classList.remove('bg-secondary');
        charCount2.classList.add('bg-danger');
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
        textarea2.value = content.substring(0, maxLength);
    } else {
        charCount2.classList.remove('bg-danger');
        charCount2.classList.add('bg-secondary');
        warningShown = false;
    }
});