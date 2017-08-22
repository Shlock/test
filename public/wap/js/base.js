var ZY = new Object();
jQuery(function($){ 

    $.setDate = function(target){
        /*$(target).datepicker();*/
        var currYear = (new Date()).getFullYear();
        $(target).mobiscroll('destroy').date({
            startYear: currYear,
            endYear: currYear + 10,
            theme:"android",
            mode:"mixed",
            lang:"zh",
            display:"modal",
            animate:"flip"
        });
    }

    $.setDateTime = function(target){
        /*$(target).datetimepicker({
            controlType:"select",
        });*/
        var currYear = (new Date()).getFullYear();
        $(target).mobiscroll('destroy').datetime({
            minWidth:46,
            startYear: currYear,
            endYear: currYear,
            theme:"android",
            mode:"mixed",
            lang:"zh",
            display:"modal",
            animate:"flip", 
            timeFormat:"HH:ii",
            timeWheels:"HH",
        });
    }
});