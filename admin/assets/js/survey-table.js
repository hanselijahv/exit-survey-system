/**
  * This script is used to handle the survey table in the admin dashboard.
  * @authors Bravo, Fabe
*/
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('dataTableExample').querySelector('tbody');
    const rowsPerPageSelect = document.getElementById('rowsPerPage');
    const pagination = document.getElementById('pagination');
    const searchInput = document.getElementById('searchInput');
    const filterCategory = document.getElementById('filterCategory');
    const entriesInfo = document.getElementById('entriesInfo');
    const selectElement2 = document.getElementById('restrict-2');
    const selectedClassesContainer2 = document.getElementById('selected-classes-2');
    const programSelectElement2 = document.getElementById('editProgram');

    let currentPage = 1;
    let rowsPerPage = parseInt(rowsPerPageSelect.value);
    let surveys = [];

    function fetchSurveys() {
        fetch('../actions/fetch_surveys.php')
            .then(response => response.json())
            .then(data => {
                surveys = data;
                displayTable();
                setupPagination();
            })
            .catch(error => console.error('Error fetching surveys:', error));
    }


    function populateEditModal(row) {
        const surveyId = row.dataset.surveyId;
        const survey = surveys.find(s => s.survey_id === surveyId);

        document.getElementById('editSurveyId').value = surveyId;
        document.getElementById('editSurveyName').value = survey.survey_name;
        document.getElementById('editSemester').value = survey.semester;
        document.getElementById('editAcademicYear').value = survey.academic_year;

        fetch('../actions/fetch_programs.php')
            .then(response => response.json())
            .then(programs => {
                const editProgramSelect = document.getElementById('editProgram');
                editProgramSelect.innerHTML = '<option value="">Select Program</option>';
                programs.forEach(programObj => {
                    const option = document.createElement('option');
                    option.value = programObj.program_id;
                    option.textContent = programObj.program_description;
                    editProgramSelect.appendChild(option);

                    if (programObj.program_description === survey.program_description) {
                        editProgramSelect.value = programObj.program_id;
                    }
                });
            })
            .catch(error => console.error('Error fetching programs:', error));

        $('#editSurveyModal').modal('show');
    }

    const editSurveyForm = document.getElementById('editSurveyForm');
    editSurveyForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(editSurveyForm);
        const surveyListSelect = document.getElementById('survey-list');

        fetch('../actions/update_survey.php', {
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
                        title: 'Survey updated successfully.'
                    }).then(() => {
                        $('#editSurveyModal').modal('hide');
                        fetchSurveys();
                    });


                    fetch('../actions/fetch_unpublished_surveys.php')
                        .then(response => response.json())
                        .then(surveys => {
                            while (surveyListSelect.options.length > 1) {
                                surveyListSelect.remove(1);
                            }

                            console.log("adfadfadsf");

                            surveys.forEach(survey => {
                                const option = document.createElement('option');
                                option.value = survey.survey_id;
                                option.textContent = `${survey.survey_name} (${survey.semester} ${survey.academic_year}) - ${survey.program_description}`;
                                surveyListSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching unpublished surveys:', error);
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
                        title: 'Error updating survey.'
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
                    title: 'Error updating survey.'
                });
            });
    });

    function displayTable() {
        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedSurveys = surveys.slice(start, end);

        paginatedSurveys.forEach(survey => {
            const row = document.createElement('tr');
            row.dataset.surveyId = survey.survey_id;
            row.dataset.isPublished = survey.is_published;
            row.innerHTML = `
                    <td>${survey.survey_name}</td>
                    <td>${survey.semester}:${survey.academic_year}</td>
                    <td>${survey.program_description}</td>
                    <td>${survey.restricted_class}</td>
                  <td>
    ${survey.is_published == 1 ?
                (survey.is_closed == 1 ?
                        '<span style="color: #fff; background-color: #dc3545; padding: 0.25em 0.5em; border-radius: 4px; font-size: 0.875em;">closed</span>' :
                        '<span style="color: #fff; background-color: #007bff; padding: 0.25em 0.5em; border-radius: 4px; font-size: 0.875em;">published</span>'
                ) :
                '<span style="color: #fff; background-color: #7180f9; padding: 0.25em 0.5em; border-radius: 4px; font-size: 0.875em;">drafts</span>'
            }
</td>
                   <td>
    ${survey.is_published == 0 ?
                '<div class="d-flex flex-column"> <button class="btn btn-secondary btn-sm edit-btn mb-2"> <i class="ti ti-edit"></i> Edit </button><button class="btn btn-dark btn-sm publish-btn"><i class="ti ti-circle-caret-up"></i> Publish </button> </div>' :
                (survey.is_closed == 1 ?
                        '<button class="btn btn-dark btn-sm" disabled><i class="ti ti-lock"></i> Closed </button>' :
                        '<button class="btn btn-dark btn-sm close-btn"><i class="ti ti-clock-cancel"></i> Close </button>'
                )
            }
</td>
                `;
            tableBody.appendChild(row);
        });

        entriesInfo.textContent = `Showing ${start + 1} to ${Math.min(end, surveys.length)} of ${surveys.length} entries`;

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                populateEditModal(row);
            });
        });

        document.querySelectorAll('.publish-btn').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const surveyId = row.dataset.surveyId;

                fetch(`../actions/publish_survey.php?survey_id=${surveyId}`, {
                    method: 'GET'
                })
                    .then(response => response.json())
                    .then(data => {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                            timer: 1000
                        });

                        if (data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: `Survey published successfully.`
                            });
                            fetchSurveys();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: `Error publishing survey: ${data.error}`
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
                            title: 'Error publishing survey.'
                        });
                        console.error('Error:', error);
                    });
            });
        });
        document.querySelectorAll('.close-btn').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const surveyId = row.dataset.surveyId;

                fetch(`../actions/close_survey.php?survey_id=${surveyId}`, {
                    method: 'GET'
                })
                    .then(response => response.json())
                    .then(data => {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-right',
                            showConfirmButton: false,
                            timer: 1000
                        });

                        if (data.success) {
                            Toast.fire({
                                icon: 'success',
                                title: `Survey closed successfully.`
                            });
                            fetchSurveys();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: `Error closing survey: ${data.error}`
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
                            title: 'Error closing survey.'
                        });
                        console.error('Error:', error);
                    });
            });
        });
    }


    deleteSurveyButton.addEventListener('click', function () {
        const surveyId = document.getElementById('editSurveyId').value;
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
                fetch(`../actions/delete_survey.php?survey_id=${surveyId}`, {
                    method: 'GET'
                })
                    .then(response => {
                        if (response.ok) {
                            Swal.fire({
                                title: "Deleted!",
                                text: "Survey has been deleted.",
                                icon: "success",
                                timer: 1000,
                                showConfirmButton: false
                            });
                            $('#editSurveyModal').modal('hide');
                            fetchSurveys();
                            populateEditModal();
                        } else {
                            Swal.fire({
                                title: "Error!",
                                text: "Error deleting survey.",
                                icon: "error"
                            });
                        }
                    });
            }
        });
    });

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
            const isPublished = row.dataset.isPublished;
            const matchesSearch = surveyName.includes(searchValue);
            const matchesFilter = !filterValue || (filterValue === 'drafts' && isPublished == 0) || (filterValue === 'published' && isPublished == 1);

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    rowsPerPageSelect.addEventListener('change', function () {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        displayTable();
        setupPagination();
    });

    searchInput.addEventListener('input', filterSurveys);
    filterCategory.addEventListener('change', filterSurveys);

    fetchSurveys();
});