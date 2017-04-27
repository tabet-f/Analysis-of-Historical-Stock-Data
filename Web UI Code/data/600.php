<?php
/*THIS CODE IS USED TO RUN SQL QUERIES & SEND IT AS JSON TO THE FRONTEND*/

$con = mysql_connect("localhost", "hinemel_vandy17", "PASSWORD GOES HERE CAN'T BE SHOWN");
 
if (!$con) {
  die('Could not connect: ' . mysql_error());
}
 
mysql_select_db("hinemel_ScalaSpark", $con);



$YEAR = $_GET['y']; //y=2011 ex ONE SINGLE YEAR (EX: 2011)
$GROUP = $_GET['g']; //c= COMPANY ||  s=SECTOR
$FROM = $_GET['f']; //from date
$TO = $_GET['t']; //to date
$PERIOD = $_GET['p'];//period range like 5 years
$COLLECTION = $_GET['c']; //same as $GROUP
$LIMIT = $_GET['l'];//limit 
$SORT = $_GET['s'];//Sorting Desc or Asc
$Notelowercase = $_GET['txt'];//note like Top or Bottom used in title only
$NOTE=strtoupper($Notelowercase);//make it upper case


//CHECK IF PARAMETERS ARE NOT EMPTY
if (!empty($YEAR) && !empty($GROUP)) {



if($GROUP==="s"){

sectorVSsectorYEAR($YEAR);

}
//**************************************************************************************************************


if($GROUP==="c"){

companyVScompanyYEAR($YEAR);

}
//**************************************************************************************************************



}//end IF PARAMETERS ARE NOT EMPTY









//CHECK IF PARAMETERS ARE NOT EMPTY
if (!empty($FROM) && !empty($TO) && !empty($GROUP)) {



if($GROUP==="s"){
SectorVSSectorDATERANGE($FROM, $TO);
}
//**************************************************************************************************************


if($GROUP==="c"){

CompanyVSCompanyDATERANGE($FROM, $TO);

}
//**************************************************************************************************************
}//end IF PARAMETERS ARE NOT EMPTY





//CHECK IF PARAMETERS ARE NOT EMPTY
if (!empty($PERIOD) && !empty($GROUP)) {



if($GROUP==="s"){
SECTORoverYEARPERIOD($PERIOD);
}
//**************************************************************************************************************


if($GROUP==="c"){

COMPANYoverYEARPERIOD($PERIOD);

}
//**************************************************************************************************************
}//end IF PARAMETERS ARE NOT EMPTY







//CHECK IF PARAMETERS ARE NOT EMPTY
if (!empty($YEAR) && !empty($COLLECTION) && !empty($SORT) && !empty($LIMIT) && !empty($NOTE)) {



if($COLLECTION==="s"){
	
     if($SORT==="DESC"){//TOP 
	
	TOP_OR_BOTTOM_SECTOR_BY_YEAR($YEAR, $LIMIT, $SORT, $NOTE);
	
     }
     else if ($SORT==="ASC"){//BOTTOM
     
     TOP_OR_BOTTOM_SECTOR_BY_YEAR($YEAR, $LIMIT, $SORT, $NOTE);
     
     }


}
//**************************************************************************************************************


if($COLLECTION==="c"){

     if($SORT==="DESC"){//TOP 
	TOP_OR_BOTTOM_COMPANY_BY_YEAR($YEAR, $LIMIT, $SORT, $NOTE);
	
     }
     else if ($SORT==="ASC"){//BOTTOM
       TOP_OR_BOTTOM_COMPANY_BY_YEAR($YEAR, $LIMIT, $SORT, $NOTE);
     
     }


}
//**************************************************************************************************************



}//end IF PARAMETERS ARE NOT EMPTY




