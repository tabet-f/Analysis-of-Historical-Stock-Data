<?php
include("db/dbconnecti.php");
?>

<!DOCTYPE html>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">


<title>Historical Stock Market Analysis | Mix Comparison</title>
<script src="js/hgraphone.js"></script>
<script src="js/hgraphtwo.js"></script>
<script src="js/hgraphthree.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>


<!-- bootstrap library -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    
     <!-- JQuery UI Datepicker-->
     <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
     
     <!-- Select2 JQuery-->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

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



#sectorDiv, #compDiv, #yearRangeDIV, #perORvolDIV{
    //float: left;
    width: auto;
   // height: 20px;
    //margin-right: 8px;
    margin-left:-20px;
    
    
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
    <nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="index.php" style="background-color:#424558; color:#ffffff; font-size:15pt;">Historical Stock Analysis</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="index.php">Bulk Comparison</a></li>
      <li class="active"><a href="single.php">Mix Comparison</a></li>
    </ul>
  
  
   <button id="scala"  type="submit" class="col-xs-3 col-sm-1 btn btn-primary btn-lg" onclick="displayAlert()">
  <span class="glyphicon glyphicon-off"></span> Check Scala</button>
  
  
  </div>
  
</nav>

    <!-- Nav bar ends -->
    
    
    
    
    
 
    
    
    
    
    
    
    
    
    
    <div class="container">
    
    <?php


	
$query = "SELECT Distinct Sector FROM meta_data ORDER BY Sector";

if ($result = $db->query($query)) {

 echo '<div id="sectorDiv" class="col-xs-6 col-sm-3">';
    echo "<select multiple id='sectors' name ='sectors' size='5' style='width:250px;'  class='form-control' onChange='SECTgetSelectedOptions(this)'>";
   
   /* fetch associative array */
    while ($row = $result->fetch_assoc()) {
    
    
    echo "<option value ='{$row['Sector']}'";
    echo ">{$row['Sector']}</option>";
       //printf ("%s\n", $row["Sector"]);
    
    
    }
    
    echo "</select>";
echo '<p id="clearSect" style="color:blue; margin-left:2px; margin-top:3px;">Clear</p>';
echo'</div>';
    /* free result set */
    $result->free();
}

/* close connection */
//$db->close();


?>


  
  
<?php
require_once("db/dbconnecti.php");
$query2 = "SELECT Company, SIndex FROM meta_data ORDER BY Company";
if ($result2 = $db->query($query2)) {

 echo '<div id="compDiv" class="col-xs-6 col-sm-3">';
    echo "<select multiple id='companies' styname ='companies' size='5' style='width:420px;' class='form-control'  onChange='COMPgetSelectedOptions(this)'  >";
   
   /* fetch associative array */
    while ($row2 = $result2->fetch_assoc()) {
    
    
    echo "<option value ='{$row2['SIndex']}'";
    echo ">{$row2['Company']}</option>";
       //printf ("%s\n", $row["Sector"]);
    
    
    }
    
    echo "</select>";
echo '<p id="clearComp" style="color:blue; margin-top:3px;">Clear</p>';
echo'</div>';
    /* free result set */
    $result2->free();
}

/* close connection */
//$db->close();

