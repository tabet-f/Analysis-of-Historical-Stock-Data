<?php

/*THIS CODE IS USED TO CHECK & SYNCHRONIZE SCALA & WEB UI STATUS */

$Status = $_GET['stat'];


if(!empty($Status) && $Status==="CHECK") {
checkifScalaisRunning();

}

if(!empty($Status) && $Status==="DONE") {

while (notifyThatScalaisDone()!=TRUE) {
   notifyThatScalaisDone(); 
   sleep(2);
}


}


    function checkifScalaisRunning(){
       $con = mysqli_connect("localhost", "hinemel_vandy17", "PASSWORD GOES HERE CAN'T BE SHOWN", "hinemel_ScalaSpark");
 
if (!$con) {
  die('Could not connect: ' . mysqli_connect_error());
}
 
       $q = "SELECT web_status, spark_status FROM app_status";
       
       $query = mysqli_query($con, $q);
       
 
       
       /* numeric array */
       $row = $query->fetch_array(MYSQLI_NUM);
     
      
       $web = $row[0];
       $app = $row[1];
       
       if($app==="START" && $web!="START"){
       
       notifyScala();
       
       if($app==="START" && notifyScala()===TRUE){
      
       print json_encode("ON&TRUE");
     
       }
       else if($app==="START" && notifyScala()===FALSE){
        print json_encode("ON&FALSE");
       }
       
       }
       
       if($app==="START" && $web==="START"){
       
       print json_encode("ALREADYON");
       }
       
       else if($app!="START"){
        print json_encode("OFF");
       }
       
      
     
      
     }

     
    

 function notifyScala(){
       
         $con = mysqli_connect("localhost", "hinemel_vandy17", "PASSWORD GOES HERE CAN'T BE SHOWN", "hinemel_ScalaSpark");
 
if (!$con) {
  die('Could not connect: ' . mysqli_connect_error());
}
      
     
        $qss = "UPDATE app_status SET web_status='START' WHERE rowid=1";
       $rq = mysqli_query($con, $qss);

       if($rq===TRUE){
      
       return true;
       }
       
       else{
       return false;
       
       }
      
       }
      
       
     function notifyThatScalaisDone(){
      $con = mysqli_connect("localhost", "hinemel_vandy17", "PASSWORD GOES HERE CAN'T BE SHOWN", "hinemel_ScalaSpark");
 
if (!$con) {
  die('Could not connect: ' . mysqli_connect_error());
}
      
     
        $qa = "SELECT  web_status, spark_status, isApplicationfinished FROM app_status";
       
       $query = mysqli_query($con, $qa);
       
 
       
       /* numeric array */
       $row = $query->fetch_array(MYSQLI_NUM);
     
      
       $web = $row[0];
       $app = $row[1];
       $isDone = $row[2];
       
       if($isDone==="DONE" && $web==="START" && $app==="START"){
         print json_encode("SCALADONE");
         return true;
       }
     
     
     }
   
    
   
       
      
     
      
   ?>  