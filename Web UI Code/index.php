  

<!DOCTYPE html>
<html>
<head>
<title>Historical Stock Market Analysis | Bulk Comparison</title>
<script src="js/hgraphone.js"></script>
<script src="js/hgraphtwo.js"></script>
<script src="js/hgraphthree.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>
<!-- <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>-->




  
  
    
  

    <!-- bootstrap library -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    
    
     <!-- JQuery UI Datepicker-->
     <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
     
     
     <!--Sweet Alert-->
     <script src="js/sweetalert.min.js"></script>
     <link rel="stylesheet" type="text/css" href="js/sweetalert.css">
     
    <style>
        #chartdiv {
	width		: 100%;
	height		: 500px;
	font-size	: 11px;
}

#spinner {
  position: fixed;
  left: 0px;
  top: 0px;
  width: 100%;
  height: 100%;
  z-index: 9999;
  background: url('data/img/g.gif') 50% 50% no-repeat ;
}

#spinnerSCALA {

 
 
 position: fixed;
   position:fixed;
    left: 0px;
  top: 0px;
    height: 100%;
    width: 100%;
	z-index:10000;
	margin:0px;
	padding:0px;
	display:block;
     background: url('data/img/new.gif') 50% 50% no-repeat ;
}

#spinnerSCALA {

 
 
 position: fixed;
   position:fixed;
    left: 0px;
  top: 0px;
    height: 100%;
    width: 100%;
	z-index:10000;
	margin:0px;
	padding:0px;
	display:block;
     background: url('data/img/new.gif') 50% 50% no-repeat ;
     background-size: 700px 450px;
}


 
#selection, #level, #specificYearDIV, #dateRangeDIV, #yearRangeDIV, #TOPBOTTOM, #TOPBOTTOMYearDIV{
    //float: left;
    width: auto;
   // height: 20px;
    //margin-right: 8px;
 }
 

 

 /* Footer */

footer{
   background-color: #424558;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    text-align: center;
    color: #CCC;
}

footer p {
    padding: 10.5px;
    margin: 0px;
    line-height: 100%;
}


 .glyphicon.glyphicon-off {
    font-size: 25px;
   
}

#scala {
    min-width: 200px;
    max-width: 200px;
    max-height:50px;
    min-height:50px;
    float: right;
     font-size: 25px;
}

    
    



</style>
    
    


    
</head>

<body>




    
    
    <!-- nav bar -->
    <nav class="navbar navbar-default" style="border-width:0px;">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="index.php" style="background-color:#424558; color:#ffffff; font-size:15pt;">Historical Stock Analysis</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="index.php">Bulk Comparison</a></li>
      <li class="active"><a href="single.php">Mix Comparison</a></li>
       
      <!--
      <li><a href="index2.html">Compare</a></li>
      <li><a href="comparecharts.html">Over the Period</a></li>
      <li><a href="index3.html">Pie Chart</a></li>-->
    </ul>
   
    <button id="scala"  type="submit" class="col-xs-3 col-sm-1 btn btn-primary btn-lg" onclick="displayAlert()">
  <span class="glyphicon glyphicon-off"></span> Check Scala</button>


 
  </div>
  
</nav>

    <!-- Nav bar ends -->
    
    
