<?php
//THIS CODE IS USED TO QUERY THE SQL DB AND SEND DATA IN JSON FORMAT TO THE FRONTEND*/

$con = mysql_connect("localhost", "hinemel_vandy17", "PASSWORD GOES HERE CAN'T BE SHOWN);
 
if (!$con) {
  die('Could not connect: ' . mysql_error());
}
 
mysql_select_db("hinemel_ScalaSpark", $con);


$PERIOD_ENCODED = $_GET['p']; //y=2011 ex ONE SINGLE YEAR (EX: 2011)
$PERIOD =  base64_decode($PERIOD_ENCODED);
$SECTORS_STRING_ENCODED = $_GET['selectedSectors']; //c= COMPANY ||  s=SECTOR 
$SECTORS_STRING = base64_decode($SECTORS_STRING_ENCODED);
$COMPANIES_STRING_ENCODED = $_GET['selectedCompanies']; //from date
$COMPANIES_STRING = base64_decode($COMPANIES_STRING_ENCODED);
$UNIT_ENCODED = $_GET['u']; //y=2011 ex ONE SINGLE YEAR (EX: 2011)
$UNIT = base64_decode($UNIT_ENCODED);

//$passedURL= $SECTORS_STRING.$COMPANIES_STRING.$PERIOD.$UNIT;
//echo $passedURL;

if (!empty($PERIOD) && !empty($SECTORS_STRING) && !empty($COMPANIES_STRING) && !empty($UNIT) ) {


if($UNIT==="volume"){

SectorVSCompanySUMVOLUME_YEARSRANGE($SECTORS_STRING, $COMPANIES_STRING, $PERIOD);
}


if($UNIT==="percgain"){
SectorVSCompanyAVERAGPERCGAIN_YEARSRANGE($SECTORS_STRING, $COMPANIES_STRING, $PERIOD);

}


}
else{
print json_encode("NOR");
}

 

//Get AVG % Gain for sectors and indexes -> companies
function SectorVSCompanyAVERAGPERCGAIN_YEARSRANGE($SECTORS_STRING, $COMPANIES_STRING, $PERIOD){




$startyear=2007;//start year
$yearsperiod=$PERIOD;
$untilyear = $startyear + $yearsperiod;

$SectorNamesArray= explode(',', $SECTORS_STRING);



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
	
	$query = mysql_query("SELECT AVG(parsed_datasource.Perc_Gain) AS 'perGain', Date FROM parsed_datasource WHERE Sector = '$iterationSectorName' and Date Like '%$startyear%' GROUP BY Sector");

	//echo"INNER call: getting vol for {$startyear} & Sector: {$iterationSectorName}<br/>";
	
	//getting query result
	while($r = mysql_fetch_array($query)) {
    //$querySector = $r['Sector'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    $foo = $r['perGain'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $pg = number_format((float)$foo, 2, '.', ''); 
    $queryVol = $pg;
    ${"DataResult$outer"}[$Inner] = array($startyear,$queryVol);//Add QueryResult[1] ==> (array[0]=YEAR & array[1]=vol amount)
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

  //dynamic part ->  IT
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
    $graphOptions['data'][] = "Sector(s) VS Company(s) Average Percentage Gain for ({$PERIOD} years)"; //index 1: Graph Title
    $graphOptions['data'][] = "From: {$exactSTART}  To: {$exactEND}"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "AVG % Gain"; //index 3: Y-AXIS
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
    
    //dynamic part ->  IT
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



}

$startyear=2007;//start year
$yearsperiod=$PERIOD;
$untilyear = $startyear + $yearsperiod;


$SectorNamesArray = explode(',', $COMPANIES_STRING);

$compNames = array();

//STEP1: GET DATE->VOL OVER THE PERIOD (2012 TO 2015) FOR EACH SECTOR IN AN INDEPENDENT ARRAY CALLED DATARESULT 
$SectorArraySize = sizeof($SectorNamesArray);//get sector array SIZE
if($PERIOD>0){
$outer=0;//Outer Loop Iteration Counter
$Inner=0;//Query Fetch Loop Array Index Counter

while($outer<$SectorArraySize){ //this iteration will go untill all sectors index has been looped through
	$iterationSectorName = $SectorNamesArray[$outer];
	
	$startyear=2007;//reset Start Year Again for Next Sector
	
	${"DataResultX$outer"}=array(); //Dynamic 2D Array Creation For Each Sector to hold year->vol 
	
	
	//echo"OUTER call#{$outer} for $iterationSectorName<br/>";
	//getting data for each of these sector index but for 10 years (so 1 to many)

    while($startyear<$untilyear){//generating query for each year where startyear is the loop counter and incremented by 1 year on each iteration
	
	$query = mysql_query("SELECT AVG(parsed_datasource.Perc_Gain) AS 'perGain', parsed_datasource.Date, meta_data.Company as CompName FROM parsed_datasource, meta_data WHERE parsed_datasource.Index = '$iterationSectorName' and Date Like '%$startyear%' and parsed_datasource.Index = meta_data.SIndex GROUP BY parsed_datasource.Index");

	//echo"INNER call: getting vol for {$startyear} & Sector: {$iterationSectorName}<br/>";
	
	//getting query result
	while($r = mysql_fetch_array($query)) {
    $querySector = $r['CompName'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
    
     if (!in_array($querySector, $compNames)) { $compNames[] = $querySector;}
     $foo = $r['perGain'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $pg = number_format((float)$foo, 2, '.', ''); 
    $queryVol = $pg;
    ${"DataResultX$outer"}[$Inner] = array($startyear,$queryVol);//Add QueryResult[1] ==> (array[0]=YEAR & array[1]=vol amount)
	$Inner++;
}//end of fetch loop

     $startyear++;//Increment to next year
	
}//end of inner year loop	
	
 $outer++;//Get next Sector from sector array

}//end of sector outer loop 




$SectorArraySize=sizeof($compNames);

$c=0;

while($c<$SectorArraySize){

$arName = ${"DataResultX$c"};
$sizeofArray = sizeof($arName);
$i=0;
${"getOnlyVolfromdataArrayX$c"} = array();
foreach ( $arName as $var ) {
   
    ${"getOnlyVolfromdataArrayX$c"}[] = $var[1];
    
  
    //array_push(${"getOnlyVolfromdataArray$c"},$var[1]); //$var[1] is the volume 
}

$c++;
}





  
 $oxx=0;
 $sizeofArraySector = sizeof($SectorNamesArray);
   $f2=$f;
 while($oxx<$sizeofArraySector){
     ${"series$f"}=array();
     ${"series$f"}['name'] = $compNames[$oxx];
     $f++;
     $oxx++;
    }


    $x=0;
    $g=0;
    
    while($g<$SectorArraySize){
   
    $sizeofVolArray = sizeof (${"getOnlyVolfromdataArrayX$g"});
    
    	while($x<$sizeofVolArray){
    	${"series$f2"}['data'][] =  ${"getOnlyVolfromdataArrayX$g"}[$x];
    	$x++;
    	}
     array_push($result,${"series$f2"});
     $x=0;
     $f2++;
     $g++;
     //p++;
    }




print json_encode($result, JSON_NUMERIC_CHECK);



}

else{print json_encode("NOR");}
}









//Get Sum Volume for sectors and indexes -> companies
function SectorVSCompanySUMVOLUME_YEARSRANGE($SECTORS_STRING, $COMPANIES_STRING, $PERIOD){




$startyear=2007;//start year
$yearsperiod=$PERIOD;
$untilyear = $startyear + $yearsperiod;

$SectorNamesArray= explode(',', $SECTORS_STRING);



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
	
	$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', Date FROM parsed_datasource WHERE Sector = '$iterationSectorName' and Date Like '%$startyear%' GROUP BY Sector");

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

  //dynamic part ->  IT
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
    $graphOptions['data'][] = "Sector(s) VS Company(s) Per Volume Traded for ({$PERIOD} years)"; //index 1: Graph Title
    $graphOptions['data'][] = "From: {$exactSTART}  To: {$exactEND}"; //index 2: Graph Subtitle
    $graphOptions['data'][] = "Volume"; //index 3: Y-AXIS
    $graphOptions['data'][] = "column"; //index 4: Graph Type (col chart, bar chart, pie chart...)

   
    
     $sizeofYearArray= sizeof($yearsArray);
    $q=0;
    while($q<$sizeofYearArray){
     $category['data'][] = $yearsArray[$q]; //index 0: X-AXIS LABEL (THIS WORKS FOR SINGLE X-AXIS LABEL)
     $q++;
    }
    
   
    $result = array();
    array_push($result,$category);
    array_push($result,$graphOptions);
    
    //dynamic part ->  IT
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



}

$startyear=2007;//start year
$yearsperiod=$PERIOD;
$untilyear = $startyear + $yearsperiod;


$SectorNamesArray = explode(',', $COMPANIES_STRING);


//STEP1: GET DATE->VOL OVER THE PERIOD (2012 TO 2015) FOR EACH SECTOR IN AN INDEPENDENT ARRAY CALLED DATARESULT 
$SectorArraySize = sizeof($SectorNamesArray);//get sector array SIZE
if($PERIOD>0){
$outer=0;//Outer Loop Iteration Counter
$Inner=0;//Query Fetch Loop Array Index Counter

$compNames = array();

while($outer<$SectorArraySize){ //this iteration will go untill all sectors index has been looped through
	$iterationSectorName = $SectorNamesArray[$outer];
	
	$startyear=2007;//reset Start Year Again for Next Sector
	
	${"DataResultX$outer"}=array(); //Dynamic 2D Array Creation For Each Sector to hold year->vol 
	
	
	//echo"OUTER call#{$outer} for $iterationSectorName<br/>";
	//getting data for each of these sector index but for 10 years (so 1 to many)

    while($startyear<$untilyear){//generating query for each year where startyear is the loop counter and incremented by 1 year on each iteration
	
	$query = mysql_query("SELECT SUM(parsed_datasource.Volume) AS 'Vol', parsed_datasource.Date, meta_data.Company as CompName FROM parsed_datasource, meta_data WHERE parsed_datasource.Index = '$iterationSectorName' and Date Like '%$startyear%' and meta_data.SIndex = parsed_datasource.Index GROUP BY parsed_datasource.Index");

	//echo"INNER call: getting vol for {$startyear} & Sector: {$iterationSectorName}<br/>";
	
	//getting query result
	while($r = mysql_fetch_array($query)) {
     $querySector = $r['CompName'];//Get Sector Name from query for 1st Iteration which is group by sector so from A to Z
     if (!in_array($querySector, $compNames)) { $compNames[] = $querySector;}
     $queryVol = $r['Vol'];//Get Vol Sum from query for 1st Iteration which is for the above sector
    $rounded=round($queryVol);
    ${"DataResultX$outer"}[$Inner] = array($startyear,$rounded);//Add QueryResult[1] ==> (array[0]=YEAR & array[1]=vol amount)
	$Inner++;
}//end of fetch loop

     $startyear++;//Increment to next year
	
}//end of inner year loop	
	
 $outer++;//Get next Sector from sector array

}//end of sector outer loop 




$SectorArraySize=sizeof($compNames);

$c=0;

while($c<$SectorArraySize){

$arName = ${"DataResultX$c"};
$sizeofArray = sizeof($arName);
$i=0;
${"getOnlyVolfromdataArrayX$c"} = array();
foreach ( $arName as $var ) {
   
    ${"getOnlyVolfromdataArrayX$c"}[] = $var[1];
    
  
    //array_push(${"getOnlyVolfromdataArray$c"},$var[1]); //$var[1] is the volume 
}

$c++;
}





  //dynamic part ->  IT
 $oxx=0;
 $sizeofArraySector = $SectorArraySize;
   $f2=$f;
 while($oxx<$sizeofArraySector){
     ${"series$f"}=array();
     ${"series$f"}['name'] = $compNames[$oxx];
     $f++;
     $oxx++;
    }

 //dynamic part ->  IT
  
 
    $x=0;
    $g=0;
    
    while($g<$SectorArraySize){
   
    $sizeofVolArray = sizeof (${"getOnlyVolfromdataArrayX$g"});
    
    	while($x<$sizeofVolArray){
    	${"series$f2"}['data'][] =  ${"getOnlyVolfromdataArrayX$g"}[$x];
    	$x++;
    	}
     array_push($result,${"series$f2"});
     $x=0;
     $f2++;
     $g++;
     //p++;
    }



//DONT PUT ANY PRINT OR ECHO BEFORE JSON_ENCODE
print json_encode($result, JSON_NUMERIC_CHECK);



}

else{print json_encode("NOR");}
}






?>