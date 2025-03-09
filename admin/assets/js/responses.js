/**
  * This script is used to fetch and display survey responses.
  * @authors Briones, Fabe
*/
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('dataTableExample').querySelector('tbody');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const pagination = document.getElementById('pagination');
    const searchInput = document.getElementById('searchInput');
    const filterCategory = document.getElementById('filterCategory');
    const entriesInfo = document.getElementById('entriesInfo');
    const navSurveys = document.querySelector('.nav-link[href="#"]');
    const navResponses = document.querySelector('.nav-link[href="#responses"]');
    const surveysCardBody = document.querySelector('.card-body:not(#responsesCardBody)');
    const responsesCardBody = document.getElementById('responsesCardBody');
    let surveyViewed = false;
    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    let surveys = [];
    let viewedSurveyId = null;

    function fetchSurveys() {
        fetch('../actions/fetch_published_surveys.php')
            .then(response => response.json())
            .then(data => {
                surveys = data;
                displayTable();
                setupPagination();
            })
            .catch(error => console.error('Error fetching surveys:', error));
    }

    function fetchAssignedAndCompleted(surveyId) {
        return fetch(`../actions/fetch_responses.php?survey_id=${surveyId}`)
            .then(response => response.json());
    }

    function fetchQuestions(surveyId) {
        return fetch(`../actions/fetch_survey_responses.php?survey_id=${surveyId}`)
            .then(response => response.json())
            .then(data => {
                displayQuestions(data);
            })
            .catch(error => console.error('Error fetching questions:', error));
    }

    function displayQuestions(response) {
        responsesCardBody.innerHTML = '';
        const questionsContainer = document.createElement('div');
        questionsContainer.className = 'card-body';

        if (response.message) {
            const noQuestionsElement = document.createElement('div');
            noQuestionsElement.className = 'alert alert-info';
            noQuestionsElement.textContent = response.message;
            questionsContainer.appendChild(noQuestionsElement);
        } else if (response.length === 0) {
            const noQuestionsElement = document.createElement('div');
            noQuestionsElement.className = 'alert alert-info';
            noQuestionsElement.textContent = 'There are no questions for this survey.';
            questionsContainer.appendChild(noQuestionsElement);
        } else {
            response.forEach(question => {
                const questionElement = document.createElement('div');
                questionElement.className = 'card';
                questionElement.innerHTML = `
                <div class="card-body">
                    <h5 class="card-title">${question.question}</h5>
                    <div id="chart-${question.question_id}" style="height: 350px;"></div>
                </div>
            `;
                questionsContainer.appendChild(questionElement);

                if (question.question_type === 'multiple_choice' || question.question_type === 'boolean' || question.question_type === 'satisfaction' || question.question_type === 'relevance' || question.question_type === 'quality' && question.responses) {
                    const chartContainer = questionElement.querySelector(`#chart-${question.question_id}`);
                    const labels = [];
                    const data = [];

                    question.responses.forEach(response => {
                        labels.push(response.response);
                        data.push(response.count);
                    });

                    const options = {
                        chart: {
                            type: 'pie',
                            height: 350
                        },
                        series: data,
                        labels: labels
                    };

                    setTimeout(() => {
                        const chart = new ApexCharts(chartContainer, options);
                        chart.render();
                    }, 100);
                } else if (question.question_type === 'short_answer' && question.responses) {
                    const responsesContainer = document.createElement('div');
                    responsesContainer.className = 'card mt-3';

                    const responsesHeader = document.createElement('div');
                    responsesHeader.className = 'card-header';
                    responsesHeader.textContent = 'Responses';
                    responsesContainer.appendChild(responsesHeader);

                    const responsesList = document.createElement('ul');
                    responsesList.className = 'list-group list-group-flush';

                    question.responses.forEach(response => {
                        const listItem = document.createElement('li');
                        listItem.className = 'list-group-item';
                        listItem.textContent = response.short_answer;
                        responsesList.appendChild(listItem);
                    });

                    responsesContainer.appendChild(responsesList);
                    const cardBody = questionElement.querySelector('.card-body');
                    cardBody.insertBefore(responsesContainer, cardBody.children[1]);
                }
            });
        }

        responsesCardBody.appendChild(questionsContainer);
    }

    function displayTable() {
        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedSurveys = surveys.slice(start, end);

        paginatedSurveys.forEach(survey => {
            fetchAssignedAndCompleted(survey.survey_id)
                .then(data => {
                    const row = document.createElement('tr');
                    row.dataset.surveyId = survey.survey_id;
                    row.dataset.isPublished = survey.is_published;
                    row.innerHTML = `
                    <td>${survey.survey_name}</td>
                    <td>${survey.program_description}</td>
                    <td><span class="badge bg-primary assigned-users">${data.assigned_users}</span></td>
                    <td><span class="badge bg-success completed-users">${data.completed_users}</span></td>
                    <td><button class="btn btn-sm btn-secondary view-button"><i class="ti ti-eye-check"></i> View</button></td>
                `;
                    tableBody.appendChild(row);
                })
                .catch(error => console.error('Error fetching assigned and completed counts:', error));
        });
        entriesInfo.textContent = `Showing ${start + 1} to ${Math.min(end, surveys.length)} of ${surveys.length} entries`;
    }

    function startFetchingAssignedAndCompleted() {
        setInterval(() => {
            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                const surveyId = row.dataset.surveyId;
                fetchAssignedAndCompleted(surveyId)
                    .then(data => {
                        row.querySelector('.assigned-users').textContent = data.assigned_users;
                        row.querySelector('.completed-users').textContent = data.completed_users;
                    })
                    .catch(error => console.error('Error fetching updated assigned and completed counts:', error));
            });
        }, 3000); // Fetch every 3 seconds
    }

    startFetchingAssignedAndCompleted();

    function setupPagination() {
        pagination.innerHTML = '';
        const pageCount = Math.ceil(surveys.length / rowsPerPage);
        for (let i = 1; i <= pageCount; i++) {
            const pageItem = document.createElement('li');
            pageItem.className = 'page-item' + (i === currentPage ? ' active' : '');
            pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageItem.addEventListener('click', function () {
                currentPage = i;
                displayTable();
                setupPagination();
            });
            pagination.appendChild(pageItem);
        }
    }

    function filterSurveys() {
        const searchValue = searchInput.value.toLowerCase();
        const filterValue = filterCategory.value;
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const surveyName = row.cells[0].textContent.toLowerCase();
            const assignedCount = parseInt(row.cells[2].textContent);
            const completedCount = parseInt(row.cells[3].textContent);
            const matchesSearch = surveyName.includes(searchValue);
            const matchesFilter = !filterValue ||
                (filterValue === 'completed' && assignedCount === completedCount) ||
                (filterValue === 'pending' && assignedCount > completedCount);
            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function switchToResponsesTab() {
        navSurveys.classList.remove('active');
        navResponses.classList.add('active');
        surveysCardBody.style.display = 'none';
        responsesCardBody.style.display = 'block';
        if (viewedSurveyId) {
            fetchQuestions(viewedSurveyId);
        }
    }

    rowsPerPageSelect.addEventListener('change', function () {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        displayTable();
        setupPagination();
    });

    searchInput.addEventListener('input', filterSurveys);
    filterCategory.addEventListener('change', filterSurveys);

    tableBody.addEventListener('click', function (event) {
        if (event.target.closest('.view-button')) {
            const surveyId = event.target.closest('tr').dataset.surveyId;
            surveyViewed = true;
            viewedSurveyId = surveyId;
            navResponses.classList.remove('disabled');
            switchToResponsesTab();
        }
    });

    navResponses.addEventListener('click', function (event) {
        if (!surveyViewed) {
            event.preventDefault();
        } else {
            switchToResponsesTab();
        }
    });

    navSurveys.addEventListener('click', function () {
        navResponses.classList.remove('active');
        navSurveys.classList.add('active');
        surveysCardBody.style.display = 'block';
        responsesCardBody.style.display = 'none';
    });

    navResponses.classList.add('disabled');
    fetchSurveys();
});