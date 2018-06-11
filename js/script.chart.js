/* Wont be used currently */





var ctx = document.getElementById("myChart").getContext('2d');

var data = {
    datasets: [{
        data: [10, 20, 30],
        backgroundColor: [
          "rgb(255, 99, 132)",
          "rgb(75, 192, 192)",
          "rgb(54, 162, 235)"
      ],
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
        'Red',
        'Yellow',
        'Blue'
    ]
};
var options = {
  legend: {
    display: false
  }
};

var myPieChart = new Chart(ctx,{
    type: 'pie',
    data: data,
    options: options
});