<div class="container">
    
    <div id ="selection" class="col-xs-6 col-sm-3">
    
   
    <select id="bulkCompare" data-style="btn-danger" class="form-control" onchange="BulkCompare_onChangeGetValueSelected();">
    <option value="choice" disabled="disabled" selected="selected">-- Bulk Compare --</option>
    <option  value="s">Sector VS Sector</option>
    <option  value="c">Company VS Company</option>
    </select> 
   
    </div>
    	
    	
    <div id ="level" class="col-xs-6 col-sm-3">
       
    <select id="dateLevel" class="form-control" onchange="DateLevel_onChangeGetValueSelected();">
    <option  value="choice" disabled="disabled" selected="selected">-- Choose Analysis Level --</option>
    <option  value="specificYear">Specific Year</option>
    <option  value="dateRange">Date Range</option>
    <option  value="yearRange">Year Range</option>
    <option  value="DESC">Top List</option>
    <option  value="ASC">Bottom List</option>
    </select> 
    
    </div>
    
    
    <div id ="specificYearDIV" class="col-xs-6 col-sm-3">
       
    <select id="specificYear" class="form-control" onchange="submit_onChangeGetValueSelected('specificYear');">
    <option  value="choice" disabled="disabled" selected="selected">-- Choose Year --</option>
    <option value="2007">2007</option>
    <option value="2008">2008</option>
    <option value="2009">2009</option>
    <option value="2010">2010</option>
    <option value="2011">2011</option>
    <option value="2012">2012</option>
    <option value="2013">2013</option>
    <option value="2014">2014</option>
    <option value="2015">2015</option>
    <option value="2016">2016</option>
    </select>
    
    </div>
    
    <div id ="dateRangeDIV" class="col-xs-6 col-sm-3 form-group">  
    <div class="col-xs-4 col-sm-6"><input type="text"  class="form-control input-group-lg reg_name"  readonly="readonly" placeholder="From Date" id="datepickerFROM"></div>
    <div class="col-xs-4 col-sm-6"><input type="text"  class="form-control input-group-lg reg_name"  readonly="readonly" placeholder="To Date" id="datepickerTO"></div>
    </div>
   
   


    
    
    <div id ="yearRangeDIV" class="col-xs-6 col-sm-3">
       
    <select id="yearRange" class="form-control " onchange="submit_onChangeGetValueSelected('yearRange');">
    <option value="choice" disabled="disabled" selected="selected">-- Year Periods --</option>
    <option value="2">2 Years</option>
    <option value="3">3 Years</option>
    <option value="4">4 Years</option>
    <option value="5">5 Years</option>
    <option value="6">6 Years</option>
    <option value="7">7 Years</option>
    <option value="8">8 Years</option>
    <option value="9">9 Years</option>
    <option value="10">10 Years</option>
    </select> 
    </div>
    
    
    <div id ="TOPBOTTOMYearDIV" class="col-xs-6 col-sm-3">
       
    <select id="topbottomYear" class="form-control" onchange="onChangecheckValdation('topbottomYear');">
    <option  value="choice" disabled="disabled" selected="selected">-- Choose Year --</option>
    <option value="2007">2007</option>
    <option value="2008">2008</option>
    <option value="2009">2009</option>
    <option value="2010">2010</option>
    <option value="2011">2011</option>
    <option value="2012">2012</option>
    <option value="2013">2013</option>
    <option value="2014">2014</option>
    <option value="2015">2015</option>
    <option value="2016">2016</option>
    </select>
    
    </div>
    
    
     <div id ="TOPBOTTOM" class="col-xs-6 col-sm-3">
       
    <select id="limit" class="form-control" onchange="submit_onChangeGetValueSelected('limit');">
    <option  value="choice" disabled="disabled" selected="selected">-- Choose Position --</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
    </select> 
    </div>
    
    
    
    
    <button id="submit"  type="button" class="col-xs-3 col-sm-1 btn btn-primary btn-sm " onclick="generateParams()" >Generate</button>
      </div>
      <br/><br/><br/>
  
   
    
    
    
    
    
    
    
    <script>


 $(".container").hide();


