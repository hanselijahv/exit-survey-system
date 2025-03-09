/**
  * This script is used to fetch unpublished surveys from the database and populate the select element with the survey list.
  * @author Bravo
*/
document.addEventListener('DOMContentLoaded', function() {
    const surveyListSelect = document.getElementById('survey-list');

    fetch('../actions/fetch_unpublished_surveys.php')
        .then(response => response.json())
        .then(surveys => {
            while (surveyListSelect.options.length > 1) {
                surveyListSelect.remove(1);
            }

            surveys.forEach(survey => {
                const option = document.createElement('option');
                option.value = survey.survey_id;
                option.textContent = `${survey.survey_name} (${survey.semester} ${survey.academic_year}) - ${survey.program_description}`;
                surveyListSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching unpublished surveys:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Could not load unpublished surveys. Please try again.'
            });
        });

    surveyListSelect.addEventListener('change', function() {
        const selectedSurveyId = this.value;
        if (selectedSurveyId) {
            console.log('Selected Survey ID:', selectedSurveyId);
        }
    });
});