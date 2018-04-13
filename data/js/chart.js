
$(function(){

	ctx1 = $("#myChart1")[0].getContext('2d');
	color_arr = [];
	bgc = [];
	bc =[];
	for(i = 0 ; i < 6 ; i++){
		color_arr[i] = [];
		for(j = 0 ; j < 3 ; j ++){
			color_arr[i][j] = random(255);
		}
		bgc[i] = color_a(color_arr[i],0.2);
		bc[i] = color_a(color_arr[i],1);
	}

	chart1 = new Chart(ctx1, {
	    type: 'bar',
	    data: {
	        labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
	        datasets: [{
	            label: '# of Votes',
	            data: [12, 19, 3, 5, 2, 3],
	            backgroundColor: bgc,
	            borderColor: bc,
	            borderWidth: 1
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero:true
	                }
	            }]
	        }
	    }
	});
	arr = [];
	for(i = 0 ; i < 6 ; i++){
		arr[i] = random(100);
	}
	ctx2 = $("#myChart2")[0].getContext('2d');
	chart2 = new Chart(ctx2, {
	    type: 'pie',
	    data: {
	        labels: arr,//["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],
	        datasets: [{
	            label: arr,
	            data: [12, 19, 3, 5, 2, 3],
	            backgroundColor: bgc,
	            borderColor: bc,
	            borderWidth: 1
	        }]
	    },
	    
	});

	$("#btn_bar").click(function(){
		chart1.config.data.datasets[0].type = 'bar';
		chart1.update();
	});

	$("#btn_line").click(function(){
		chart1.config.data.datasets[0].type = 'line';
		chart1.update();
	});

	console.log(random(256));
});