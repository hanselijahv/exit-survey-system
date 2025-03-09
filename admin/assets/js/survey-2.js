/**
  * This file contains the logic for the survey creation page.
  * @authors Bravo, Fabe
*/
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('questionnaireForm');
    const addQuestionButton = document.getElementById('addQuestionButton');
    const questionModal = new bootstrap.Modal(document.getElementById('questionModal'));
    const modalSearchInput = document.getElementById('modalSearchInput');
    const modalFilterCategory = document.getElementById('modalFilterCategory');
    const modalTableBody = document.getElementById('modalQuestionsTable').querySelector('tbody');
    const addSelectedQuestionsButton = document.getElementById('addSelectedQuestionsButton');
    const selectedQuestionsList = document.getElementById('selectedQuestions');
    const selectedQuestionsInputs = document.getElementById('selectedQuestionsInputs');
    const selectedQuestions = new Set();

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
                        <td><input type="checkbox" class="select-question" data-question-id="${question.question_id}" data-question="${question.question}" ${selectedQuestions.has(question.question_id) ? 'checked' : ''}></td>
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
        fetch('../actions/save_survey.php', {
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
                        title: "Survey saved successfully!"
                    }).then(() => {
                        setTimeout(() => {
                            window.location.href = '../home/surveys.php';
                        }, 200);
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: "Error saving survey."
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
                    title: "Error saving survey."
                });
                console.error('Error:', error);
            });
    });

    addQuestionButton.addEventListener('click', function () {   
        questionModal.show();
        fetchQuestions();
    });

    addSelectedQuestionsButton.addEventListener('click', function () {
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
                    selectedQuestions.delete(questionId);
                });

                selectedQuestions.add(questionId);
            }
        });
    });

    fetch('../actions/fetch_categories.php')
        .then(response => response.json())
        .then(categories => {
            const filterCategorySelect = document.getElementById('modalFilterCategory');

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