//Limit date to 2007-01-01 to 2016-12-30
    $.datepicker.setDefaults({
          //showOn: 'button', 
          //buttonImage: 'images/calendar.gif', 
          //buttonImageOnly: true,
          changeMonth: true,
          changeYear: true,
          dateFormat: 'mm/dd/yy',
          minDate: '01/03/2007',
          maxDate: '12/30/2016'
    });
    $('#datepickerFROM').datepicker({
          onSelect: function(selectedDate) {
                $('#to_date').datepicker('option', 'minDate', selectedDate || '01/03/2007');
          }
    });
    $('#datepickerTo').datepicker({
          onSelect: function(selectedDate) {
                $('#frm_date').datepicker('option', 'maxDate', selectedDate || '12/30/2016');
          }
    });

    $(".datepick").datepicker({dateFormat:'yy-mm-dd',minDate:'01/03/2007' ,maxDate:'12/30/2016'});
    //limit part ends here
    
    
    
  //Datepicker method to show/hide submit button if date in from && to is not empty
  
  //check submit for FROM date
  $('#datepickerFROM').datepicker().on("change", function (e) {
    
     if($("#datepickerFROM").datepicker("getDate") != null && $("#datepickerTO").datepicker("getDate") != null) {
  $("#submit").show();
      }
    
    });
  //check submit for TO date
    $('#datepickerTO').datepicker().on("change", function (e) {
    
     if($("#datepickerFROM").datepicker("getDate") != null && $("#datepickerTO").datepicker("getDate") != null) {
  $("#submit").show();
      }
    
    });
    
    
    
    $("#level").hide();
    $("#specificYearDIV").hide();
    $("#dateRangeDIV").hide();
    $("#yearRangeDIV").hide();
    $("#TOPBOTTOM").hide();
    $("#TOPBOTTOMYearDIV").hide();
    $("#submit").hide();
    
    
    function BulkCompare_onChangeGetValueSelected() {
   $("#level").show();
    var selectBox = document.getElementById("bulkCompare");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    console.log(selectedValue);
    
   
     resetSelectElement("dateLevel");//reset selectTag
     resetSelectElement("yearRange");//reset selectTag
     resetSelectElement("limit");//reset selectTag
     resetSelectElement("topbottomYear");//reset selectTag
     resetDatePickers();//reset Datepickers
     
      $("#specificYearDIV").hide();
      $("#dateRangeDIV").hide();
      $("#yearRangeDIV").hide();
      $("#TOPBOTTOM").hide();
      $("#TOPBOTTOMYearDIV").hide();
      $("#submit").hide();
    
   }
   
  
   
    function DateLevel_onChangeGetValueSelected() {
  
    var selectBox = document.getElementById("dateLevel");
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    console.log(selectedValue);
    
    if(selectedValue=="specificYear"){
     
     resetSelectElement("specificYear");//reset selectTag
     resetSelectElement("yearRange");//reset selectTag
     resetSelectElement("limit");//reset selectTag
     resetSelectElement("topbottomYear");//reset selectTag
     resetDatePickers();//reset Datepickers
     
     $("#specificYearDIV").show();
     $("#dateRangeDIV").hide();
     $("#yearRangeDIV").hide();
     $("#TOPBOTTOM").hide();
     $("#TOPBOTTOMYearDIV").hide();
      $("#submit").hide();
   
   }
    
    
    if(selectedValue=="dateRange"){
    $("#specificYearDIV").hide();
    $("#dateRangeDIV").show();
    $("#yearRangeDIV").hide();
    $("#TOPBOTTOM").hide();
    $("#TOPBOTTOMYearDIV").hide();
    $("#submit").hide();
    
    resetSelectElement("specificYear");//reset selectTag
    resetSelectElement("yearRange");//reset selectTag
    resetSelectElement("limit");//reset selectTag
    resetSelectElement("topbottomYear");//reset selectTag
    resetDatePickers();//reset Datepickers
     
    }
    
    
    if(selectedValue=="yearRange"){
    $("#specificYearDIV").hide();
    $("#dateRangeDIV").hide();
    $("#yearRangeDIV").show();
    $("#TOPBOTTOM").hide();
    $("#TOPBOTTOMYearDIV").hide();
    $("#submit").hide();
    
    //reset datepickers
    resetDatePickers();//reset Datepickers
    resetSelectElement("specificYear");//reset selectTag
    resetSelectElement("limit");//reset selectTag
    resetSelectElement("topbottomYear");//reset selectTag
      
    }
    
   
   
    if(selectedValue=="DESC" || selectedValue =="ASC"){
      $("#specificYearDIV").hide();
      $("#dateRangeDIV").hide();
      $("#yearRangeDIV").hide();
      $("#TOPBOTTOMYearDIV").show();
      $("#TOPBOTTOM").hide();
      $("#submit").hide();
      resetSelectElement("topbottomYear");//reset selectTag
      resetSelectElement("limit");//reset selectTag
    }
    
     
    
   
   }//end of func
   
   //only used for top & bottom to show top 5 or so on.
   function onChangecheckValdation(selectElement){
    
    //check if topbottomyear is selected
    if($("#TOPBOTTOMYearDIV").is(":visible")){
     $topbottomYear = getSelectValue(selectElement);
   
    if($topbottomYear!="choice"){
    
    $("#specificYearDIV").hide();
    $("#dateRangeDIV").hide();
    $("#yearRangeDIV").hide();
    $("#TOPBOTTOM").show();
    //$("#submit").hide();
   
    //reset datepickers
    resetDatePickers();//reset Datepickers
    resetSelectElement("specificYear");//reset selectTag
    resetSelectElement("yearRange");//reset selectTag
    //resetSelectElement("limit");//reset selectTag
    }
    
    }
   }
   
   //function to reset input selectTag
   function resetSelectElement(selectElement) {
   document.getElementById(selectElement).selectedIndex = 0;
   }
    
    //function to reset dataPickerUI
   function resetDatePickers() {
     //reset Datapicker
     var $dates = $('#datepickerFROM, #datepickerTO').datepicker();
     $dates.datepicker('setDate', null);
   }
   
   //function to get value from selected input in selectTag
   function getSelectValue(selectTagName){
    var selectBox = document.getElementById(selectTagName);
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    return selectedValue;
   }
   
   
   //check to make sure that end date is bigger than start date
   function checkDateRange(from,to){
   var difference = (to- from) / (86400000 * 7);
   if (difference> 0) {
       alert("START Date Must be Greater Than END Date");
       return false;
   }
   return true;
   }
   
   //convert date to yyyy-mm-dd format
  function convertDate(date) {
  var yyyy = date.getFullYear().toString();
  var mm = (date.getMonth()+1).toString();
  var dd  = date.getDate().toString();
  var mmChars = mm.split('');
  var ddChars = dd.split('');
  return yyyy + '-' + (mmChars[1]?mm:"0"+mmChars[0]) + '-' + (ddChars[1]?dd:"0"+ddChars[0]);
  }
   
   
   
   //function to show/hide submit button
   function submit_onChangeGetValueSelected(selectElement) {$value = getSelectValue(selectElement);if($value!="choice"){$("#submit").show();}else{$("#submit").hide();}}
   
   
   //Build PHP Params
   function generateParams(){
   
   //(sector vs sector) or (company vs company)?
   $bulk = getSelectValue("bulkCompare");
   
   
   
   if($("#specificYearDIV").is(":visible")){
   $value = getSelectValue("specificYear");
   $param="data/600.php?g="+$bulk+"&y="+$value;
   DrawGraph($param);
   }
   
   
   
   if($("#yearRangeDIV").is(":visible")){
   $value = getSelectValue("yearRange");
   $param="data/600.php?g="+$bulk+"&p="+$value;
   DrawGraph($param);
   }
   
   
   
   
   if($("#TOPBOTTOM").is(":visible")){
    
   $value = getSelectValue("dateLevel");//ASC or DESC
   $limit = getSelectValue("limit");//LIMIT 1,2 or 3 and so on.
   $year = getSelectValue("topbottomYear");//for which year
  
   //TOP:LIST (top 5)
   if($value=="DESC"){
   $param="data/600.php?y="+$year+"&c="+$bulk+"&s="+$value+"&l="+$limit+"&txt=TOP";
   DrawGraph($param);
   }
   //BOTTOM:LIST (last 5)
   else{
   $param="data/600.php?y="+$year+"&c="+$bulk+"&s="+$value+"&l="+$limit+"&txt=BOTTOM";
   DrawGraph($param);
   }
 }
 
   if($("#dateRangeDIV").is(":visible")){

   $from = $("#datepickerFROM").datepicker( "getDate" );
   $to = $("#datepickerTO").datepicker( "getDate" );
   
     //if start date <end date ==>build param
     if(checkDateRange($to,$from)===true){
     $f=convertDate($from);
     $t=convertDate($to);
     $param="data/600.php?g="+$bulk+"&f="+$f+"&t="+$t;
     console.log($param);
     DrawGraph($param);
  
     }
  }
   
    
   
   
   
 }//end of function build param
 
 

 function displayAlert(){
 
 var c="data/checkStatus.php?stat=CHECK";

 
  $.getJSON(c, function(json) {
  
//scala is on and db is updated 
 if(json==="ON&TRUE"){
 swal("Let's Get Started!", "Scala Has Started...Check Your Console", "success");
  $(".container").hide();
 $("#spinnerSCALA").toggleClass("hidden");
$.getJSON("data/checkStatus.php?stat=DONE", function(json) {
  
$("#spinnerSCALA").toggleClass("hidden");
 $(".container").show();

});

 
 
 }
 
 //scala is on and db is updated 
 else if(json==="ALREADYON"){
 swal("Scala Is Already Done!", "Scala Finished Execution", "success");
  $(".container").show();
 
 
 }
 

//scala is on but error happened while updating row in db 
else if(json==="ON&FALSE"){
 
 swal({
  	title: "Communication Error",
  	text: "Couldn't Reach Scala",
  	type: "error",
  	confirmButtonText: "Ok"
  	})
 
 
}

//scala is off
else if(json==="OFF"){
   swal({
  title: "Scala is OFF",
  text: "Run Your Scala Application First",
  type: "error",
  confirmButtonText: "Ok"
  })

}
  
  });





 
 }


    </script>
    
    
    
    <div id="spinnerSCALA" class="hidden" style="position:fixed;"></div>
    <div id="spinner" class="hidden" style="position:fixed;"></div>
    
    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto; margin-bottom:60;"></div>
    
   
    
      
 <script>
 
