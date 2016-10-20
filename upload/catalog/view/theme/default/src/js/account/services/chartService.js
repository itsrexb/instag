angular
  .module('instag-app')
  .service('chartService',chartService);

function chartService() {
  return {
    changeHeight: changeHeight,
    showChart: showChart
  }

  /* changeHeight() = Changes chart height */
  function changeHeight() {
    if (window.innerWidth > 730) {
      // Chart and My Influence cards
      var influence = angular.element(document.getElementById('my-influence')),
          chartCard = angular.element(document.getElementById('chart-card'));

      influence.innerHeight('auto');
      chartCard.innerHeight('auto');
      if (influence.innerHeight() > chartCard.innerHeight()) {
        chartCard.innerHeight(influence.innerHeight());
      } else {
        influence.innerHeight(chartCard.innerHeight());
      }
    }
  }

  /* showChart() = Shows chart on Dashboard Tab */
  function showChart() {
    if (angular.element(document.getElementById('chart-data')).length) {
      // Get data container
      var dataContainer = angular.element(document.getElementById('chart-data')),
          // Initialize graph variables
          graph         = [],
          labels        = [],
          datasets      = [],
          scales        = {},
          // Initialize alpha channel for data background
          alpha         = 3,
          // Initialize minimal step size
          stepSize      = 0,
          // Initialize max value from server
          maxValue      = 0;
          // Initialize limit Y-Axis Limit
          yLimit        = 0;

      // Loop through all data types (historical,current,projected)
      for (var i = 0; i < dataContainer.children().length; i++) {
          // Initialize graph instance and followers array
          graph[i] = {
              followers: []
          }
          // If this it's not the first data type, start with null value
          // https://github.com/chartjs/Chart.js/issues/2450
          if (i > 0) {
              // Skip previous 
              for (var p = 1; p < reportData.children().length; p++) {
                  graph[i].followers.push(null);
              }
          }
          // Get report data
          var reportData = dataContainer.children().eq(i);
          // Loop through all data items
          for (var p = 0; p < reportData.children().length; p++) {
              // Don't repeat date items on X-Axis
              if (labels.indexOf(reportData.children().eq(p).data('date')) == -1) {
                  labels.push(reportData.children().eq(p).data('date'));
              }
              // Add follower data to followers property on graph object
              graph[i].followers.push(reportData.children().eq(p).data('followers'));
              // Get step size
              var o = p+1;
              // Get sure next report data exists
              if (reportData.children().eq(o)) {
                // Check if stepSize is < to current and next repor data diff
                if (stepSize < (Math.abs(reportData.children().eq(o).data('followers')) - Math.abs(reportData.children().eq(p).data('followers'))))
                  stepSize = Math.abs(Math.abs(reportData.children().eq(o).data('followers')) - Math.abs(reportData.children().eq(p).data('followers')));
              }
              // Get Max value
              if (yLimit < reportData.children().eq(p).data('followers')) {
                yLimit = reportData.children().eq(p).data('followers');
              }
          }
          // Push dataset data to draw the graph
          datasets.push({
              label: dataContainer.children().eq(i).data('key'),
              data: graph[i].followers,
              //data: 371+i,
              backgroundColor: "rgba(129,55,176,0."+alpha+")",
              lineTension: 0.1,
              borderWidth: 1,
              borderColor: "rgba(0,0,0,0.3)",
              pointBorderColor: "rgba(129,55,176,0.7)",
              pointBackgroundColor: "rgba(129,55,176,1)"
          });
          // Increase alpha channel to get darker graph background
          alpha += 3;
      }
      // Round stepSize
      stepSize = (Math.ceil(stepSize/100)*100);
      // Get the max value
      maxValue = stepSize*(reportData.children().length+2);
      // Make sure yLimit it's not exaggeratedly large
      var condition = false;
      while (!condition) {
        if ((maxValue/yLimit)>2.1) {
          if ((maxValue-stepSize) > yLimit) {
            maxValue -= stepSize;
          } else {
            condition = true;
          }
        } else {
          condition = true; 
        }
      }

      // Check if chart element is there
      if (document.getElementById("chart")) {
          // Initialize chart
          var ctx = document.getElementById("chart");
          window.graphic = new Chart(ctx, {
              type: 'line',
              scales: {
                  position:'left',
                  height: 250
              },
              data: {
                  labels: labels,
                  datasets: datasets
              },
              options: {
                  maintainAspectRatio: false,
                  scales: {
                    yAxes: [{
                      ticks: {
                        scaleOverride: true,
                        stepSize: stepSize,
                        scaleStepWidth: Math.ceil(maxValue / stepSize)
                      }
                    }]
                  }
              }
          });
      }
    }
  }
}