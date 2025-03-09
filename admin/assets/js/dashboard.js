/**
  * This file contains the logic for the dashboard page
  * @authors Bravo, Fabe
*/
$(function () {
  $.ajax({
    url: '../actions/fetch_chart_data.php',
    method: 'GET',
    success: function (data) {
      var totalSurveys = data[0].total_surveys;
      var uniqueCompletedSurveys = data[0].unique_completed_surveys;
      var pendingSurveys = data[0].pending_surveys;
      var publishedSurveys = data[0].published_surveys;
      var chart = {
        series: [
          { name: "Total Surveys", data: [totalSurveys] },
          { name: "Accomplished Surveys", data: [uniqueCompletedSurveys] },
          { name: "Pending Surveys", data: [pendingSurveys] },
          { name: "Published Surveys", data: [publishedSurveys] }
        ],
        chart: {
          type: "bar",
          height: 345,
          offsetX: -15,
          toolbar: { show: false },
          foreColor: "#adb0bb",
          fontFamily: 'inherit',
          sparkline: { enabled: false },
        },
        colors: ["#5D87FF", "#FF4560", "#FFAE1F", "#13DEB9"],
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: "35%",
            borderRadius: [6],
            borderRadiusApplication: 'end',
            borderRadiusWhenStacked: 'all'
          },
        },
        markers: { size: 0 },
        dataLabels: { enabled: false },
        legend: { show: true },
        grid: {
          borderColor: "rgba(0,0,0,0.1)",
          strokeDashArray: 3,
          xaxis: { lines: { show: false } },
        },
        xaxis: {
          type: "category",
          categories: ["Surveys"],
          labels: { style: { cssClass: "grey--text lighten-2--text fill-color" } },
        },
        yaxis: {
          show: true,
          min: 0,
          max: Math.max(totalSurveys, uniqueCompletedSurveys, pendingSurveys, publishedSurveys) + 10,
          tickAmount: 4,
          labels: { style: { cssClass: "grey--text lighten-2--text fill-color" } },
        },
        stroke: {
          show: true,
          width: 3,
          lineCap: "butt",
          colors: ["transparent"],
        },
        tooltip: { theme: "light" },
        responsive: [
          {
            breakpoint: 600,
            options: {
              plotOptions: { bar: { borderRadius: 3 } },
            },
          },
        ],
      };

      var chart = new ApexCharts(document.querySelector("#chart"), chart);
      chart.render();
      $('#totalSurveys').text(totalSurveys);
      $('#uniqueCompletedSurveys').text(uniqueCompletedSurveys);
      $('#pendingSurveys').text(pendingSurveys);
      $('#publishedSurveys').text(publishedSurveys);
    },
    error: function (error) {
      console.error("Error fetching data", error);
    }
  });
});


$(function () {
  $.ajax({
    url: '../actions/fetch_card_data.php',
    method: 'GET',
    success: function (data) {
      var totalResponses = data.total_responses;
      var percentageChange = parseFloat(data.percentage_change).toFixed(2);
      var usersWithResponses = data.users_with_responses;
      var usersWithoutResponses = data.users_without_responses;
      var totalUsers = data.total_users;
      var totalAdmins = data.total_admins;
      var totalStudents = data.total_students;


      $('.card-title:contains("Total Responses")').next('h4').text(totalResponses);
      $('.card-title:contains("Total Responses")').next('h4').next('.d-flex').find('p').text(percentageChange + '%');
      $('.users-with-responses').text(usersWithResponses);
      $('.users-without-responses').text(usersWithoutResponses);

      $('.card-title:contains("Total Users")').next('h4').text(totalUsers);
      $('.total_admins').text(totalAdmins);
      $('.total_students').text(totalStudents);

    },
    error: function (error) {
      console.error("Error fetching data", error);
    }
  });
});