function DrawGraph(URLparam){  
       
       // Create the chart
$(document).ready(function() {
           
           
            var options = {
                chart: {
                    renderTo: 'container',
                    type: 'column'
                },
               title: {
                    text: 'Project Requests',
                    x: -20 //center
                },
                subtitle: {
                    text: '',
                    x: -20
                },
                xAxis: {
                    categories: []
                },
                yAxis: {
                   
                    title: {
                        text: 'Requests'
                    }
                },
                
               plotOptions: {
        bar: {
            dataLabels: {
                enabled: true
            },
            enableMouseTracking: true
        },
        
         pie: {
            innerSize: 100,
            depth: 45
        }
    },
    
                series: []
            }
            $("#spinner").toggleClass("hidden");//y=2014&c=s&s=DESC&txt=bottom&l=3
            $("#container").hide();
            $.getJSON(URLparam, function(json) {
             $("#spinner").toggleClass("hidden");
             $("#container").show();
             //console.log(JSON.stringify(json));
             //CHECK IF NO DATA IS RECEIVED FROM SERVER
             if (json.toString()==="NOR") {
             alert("NO DATA RECEIVED FROM SERVER CHECK YOUR PARAMS");
             console.log("NO DATA!")
             }
               
               //ELSE DATA IS RECEIVED..CONTINUE
               else{
                            
               
                console.log("Data Fully Received From Server");
                //GRAPH OPTIONS 
                var GraphOptionsArray = json[1]['data'];
                var splittedArray = GraphOptionsArray.toString();
                var CommaSeperated = splittedArray.split(",");
                //console.log(splittedArray); print array
                //CommaSeperated[0]=> Graph Title,  CommaSeperated[1]=> Graph SubTitle, //CommaSeperated[2]=> Graph Y-Axis, //CommaSeperated[3]=> Chart Type (Pie,Bar...)
                options.title.text = CommaSeperated[0];
                options.subtitle.text = CommaSeperated[1];
                options.yAxis.title.text = CommaSeperated[2];
		options.chart.type = CommaSeperated[3];
		
		//x-axis label
                options.xAxis.categories = json[0]['data'];
		//options.plotOptions.series.pointStart = json[0]['data'];
        
		
		//SQL DATA
		var c = json.length-2; //size of json array - 2 because the first sub array are for x-axis and graph Options
		var ji = 2;
		var i =0;
		while(i<c){
		options.series[i] = json[ji];
		i++;
		ji++
		}
		
                
                /*
		//data goes here
                options.series[0] = json[2];
                options.series[1] = json[3];
                options.series[2] = json[4];
                options.series[3] = json[5];
                options.series[4] = json[6];
                */
                chart = new Highcharts.Chart(options);
               }
               
               
            });
           
         
             
        });
      }
    </script>
    
  <br/><br/>
    <footer>
        <p>Developed By <a style="color:#0a93a6; text-decoration:none;" href="https://github.com/tabet-f/Analysis-of-Historical-Stock-Data" target="_blank">Team 4 - CSYE 7200</a></p>
        <span>Fadi Tabet - Vandana Iyer - Omkar Daphal</span>
         
    </footer>
    
</body>
</html>