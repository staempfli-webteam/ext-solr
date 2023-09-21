import { Chart, registerables } from './Chart/chart.esm.js'
Chart.register(...registerables);

var ctx = document.getElementById('queriesOverTime');
var queryChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: queryLabels,
    datasets: [
      {
        data: queryData,
        label: "# of Queries",

        lineTension: 0.1,
        fill: false,
        backgroundColor: 'rgba(206, 43, 23, 0.4)',

        borderColor: 'rgba(206, 43, 23, 1)',
        borderCapStyle: 'round',
        borderJoinStyle: 'round',

        pointRadius: 2,
        pointHitRadius: 10,
        pointBorderColor: 'rgba(206, 43, 23, 1)',
        pointBackgroundColor: '#fff',
        pointBorderWidth: 1,

        pointHoverRadius: 7,
        pointHoverBackgroundColor: 'rgba(206, 43, 23, 1)',
        pointHoverBorderColor: '#fff',
        pointHoverBorderWidth: 3
      }
    ]
  },
  options: {
    animation: {
      duration: 0
    },
    legend: {
      display: false
    },
    tooltips: {
      cornerRadius: 3
    },
    scales: {
      y: {
        beginAtZero: true,
        gridLines: {
          drawBorder: false
        }
      },
      x: {
        gridLines: {
          display: false
        }
      }
    }
  }
});