?>
    
    
    
     <div id ="yearRangeDIV" class="col-xs-6 col-sm-3">
       
    <select id="yearRange" class="form-control" onchange ="Year_onChangeGetValueSelected('yearRange');">
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
    
    
    <div id ="perORvolDIV" class="col-xs-6 col-sm-3">
       
    <select id="perORvol" class="form-control" onchange="percORvol_onChangeGetValueSelected('perORvol');">
    <option  value="choice" disabled="disabled" selected="selected">-- Choose Unit --</option>
    <option value="percgain">AVG % Gain</option>
    <option value="volume">Volume</option>
   
    </select>
    
    </div>
    
    
  
    
    
    
  
  <button id="submit"  type="button" class="btn btn-primary btn-sm" onclick="generateParams()">Generate</button>
    
    </div>
    <br/></br></br><br/>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    <div id="spinner" class="hidden" style="position:fixed;"></div>
    
    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto; margin-bottom:60;"></div>
    
    <br/><br/> <br/><br/> <br/><br/>
    <script>
    
    
     $(".container").hide();
    
    var $comp = $("#companies").select2();
    var $sect = $("#sectors").select2();
    
   
   
    
    //to fire clear <p> 
    $("#clearSect").on("click", function () { $sect.val(null).trigger("change"); });
    $("#clearComp").on("click", function () { $comp.val(null).trigger("change"); });
 
    //hide submit & specificdate if user remove everything from one of both select tag (real time change)
    $($sect).select2().on("change", function(e) { if($($sect).val()<=0 || $($comp).val()<=0){   $("#submit").hide();} else if(getSelectValue("yearRange")!="choice" && getSelectValue("perORvol")!="choice"){$("#submit").show();}})
    $($comp).select2().on("change", function(e) { if($($comp).val()<=0 || $($sect).val()<=0){   $("#submit").hide();} else if(getSelectValue("yearRange")!="choice" && getSelectValue("perORvol")!="choice"){$("#submit").show();}})
    
    
    
  $($comp).select2({
    placeholder: "Select a Company"
    //allowClear: true
    });
    $($sect).select2({
    placeholder: "Select a Sector"
    });
    
    
    
    
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
                    min: 0,
                    title: {
                        text: 'Requests'
                    },
                    labels: {
                        overflow: 'justify'
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
    
    
    





  
    <script>
    //Build PHP Params
    $("#perORvolDIV").hide();
    $("#yearRangeDIV").hide();
    $("#submit").hide();
    var Comparray = [];
    var Sectarray = [];
    
    
    function generateParams(){
    
    
   
   
     
    var Sectors = $('#sectors').val();
    var Companies = $('#companies').val();
    $YearRange = getSelectValue('yearRange');
    $Unit = getSelectValue('perORvol');
    
    //building sectParams
    var sectorsParams=" ";
    for (var i = 0; i < Sectors.length; i++) {
    if(sectorsParams===" "){
    sectorsParams=Sectors[i];
    }
    else{
    sectorsParams=sectorsParams+","+Sectors[i];
    }
  }
  
  
  //building compParams
  var companiesParams=" ";
    for (var i = 0; i < Companies.length; i++) {
    if(companiesParams===" "){
    companiesParams = Companies[i];
    }
    else{
    companiesParams = companiesParams+","+Companies[i];
    }
  }
  
   //Building Final Params
   $param="data/700.php?selectedSectors="+window.btoa(sectorsParams)+"&selectedCompanies="+window.btoa(companiesParams)+"&p="+window.btoa($YearRange)+"&u="+window.btoa($Unit);
   DrawGraph($param);
     console.log($param);

   }
   
   
   
  
    
    
function SECTgetSelectedOptions(sel) {
  var opts = [],
    opt;
  var len = len = sel.options.length;
  for (var i = 0; i < len; i++) {
    opt = sel.options[i];

    if (opt.selected) {
      opts.push(opt);
      Sectarray.push(opt);
      
      if(Sectarray.length>0 && Comparray.length>0){
      $("#yearRangeDIV").show();
     
      }   
    }
  }

  return opts;
}
    
    
 function COMPgetSelectedOptions(sel) {
  var opts = [],
    opt;
  var len = len = sel.options.length;
  for (var i = 0; i < len; i++) {
    opt = sel.options[i];

    if (opt.selected) {
      opts.push(opt);
      Comparray.push(opt);
      
      if(Comparray.length>0 && Sectarray.length>0){
       $("#yearRangeDIV").show();
      
      }   
    }
  }

  return opts;
}
   
   
    
  
   //these two events to clear the lists and show/hide yearDiv
   $("#clearComp").click(function(event) {
    $("#companies").val([]);
    //Sectarray.length=0;
    Comparray.length=0;
    if(Sectarray.length<=0 || Comparray.length<=0){
      $("#yearRangeDIV").hide();
      $("#perORvolDIV").hide();
      $("#submit").hide();
       resetSelectElement("yearRange");
       resetSelectElement("perORvol");
      } 
   }); 
   
   
    $("#clearSect").click(function(event) {
    $("#sectors").val([]);
    Sectarray.length=0;
    //Comparray.length=0;
    
    if(Sectarray.length<=0 || Comparray.length<=0){
      $("#yearRangeDIV").hide();
      $("#perORvolDIV").hide();
      $("#submit").hide();
       resetSelectElement("yearRange");
       resetSelectElement("perORvol");
      }  
    
   }); 
   
   function Year_onChangeGetValueSelected(selectTagID) {
  
    var selectBox = document.getElementById(selectTagID);
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    
    if(selectedValue!="choice"){
     $("#perORvolDIV").show();
    }

   }
   
    function percORvol_onChangeGetValueSelected(selectTagID) {
  
    var selectBox = document.getElementById(selectTagID);
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    
    if(selectedValue!="choice"){
     $("#submit").show();
    }

   }
    
    
   //function to reset input selectTag
   function resetSelectElement(selectElement) {
   document.getElementById(selectElement).selectedIndex = 0;
   }
   
   //function to get value from selected input in selectTag
   function getSelectValue(selectTagName){
    var selectBox = document.getElementById(selectTagName);
    var selectedValue = selectBox.options[selectBox.selectedIndex].value;
    return selectedValue;
   }
   
   
   
   
   
   
    function displayAlert(){
 
 var c="data/checkStatus.php?stat=CHECK";

 
  $.getJSON(c, function(json) {
  
//scala is on and db is updated 
 if(json==="ON&TRUE"){
 swal("Let's Get Started!", "Scala Has Started...Check Your Console", "success");
  $(".container").hide();
 $("#spinner").toggleClass("hidden");
$.getJSON("data/checkStatus.php?stat=DONE", function(json) {
  
$("#spinner").toggleClass("hidden");
 $(".container").show();

});

 
 
 }
 
 //scala is on and db is updated 
 else if(json==="ALREADYON"){
 swal("Scala Is Already Running!", "Scala Already ON", "success");
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


 



   
   
   
   function DrawGraph(URLparam){  
       
       // Create the chart
$(document).ready(function() {
           
           
            var options = {
                chart: {
                renderTo: 'container',
        type: 'area'
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
    
    
      <footer>
        <p>Developed By <a style="color:#0a93a6; text-decoration:none;" href="https://github.com/tabet-f/Analysis-of-Historical-Stock-Data" target="_blank">Team 4 - CSYE 7200</a></p>
        <span>Fadi Tabet - Vandana Iyer - Omkar Daphal</span>
         
    </footer>
    
    
    
</body>
</html>








