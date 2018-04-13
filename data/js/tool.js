var box = function (type , title , message , func = null , size = "small"){
    if(type == 'alert'){
        bootbox.alert({
            size: size,
            title: title,
            message: message, 
            callback: func
        });
    }else if(type == 'confirm'){
        bootbox.confirm({
            size: size,
            title: title,
            message: message, 
            callback: func
        });
    }
};

var isJsonFormat = function ( str ) {  
    try {  
        $.parseJSON(str);  
    } catch (e) {  
        return false;  
    }  
    return true;  
}; 

var random = function(range){
    return Math.round(Math.random()*10000000000)%range;
};

var chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)'
};

var color_a = function(rgb,a){
    return "rgba("+rgb[0]+","+rgb[1]+","+rgb[2]+","+a+")";
}