/*
//ELSE IF ONE OF PARAMETERS IS EMPTY
else{print json_encode("NOR");}
*/



 
function sectorVSsectorYEAR($YEAR) {
//QUERY CODE#1: Yearly total volume by sector for specific year
/* 
Yearly total volume by sector for specific year
SELECT sum(volume),sector
FROM datasource.stocks
where d like &#39;2016%&#39;
group by sector
*/

$query = mysql_query("SELECT SUM(Volume) AS 'Vol', Sector FROM parsed_datasource WHERE Date LIKE '%$YEAR%' GROUP BY Sector");

$num_rows = mysql_num_rows($query);//get num of rows returned by query

//if numb of rows returned is > 0 then we have results
if($num_rows>0){

$i=1;//query index counter
$QueryResult = array(); //on Index 1 which is $i will hold another array that has 2 indexes 0 and 1 where 0->Sector Name and 1->Vol Amount

while($r = mysql_fetch_array($query)) {
    $querySector = $r['Sector'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    $QueryResult[$i] = array($querySector,$rounded);//Add QueryResult[1] ==> (array[0]=Sectorname & array[1]=vol amount)
    $i++;
}


//Specify the Category or x-Axis Group
$category = array();
$category['name'] = 'x-Axis Label';

//Here goes the legend or bar col NAMES which found at $QueryResult[i][0] 
$series1 = array();
$series1['name'] = $QueryResult[1][0];//GETTING SECTOR NAME

$series2 = array();
$series2['name'] =  $QueryResult[2][0];
 
$series3 = array();
$series3['name'] = $QueryResult[3][0];

$series4 = array();
$series4['name'] = $QueryResult[4][0];
 
$series5 = array();
$series5['name'] = $QueryResult[5][0];

    $graphOptions = array();
    $graphOptions['data'][] = "Sector VS Sector ({$YEAR})"; //index 1: Graph Title
    $graphOptions['data'][] = "Yearly Total Volume Per Sector"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "bar"; //index 4: Graph Type (col chart, bar chart, pie chart...)

    $category['data'][] = $YEAR; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)

    $series1['data'][] =  $QueryResult[1][1];
    $series2['data'][] =  $QueryResult[2][1];
    $series3['data'][] =  $QueryResult[3][1];
    $series4['data'][] =  $QueryResult[4][1];
    $series5['data'][] =  $QueryResult[5][1];
    
$result = array();

array_push($result,$category);
array_push($result,$graphOptions);
array_push($result,$series1);
array_push($result,$series2);
array_push($result,$series3);
array_push($result,$series4);
array_push($result,$series5);
 
//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

//ELSE IF QUERY FAILED SEND THE WORD 'NOR' AS SIGNAL THAT AN ERROR HAPPENED AND NO DATA COULD BE FETCHED FROM DB
else{print json_encode("NOR");}  

}




function companyVScompanyYEAR($YEAR){
	
$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', meta_data.Company AS compName FROM parsed_datasource, meta_data WHERE parsed_datasource.Date LIKE '%$YEAR%' &&  parsed_datasource.Index = meta_data.SIndex GROUP BY parsed_datasource.Index");

$num_rows = mysql_num_rows($query);//get num of rows returned by query
//echo $num_rows;
//if numb of rows returned is > 0 then we have results
if($num_rows>0){

$i=1;//query index counter start at 1 because index 0 is reserved for
$QueryResult = array(); //on Index 1 which is $i will hold another array that has 2 indexes 0 and 1 where 0->Sector Name and 1->Vol Amount

while($r = mysql_fetch_array($query)) {
    $querySector = $r['compName'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    $QueryResult[$i] = array($querySector,$rounded);//Add QueryResult[1] ==> (array[0]=Sectorname & array[1]=vol amount)
    $i++;
}

  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 

  //dynamic part -> DONT TOUCH IT
 $oxx=1;
 while($oxx<=$num_rows){
     ${"series$oxx"}=array();
     ${"series$oxx"}['name'] = $QueryResult[$oxx][0];//GETTING SECTOR NAME
     $oxx++;
    }
    

    $graphOptions = array();
    $graphOptions['data'][] = "Company VS Company ({$YEAR})"; //index 1: Graph Title
    $graphOptions['data'][] = "Yearly Total Volume Per Company"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "column"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
    $category['data'][] = $YEAR; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
    while($c<=$num_rows){
     ${"series$c"}['data'][] =  $QueryResult[$c][1];
     array_push($result,${"series$c"});
     //echo"{$c}<br/>";
     $c++;
    }
    


//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

//ELSE IF QUERY FAILED SEND THE WORD 'NOR' AS SIGNAL THAT AN ERROR HAPPENED AND NO DATA COULD BE FETCHED FROM DB
else{print json_encode("NOR");}
}
 
 
 
 
 
//Get Vol for each Sector For Period of years like 5 years from 2007 to 2011
//One to many relationship
function SECTORoverYEARPERIOD($PERIOD){


$startyear=2007;//start year
$yearsperiod=$PERIOD;
$untilyear = $startyear + $yearsperiod;


//first put all sectors names in 1 array each on different index
$SectorNamesArray = array();
$i=0;
$SectorNamesquery = mysql_query("SELECT Distinct Sector FROM parsed_datasource GROUP BY Sector");
while($r = mysql_fetch_array($SectorNamesquery)) {
    $getselectSector = $r['Sector'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $SectorNamesArray[$i] = $getselectSector;//Add QueryResult[1] ==> (Sectorname and so on at each index)
	$i++;
}


//STEP1: GET DATE->VOL OVER THE PERIOD (2012 TO 2015) FOR EACH SECTOR IN AN INDEPENDENT ARRAY CALLED DATARESULT 
$SectorArraySize = sizeof($SectorNamesArray);//get sector array SIZE
if($PERIOD>0){
$outer=0;//Outer Loop Iteration Counter
$Inner=0;//Query Fetch Loop Array Index Counter

while($outer<$SectorArraySize){ //this iteration will go untill all sectors index has been looped through
	$iterationSectorName = $SectorNamesArray[$outer];
	
	$startyear=2007;//reset Start Year Again for Next Sector
	
	${"DataResult$outer"}=array(); //Dynamic 2D Array Creation For Each Sector to hold year->vol 
	
	
	//echo"OUTER call#{$outer} for $iterationSectorName<br/>";
	//getting data for each of these sector index but for 10 years (so 1 to many)

    while($startyear<$untilyear){//generating query for each year where startyear is the loop counter and incremented by 1 year on each iteration
	
	$query = mysql_query("SELECT SUM(Volume) AS 'Vol', Date FROM parsed_datasource WHERE Sector = '$iterationSectorName' and Date Like '%$startyear%' GROUP BY Sector");

	//echo"INNER call: getting vol for {$startyear} & Sector: {$iterationSectorName}<br/>";
	
	//getting query result
	while($r = mysql_fetch_array($query)) {
    //$querySector = $r['Sector'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    ${"DataResult$outer"}[$Inner] = array($startyear,$rounded);//Add QueryResult[1] ==> (array[0]=YEAR & array[1]=vol amount)
	$Inner++;
}//end of fetch loop

     $startyear++;//Increment to next year
	
}//end of inner year loop	
	
 $outer++;//Get next Sector from sector array

}//end of sector outer loop 


/*RESULT OF THE ABOVE LOOP
print_r($DataResult0);	
print_r($DataResult1);	
print_r($DataResult2);
print_r($DataResult3);
print_r($DataResult4);
*/


//STEP2: EXTRACT VOLUME FROM 2D ARRAY THAT HAS YEAR->VOL AND PUSH ALL VOLUMES TO ARRAY $getOnlyVolfromDoubleArray
$c=0;
$yearsArray = array();
while($c<$SectorArraySize){

$arName = ${"DataResult$c"};
$sizeofArray = sizeof($arName);
$i=0;
${"getOnlyVolfromdataArray$c"} = array();
foreach ( $arName as $var ) {
   
    ${"getOnlyVolfromdataArray$c"}[] = $var[1];
    $yearsArraywithDuplicate[]=$var[0]; //all years
  
    //array_push(${"getOnlyVolfromdataArray$c"},$var[1]); //$var[1] is the volume 
}

$c++;
}
$yearsArray = array_unique($yearsArraywithDuplicate);

/*RESULT OF ABOVE LOOP
print_r($getOnlyVolfromdataArray0);
print_r($getOnlyVolfromdataArray1);
print_r($getOnlyVolfromdataArray2);
print_r($getOnlyVolfromdataArray3);
print_r($getOnlyVolfromdataArray4);
*/



  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 

  //dynamic part -> DONT TOUCH IT
 $oxx=0;
 $sizeofArraySector = sizeof($SectorNamesArray);
  $f=1;
 while($oxx<$sizeofArraySector){
     ${"series$f"}=array();
     ${"series$f"}['name'] = $SectorNamesArray[$oxx];
    $f++;
     $oxx++;
    }
    
    //these 2 are just used for title
    $exactSTART = $startyear-$PERIOD;
    $exactEND = $untilyear-1;
    
    $graphOptions = array();
    $graphOptions['data'][] = "Yearly Total Volume Per Sector for ({$PERIOD} years)"; //index 1: Graph Title
    $graphOptions['data'][] = "From: {$exactSTART}  To: {$exactEND}"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "area"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
    
     $sizeofYearArray= sizeof($yearsArray);
    $q=0;
    while($q<$sizeofYearArray){
     $category['data'][] = $yearsArray[$q]; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
     $q++;
    }
    
   
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
 
    $x=0;
    $g=0;
    
    while($g<$SectorArraySize){
    
    $sizeofVolArray = sizeof (${"getOnlyVolfromdataArray$g"});
    
    	while($x<$sizeofVolArray){
    	${"series$c"}['data'][] =  ${"getOnlyVolfromdataArray$g"}[$x];
    	$x++;
    	}
     array_push($result,${"series$c"});
     $x=0;
     $c++;
     $g++;
     //p++;
    }
    


//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

else{print json_encode("NOR");}

} 
 
 
 
 
 
 
 
 
 
//Get Vol for each Sector For Period of years like 5 years from 2007 to 2011
//One to many relationship
function COMPANYoverYEARPERIOD($PERIOD){


$startyear=2007;//start year
$yearsperiod=$PERIOD;
$untilyear = $startyear + $yearsperiod;


//first put all sectors names in 1 array each on different index
$SectorNamesArray = array();
$IndexNamesArray = array();
$i=0;
$SectorNamesquery = mysql_query("SELECT Company, SIndex FROM meta_data GROUP BY SIndex");
while($r = mysql_fetch_array($SectorNamesquery)) {
    $getselectSector = $r['Company'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $getselectINDEX= $r['SIndex'];
    $SectorNamesArray[$i] = $getselectSector;//Add QueryResult[1] ==> (Sectorname and so on at each index)
    $IndexNamesArray[$i] = $getselectINDEX;//Add QueryResult[1] ==> (Sectorname and so on at each index)
	$i++;
}



//STEP1: GET DATE->VOL OVER THE PERIOD (2012 TO 2015) FOR EACH SECTOR IN AN INDEPENDENT ARRAY CALLED DATARESULT 
$SectorArraySize = sizeof($SectorNamesArray);//get sector array SIZE
if($PERIOD>0){
$outer=0;//Outer Loop Iteration Counter
$Inner=0;//Query Fetch Loop Array Index Counter

while($outer<$SectorArraySize){ //this iteration will go untill all sectors index has been looped through
	$iterationSectorName = $IndexNamesArray[$outer];
	
	$startyear=2007;//reset Start Year Again for Next Sector
	
	${"DataResult$outer"}=array(); //Dynamic 2D Array Creation For Each Sector to hold year->vol 
	
	
	//echo"OUTER call#{$outer} for $iterationSectorName<br/>";
	//getting data for each of these sector index but for 10 years (so 1 to many)

    while($startyear<$untilyear){//generating query for each year where startyear is the loop counter and incremented by 1 year on each iteration
	
	$query = mysql_query("SELECT SUM(Volume) AS 'Vol',Date FROM parsed_datasource WHERE parsed_datasource.Index = '$iterationSectorName' and Date Like '%$startyear%' GROUP BY parsed_datasource.Index");

	//echo"INNER call: getting vol for {$startyear} & Sector: {$iterationSectorName}<br/>";
	
	//getting query result
	while($r = mysql_fetch_array($query)) {
    //$querySector = $r['Sector'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    ${"DataResult$outer"}[$Inner] = array($startyear,$rounded);//Add QueryResult[1] ==> (array[0]=YEAR & array[1]=vol amount)
	$Inner++;
}//end of fetch loop

     $startyear++;//Increment to next year
	
}//end of inner year loop	
	
 $outer++;//Get next Sector from sector array

}//end of sector outer loop 


/*RESULT OF THE ABOVE LOOP
print_r($DataResult0);	
print_r($DataResult1);	
print_r($DataResult2);
print_r($DataResult3);
print_r($DataResult4);
*/



//STEP2: EXTRACT VOLUME FROM 2D ARRAY THAT HAS YEAR->VOL AND PUSH ALL VOLUMES TO ARRAY $getOnlyVolfromDoubleArray
$c=0;
$yearsArray = array();
while($c<$SectorArraySize){

$arName = ${"DataResult$c"};
$sizeofArray = sizeof($arName);
$i=0;
${"getOnlyVolfromdataArray$c"} = array();
foreach ( $arName as $var ) {
   
    ${"getOnlyVolfromdataArray$c"}[] = $var[1];
    $yearsArraywithDuplicate[]=$var[0]; //all years
  
    //array_push(${"getOnlyVolfromdataArray$c"},$var[1]); //$var[1] is the volume 
}

$c++;
}
//print_r($yearsArraywithDuplicate);
$yearsArray = array_unique($yearsArraywithDuplicate);


/*RESULT OF ABOVE LOOP
print_r($getOnlyVolfromdataArray0);
print_r($getOnlyVolfromdataArray1);
print_r($getOnlyVolfromdataArray2);
print_r($getOnlyVolfromdataArray3);
print_r($getOnlyVolfromdataArray4);
*/



  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 

  //dynamic part -> DONT TOUCH IT
 $oxx=0;
 $sizeofArraySector = sizeof($SectorNamesArray);
  $f=1;
 while($oxx<$sizeofArraySector){
     ${"series$f"}=array();
     ${"series$f"}['name'] = $SectorNamesArray[$oxx];
    $f++;
     $oxx++;
    }
    
    //these 2 are just used for title
    $exactSTART = $startyear-$PERIOD;
    $exactEND = $untilyear-1;
    
    $graphOptions = array();
    $graphOptions['data'][] = "Yearly Total Volume Per Company for ({$PERIOD} years)"; //index 1: Graph Title
    $graphOptions['data'][] = "From: {$exactSTART}  To: {$exactEND}"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "line"; //index 4: Graph Type (col chart, bar chart, pie chart...)


    $sizeofYearArray= sizeof($yearsArray);
    $q=0;
    while($q<$sizeofYearArray){
     $category['data'][] = $yearsArray[$q]; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
     $q++;
    }
       
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
 
    $x=0;
    $g=0;
    
    while($g<$SectorArraySize){
    
    $sizeofVolArray = sizeof (${"getOnlyVolfromdataArray$g"});
    
    	while($x<$sizeofVolArray){
    	${"series$c"}['data'][] =  ${"getOnlyVolfromdataArray$g"}[$x];
    	$x++;
    	}
     array_push($result,${"series$c"});
     $x=0;
     $c++;
     $g++;
     //p++;
    }
    


//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

else{print json_encode("NOR");}

}  
 
 
 
 
 
 
 
function SectorVSSectorDATERANGE($FROM, $TO){
	
$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', parsed_datasource.Sector AS SectorName FROM parsed_datasource WHERE parsed_datasource.Date between '$FROM' and '$TO' GROUP BY parsed_datasource.Sector");

$num_rows = mysql_num_rows($query);//get num of rows returned by query
//echo $num_rows;
//if numb of rows returned is > 0 then we have results
if($num_rows>0){

$i=1;//query index counter start at 1 because index 0 is reserved for
$QueryResult = array(); //on Index 1 which is $i will hold another array that has 2 indexes 0 and 1 where 0->Sector Name and 1->Vol Amount

while($r = mysql_fetch_array($query)) {
    $querySector = $r['SectorName'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    $QueryResult[$i] = array($querySector,$rounded);//Add QueryResult[1] ==> (array[0]=Sectorname & array[1]=vol amount)
    $i++;
}

  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 

  //dynamic part -> DONT TOUCH IT
 $oxx=1;
 while($oxx<=$num_rows){
     ${"series$oxx"}=array();
     ${"series$oxx"}['name'] = $QueryResult[$oxx][0];//GETTING SECTOR NAME
     $oxx++;
    }
    

    $graphOptions = array();
    $graphOptions['data'][] = "Sector VS Sector For Date Range<br/> From {$FROM} <br/>TO: {$TO}"; //index 1: Graph Title
    $graphOptions['data'][] = "Total Volume Per Sector For Specified Date Range"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "bar"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
   $category['data'][] = "{$FROM} <br/> {$TO}"; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)

    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
    while($c<=$num_rows){
     ${"series$c"}['data'][] =  $QueryResult[$c][1];
     array_push($result,${"series$c"});
     //echo"{$c}<br/>";
     $c++;
    }
    


//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

//ELSE IF QUERY FAILED SEND THE WORD 'NOR' AS SIGNAL THAT AN ERROR HAPPENED AND NO DATA COULD BE FETCHED FROM DB
else{print json_encode("NOR");}
}
 
 
 
 
 
 
 
function CompanyVSCompanyDATERANGE($FROM, $TO){
	
$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', meta_data.Company AS compName FROM parsed_datasource, meta_data WHERE parsed_datasource.Date between '$FROM' and '$TO' and parsed_datasource.Index = meta_data.SIndex GROUP BY parsed_datasource.Index");

$num_rows = mysql_num_rows($query);//get num of rows returned by query
//echo $num_rows;
//if numb of rows returned is > 0 then we have results
if($num_rows>0){

$i=1;//query index counter start at 1 because index 0 is reserved for
$QueryResult = array(); //on Index 1 which is $i will hold another array that has 2 indexes 0 and 1 where 0->Sector Name and 1->Vol Amount

while($r = mysql_fetch_array($query)) {
    $querySector = $r['compName'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    $QueryResult[$i] = array($querySector,$rounded);//Add QueryResult[1] ==> (array[0]=Sectorname & array[1]=vol amount)
    $i++;
}

  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 

  //dynamic part -> DONT TOUCH IT
 $oxx=1;
 while($oxx<=$num_rows){
     ${"series$oxx"}=array();
     ${"series$oxx"}['name'] = $QueryResult[$oxx][0];//GETTING SECTOR NAME
     $oxx++;
    }
    

    $graphOptions = array();
    $graphOptions['data'][] = "Company VS Company For Date Range<br/> From {$FROM} <br/>TO: {$TO}"; //index 1: Graph Title
    $graphOptions['data'][] = "Total Volume Per Company For Specified Date Range"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "column"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
   $category['data'][] = "{$FROM} UNTIL {$TO}"; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
   // $category['data'][]= $FROM;
    // $category['data'][]=$TO;
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
    while($c<=$num_rows){
     ${"series$c"}['data'][] =  $QueryResult[$c][1];
     array_push($result,${"series$c"});
     //echo"{$c}<br/>";
     $c++;
    }
    


//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

//ELSE IF QUERY FAILED SEND THE WORD 'NOR' AS SIGNAL THAT AN ERROR HAPPENED AND NO DATA COULD BE FETCHED FROM DB
else{print json_encode("NOR");}
}
 
 
 
 
 
 
function TOP_OR_BOTTOM_SECTOR_BY_YEAR($YEAR, $LIMIT, $SORT, $NOTE){
	
$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', parsed_datasource.Sector AS 'Sector' FROM parsed_datasource WHERE parsed_datasource.Date LIKE '%$YEAR%' GROUP BY Sector ORDER BY Vol $SORT LIMIT $LIMIT ");

$num_rows = mysql_num_rows($query);//get num of rows returned by query
//echo $num_rows;
//if numb of rows returned is > 0 then we have results
if($num_rows>0){

$i=1;//query index counter start at 1 because index 0 is reserved for
$QueryResult = array(); //on Index 1 which is $i will hold another array that has 2 indexes 0 and 1 where 0->Sector Name and 1->Vol Amount

while($r = mysql_fetch_array($query)) {
    $querySector = $r['Sector'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    $QueryResult[$i] = array($querySector,$rounded);//Add QueryResult[1] ==> (array[0]=Sectorname & array[1]=vol amount)
    $i++;
}

  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 

  //dynamic part -> DONT TOUCH IT
 $oxx=1;
 while($oxx<=$num_rows){
     ${"series$oxx"}=array();
     ${"series$oxx"}['name'] = $QueryResult[$oxx][0];//GETTING SECTOR NAME
     $oxx++;
    }
    

    $graphOptions = array();
    $graphOptions['data'][] = "{$NOTE} {$LIMIT} Sectors for ({$YEAR})"; //index 1: Graph Title
    $graphOptions['data'][] = "Yearly Total Volume Per Sector for {$NOTE} {$LIMIT} Sectors in {$YEAR}"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "column"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
    $category['data'][] = $YEAR; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
    while($c<=$num_rows){
     ${"series$c"}['data'][] =  $QueryResult[$c][1];
     array_push($result,${"series$c"});
     //echo"{$c}<br/>";
     $c++;
    }
    


//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

//ELSE IF QUERY FAILED SEND THE WORD 'NOR' AS SIGNAL THAT AN ERROR HAPPENED AND NO DATA COULD BE FETCHED FROM DB
else{print json_encode("NOR");}
}
 
 
 
 
function TOP_OR_BOTTOM_COMPANY_BY_YEAR($YEAR, $LIMIT, $SORT, $NOTE){
	
$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', meta_data.Company AS compName FROM parsed_datasource, meta_data WHERE parsed_datasource.Date LIKE '%$YEAR%' AND parsed_datasource.Index = meta_data.SIndex GROUP BY parsed_datasource.Index ORDER BY Vol $SORT LIMIT $LIMIT ");

$num_rows = mysql_num_rows($query);//get num of rows returned by query
//echo $num_rows;
//if numb of rows returned is > 0 then we have results
if($num_rows>0){

$i=1;//query index counter start at 1 because index 0 is reserved for
$QueryResult = array(); //on Index 1 which is $i will hold another array that has 2 indexes 0 and 1 where 0->Sector Name and 1->Vol Amount

while($r = mysql_fetch_array($query)) {
    $querySector = $r['compName'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    $QueryResult[$i] = array($querySector,$rounded);//Add QueryResult[1] ==> (array[0]=Sectorname & array[1]=vol amount)
    $i++;
}

  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 

  //dynamic part -> DONT TOUCH IT
 $oxx=1;
 while($oxx<=$num_rows){
     ${"series$oxx"}=array();
     ${"series$oxx"}['name'] = $QueryResult[$oxx][0];//GETTING SECTOR NAME
     $oxx++;
    }
    

    $graphOptions = array();
    $graphOptions['data'][] = "{$NOTE} {$LIMIT} Companies for ({$YEAR})"; //index 1: Graph Title
    $graphOptions['data'][] = "Yearly Total Volume Per Company for {$NOTE} {$LIMIT} Companies in {$YEAR}"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "column"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
    $category['data'][] = $YEAR; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
    while($c<=$num_rows){
     ${"series$c"}['data'][] =  $QueryResult[$c][1];
     array_push($result,${"series$c"});
     //echo"{$c}<br/>";
     $c++;
    }
    


//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

//ELSE IF QUERY FAILED SEND THE WORD 'NOR' AS SIGNAL THAT AN ERROR HAPPENED AND NO DATA COULD BE FETCHED FROM DB
else{print json_encode("NOR");}
}
 
 
 
 /*THIS FUNCTION WILL GENERATE DATA FOR A PIE CHART FORMAT ... AND IT'S WORKING BUT NEVER USED IN THE UI*/
 function PIECHARTVOLUME($YEAR, $LIMIT, $SORT, $NOTE){
	
$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', parsed_datasource.Sector AS 'Sector' FROM parsed_datasource WHERE parsed_datasource.Date LIKE '%$YEAR%' GROUP BY Sector ORDER BY Vol $SORT LIMIT $LIMIT ");

$num_rows = mysql_num_rows($query);//get num of rows returned by query
//echo $num_rows;
//if numb of rows returned is > 0 then we have results
if($num_rows>0){

$i=1;//query index counter start at 1 because index 0 is reserved for
$QueryResult = array(); //on Index 1 which is $i will hold another array that has 2 indexes 0 and 1 where 0->Sector Name and 1->Vol Amount

while($r = mysql_fetch_array($query)) {
    $querySector = $r['Sector'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    $QueryResult[$i] = array($querySector,$rounded);//Add QueryResult[1] ==> (array[0]=Sectorname & array[1]=vol amount)
    $i++;
}

  //Specify the Category or x-Axis Group
   $category = array();
   $category['name'] = 'x-Axis Label';//DONT CHANGE THIS 
/*
  //dynamic part -> DONT TOUCH IT
 $oxx=1;
 while($oxx<=$num_rows){
     ${"series$oxx"}=array();
     ${"series$oxx"}['name'] = $QueryResult[$oxx][0];//GETTING SECTOR NAME
     $oxx++;
    }
    */

    $graphOptions = array();
    $graphOptions['data'][] = "{$NOTE} {$LIMIT} Sectors for ({$YEAR})"; //index 1: Graph Title
    $graphOptions['data'][] = "Yearly Total Volume Per Sector for {$NOTE} {$LIMIT} Sectors in {$YEAR}"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "pie"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
    $category['data'][] = $YEAR; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part -> DONT TOUCH IT
    $c=1;
    $data = array();
    $data['name']='KHARA DIST';
    while($c<=$num_rows){
    ${"series$c"}[] =  $QueryResult[$c][0];
    ${"series$c"}[] =  $QueryResult[$c][1];
    $data['data'][] = ${"series$c"};
     
     $c++;
    }
    
array_push($result,$data);

//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);
}

//ELSE IF QUERY FAILED SEND THE WORD 'NOR' AS SIGNAL THAT AN ERROR HAPPENED AND NO DATA COULD BE FETCHED FROM DB
else{print json_encode("NOR");}
}
 

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
mysql_close($con);




?> 