/**
  * Javascript code for editing survey questions
  * @author Bravo
*/
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('editSurveyQuestionsForm');
    const surveyListSelect = document.getElementById('survey-list');
    const addSurveyQuestionButton = document.getElementById('addSurveyQuestionButton');
    const surveyQuestionModal = new bootstrap.Modal(document.getElementById('surveyQuestionModal'));
    const modalSearchInput = document.getElementById('modalSearchInput2');
    const modalFilterCategory = document.getElementById('modalFilterCategory2');
    const modalTableBody = document.getElementById('modalSurveyQuestionsTable').querySelector('tbody');
    const addSelectedSurveyQuestionsButton = document.getElementById('addSelectedSurveyQuestionsButton');
    const selectedQuestionsList = document.getElementById('selectedSurveyQuestions');
    const selectedQuestionsInputs = document.getElementById('selectedSurveyQuestionsInputs');
    const editChoicesList = document.getElementById('editChoicesList');
    const selectedSurveyQuestions = new Set();

    function loadExistingSurveyQuestions(surveyId) {
        // clear existing questions
        selectedQuestionsList.innerHTML = '';
        selectedQuestionsInputs.innerHTML = '';
        selectedSurveyQuestions.clear();

        fetch(`../actions/fetch_existing_survey_questions.php?survey_id=${surveyId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(questions => {
                questions.forEach(question => {
                    const listItem = document.createElement('div');
                    listItem.className = 'list-group-item d-flex justify-content-between align-items-center mb-9';
                    listItem.dataset.questionId = question.question_id;
                    listItem.innerHTML = `
                        ${question.question}
                        <button class="btn btn-danger btn-sm remove-btn"
                        data-question-id="${question.question_id}">&times;
                        </button>
                    `;
                    selectedQuestionsList.appendChild(listItem);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'questions[]';
                    hiddenInput.value = question.question_id;
                    hiddenInput.id = `question-${question.question_id}`;
                    selectedQuestionsInputs.appendChild(hiddenInput);

                    // remove function
                    listItem.querySelector('.remove-btn').addEventListener('click', function () {
                        selectedQuestionsList.removeChild(listItem);
                        selectedQuestionsInputs.removeChild(hiddenInput);
                        selectedSurveyQuestions.delete(question.question_id);
                    });

                    selectedSurveyQuestions.add(question.question_id);
                });
            })
            .catch(error => {
                console.error('Error fetching survey questions:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not load survey questions. Please try again.'
                });
            });
    }

    // listener for survey selection
    surveyListSelect.addEventListener('change', function() {
        const selectedSurveyId = this.value;
        if (selectedSurveyId) {
            loadExistingSurveyQuestions(selectedSurveyId);
        } else {
            // clear if no survey is selected
            selectedQuestionsList.innerHTML = '';
            selectedQuestionsInputs.innerHTML = '';
            selectedSurveyQuestions.clear();
        }
    });

    function fetchQuestions() {
        fetch('../actions/fetch_null-questions.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                modalTableBody.innerHTML = '';
                if (data.length === 0) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td colspan="4" class="text-center">No questions that doesn't have a survey</td>
                `;
                    modalTableBody.appendChild(row);
                } else {
                    data.forEach(question => {
                        const row = document.createElement('tr');
                        row.dataset.questionId = question.question_id;
                        row.innerHTML = `
                        <td><input type="checkbox" class="select-question" data-question-id="${question.question_id}" data-question="${question.question}" ${selectedSurveyQuestions.has(question.question_id) ? 'checked' : ''}></td>
                        <td>${question.category_name}</td>
                        <td>${question.question}</td>
                        <td>${question.question_type}</td>
                    `;
                        modalTableBody.appendChild(row);
                    });
                }
            })
            .catch(error => console.error('Error fetching questions:', error));
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(form);
        fetch('../actions/update_survey_questions.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 1000
                });

                if (response.ok) {
                    Toast.fire({
                        icon: 'success',
                        title: "Survey questions updated successfully!"
                    }).then(() => {
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: "Error updating survey questions."
                    });
                }
            })
            .catch(error => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 1000
                });

                Toast.fire({
                    icon: 'error',
                    title: "Error updating survey questions."
                });
                console.error('Error:', error);
            });
    });

    addSurveyQuestionButton.addEventListener('click', function () {
        surveyQuestionModal.show();
        fetchQuestions();
    });

    addSelectedSurveyQuestionsButton.addEventListener('click', function () {
        document.querySelectorAll('.select-question:checked').forEach(checkbox => {
            const questionId = checkbox.dataset.questionId;
            const questionText = checkbox.dataset.question;

            if (!selectedQuestionsList.querySelector(`li[data-question-id="${questionId}"]`)) {
                const listItem = document.createElement('li');
                listItem.className = 'list-group-item d-flex justify-content-between align-items-center mb-2';
                listItem.dataset.questionId = questionId;
                listItem.innerHTML = `
                    ${questionText}
                    <button class="btn btn-danger btn-sm remove-btn" data-question-id="${questionId}">&times;</button>
                `;
                selectedQuestionsList.appendChild(listItem);

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'questions[]';
                hiddenInput.value = questionId;
                hiddenInput.id = `question-${questionId}`;
                selectedQuestionsInputs.appendChild(hiddenInput);

                listItem.querySelector('.remove-btn').addEventListener('click', function () {
                    selectedQuestionsList.removeChild(listItem);
                    selectedQuestionsInputs.removeChild(hiddenInput);
                    selectedSurveyQuestions.delete(questionId);
                });

                selectedSurveyQuestions.add(questionId);
            }
        });
    });

    fetch('../actions/fetch_categories.php')
        .then(response => response.json())
        .then(categories => {
            const filterCategorySelect = document.getElementById('modalFilterCategory2');

            categories.forEach(category => {
                const option1 = document.createElement('option');
                option1.value = category.category_name;
                option1.textContent = category.category_name;
                filterCategorySelect.appendChild(option1);
            });
        })
        .catch(error => console.error('Error fetching categories:', error));

    function filterQuestions() {
        const searchValue = modalSearchInput.value.toLowerCase();
        const filterValue = modalFilterCategory.value;
        const rows = modalTableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const category = row.cells[1].textContent.toLowerCase();
            const question = row.cells[2].textContent.toLowerCase();
            const matchesSearch = question.includes(searchValue);
            const matchesCategory = !filterValue || category === filterValue.toLowerCase();
            if (matchesSearch && matchesCategory) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    modalSearchInput.addEventListener('input', filterQuestions);
    modalFilterCategory.addEventListener('change', filterQuestions);
});