

/**
  * @author Team4
  */


import java.io.File
import java.sql.{Connection, DriverManager}
import java.text.SimpleDateFormat
import java.util.Calendar
import com.github.tototoshi.csv._
import org.apache.log4j.{Level, Logger}
import org.apache.spark.sql._
import org.apache.spark.sql.types._


object Dataanalysis {


  // db connection info
  val driver = "com.mysql.jdbc.Driver"
  val url = "jdbc:mysql://192.xxx.xxx.xxx:3306/hinemel_ScalaSpark"
  val username = "xxx"
  val password = "xxx"
  var connection: Connection = null

  //Connection Singeltion
  def getConnection() : Connection = {
    if(connection==null) {
      // make the connection

      Class.forName(driver)
      connection = DriverManager.getConnection(url, username, password)
      connection
    }
    else{

      connection
    }
  }





  /* getTime():  @return -> String : current system time in HH:mm:ss format*/
  def getTime(): String = {

    val today = Calendar.getInstance.getTime
    val curTimeFormat = new SimpleDateFormat("HH:mm:ss")
    val currentHMS = curTimeFormat.format(today)
    currentHMS

  }

  //function for writing logging and calculating time needed for executing each code block
  def timeElapsed[R](block: => R, message: String): R = {

    val t0 = System.currentTimeMillis()
    val timeatFirstCall = getTime()
    val result = block
    // call-by-name
    val t1 = System.currentTimeMillis()
    println(timeatFirstCall + " @ " + message + " -> Elapsed Time: " + (t1 - t0) + " ms.")
    result

  }

  //used to Delete File @param filename as String
  def deleteFile(filename: String) = {
    try {
      new File(filename).delete()
    }catch{
      case e => println("Error deleting file: "+ e.printStackTrace())
    }

  }

  /* writeLog():  @param-> String : a custom log message*/
  def writeLog(message: String) = {

    val time = getTime()
    val result = time + " @ " + message
    println(result)

  }

  def loadFileToDF(sparkSession: SparkSession, file: String, schema: StructType) = {

    sparkSession.read
      .format("csv") //as of spark 2.0 csv is supported as a format type like parquet, json, orc or jdbc...
      .option("header", true)
      .option("inferSchema", true)
      .schema(schema)
      .load(file)

  }

  def DeletingAllRowsInDB(driver: String, url: String, username: String, password:String ) = {


    var connection: Connection = null

    try {
      // make the connection
      Class.forName(driver)
      connection = getConnection()
      //val path = "FilteredData.csv"
      // create the statement, and run the select query
      val statement = connection.createStatement()
      //Clear The Table at the first run
      statement.executeQuery("TRUNCATE parsed_datasource")

    } catch {
      case e => println("Error deleting all rows in DB: "+ e.printStackTrace)
    }

  }

  //Clear The DB Table each time we run the application to avoid duplicate entries
  def LoadCSVtoDB(file: String) = {

    try {
      // make the connection

      connection = getConnection()

      // create the statement, and run the select query
      val statement = connection.createStatement()
      //Load CSV to DB
      statement.executeQuery(" LOAD DATA LOCAL INFILE '" + file +
        "' INTO TABLE parsed_datasource " +
        " FIELDS TERMINATED BY \',\' ENCLOSED BY \'\"'" +
        " LINES TERMINATED BY \'\\n\'" +
        " IGNORE 1 ROWS")

    } catch {
      case e => println("Error loading CSV to DB: "+ e.printStackTrace)
    }



  }

  //check if start is pressed on the web ui
  def appStartStop(token: String): Boolean = {

    var status = false
    try {
      // make the connection

      connection =  getConnection()

      // create the statement, and run the select query
      val statement = connection.createStatement()
      //Load CSV to DB
      val rs = statement.executeQuery("SELECT web_status from app_status ")

      while (rs.next()) {
        if(rs.getString("web_status").equals(token))
          status=true
        else
          status=false
      }
    } catch {
      case e => println("Error Status Fetching From DB: "+ e.printStackTrace)
    }


    status
  }

  //check if start is pressed on the web ui
  def notifyWeb(token: String) = {

    if(token=="START"){

      try {
        // make the connection
        connection =  getConnection()

        // create the statement, and run the select query
        val statement = connection.createStatement()
        //Load CSV to DB
        statement.executeQuery("TRUNCATE app_status ")
        statement.executeUpdate("ALTER TABLE app_status AUTO_INCREMENT = 1")
        statement.executeUpdate("INSERT INTO app_status (web_status, spark_status) VALUES ('NONE ', 'START') ")

      } catch {
        case e => println("Error Status Fetching From DB: "+ e.printStackTrace)
      }
    }

    if(token=="DONE") {

      try {
        // make the connection
        connection = getConnection()

        // create the statement, and run the select query
        val statement = connection.createStatement()
        //Load CSV to DB

        statement.executeUpdate("UPDATE app_status SET isApplicationfinished='DONE' WHERE rowid=1")

      } catch {
        case e => println("Error Updating Status in DB: " + e.printStackTrace)
      }
    }


  }




  //Main Program Execution
  def main(args: Array[String]) {


    //Metadata file
    val METADATA_CSV_PATH = "STOCKDATA/META/Metadata.csv"
    //Original DataSource
    val DATASOURCE_CSV_PATH = "STOCKDATA/SOURCE/Sourcedata.csv"
    //File Name created after filtering (the one that must be used by the application)
    val FILTEREDATA_CSV_FILENAME = "FilteredData.csv"




    notifyWeb("START")

    //a Scala Akka Actor to keep check the database app_status table to know if the application must
    // start or no
    import scala.concurrent.duration._
    import system.dispatcher
    val system = akka.actor.ActorSystem("system")

    while(appStartStop("START")==false) {
      system.scheduler.schedule(0 seconds, 1 seconds)(appStartStop("START"))
    }

    timeElapsed(println(""),"Application Started")


    timeElapsed(Logger.getLogger("org").setLevel(Level.OFF), "Disable Spark Logging: ON") //Disable Logging for better visibility in the console


    //Deleting all rows in db before start parsing csv file to avoid duplicate entries
    timeElapsed(DeletingAllRowsInDB(driver, url, username, password), "Synchronizing Datasource")
    //runing sparkSession  where all the code exists
    //as of spark 2.0 SparkContext & SparkSQL is used through SparkSession
    val sparkSession = timeElapsed(SparkSession.builder
      .master("local") // same as local[*]
      .appName("Spark for Stock Data Analysis")
      .getOrCreate(), "Establishing Spark Session")


    val reader: DataFrameReader = timeElapsed(sparkSession.read, "Creating Spark DataFrame Reader")


    //SOURCEDATA CSV File Schema & Types => Structured Schema
    val schemaTypedSourceData = new StructType()
      .add("Index", StringType, false, "Company Stock Index Official Name")
      .add("Date", StringType, false, "Date of The Quote")
      .add("Open", DoubleType, false, "Open Rate Value")
      .add("High", DoubleType, false, "High Rate Value")
      .add("Low", DoubleType, false, "Low Rate Value")
      .add("Close", DoubleType, false, "Close Rate Value")
      .add("Volume", LongType, false, "Traded Volume Value")
      .add("Adj Close", FloatType, false, "Adjusted Closing Price")




    //Parsing Source csv to Dataframe
    val df = loadFileToDF(sparkSession, DATASOURCE_CSV_PATH, schemaTypedSourceData)


    val datasrc = timeElapsed({
      df.select("*").orderBy("Index").collect()
    }, "Populating Dataframes for 'DATASOURCE FILE' Ended")
    val datasrctoCase = datasrc.map { case Row(a: String, b: String, c: Double, d: Double, e: Double, f: Double, g: Long, h: Float) => (a, b, c, d, e, f, g, h) }

    // df.select("Date").first()

    //META DATA CSV File Schema & Types => Structured Schema
    val schemaTypedMetaData = new StructType()
      .add("Sector", StringType, false, "Stock Sector Name")
      .add("Index", StringType, false, "Stock Index Name")


    //  writeLog("Creating Dataframes Started: "+metaDataCSV.toString)
    val df2 = loadFileToDF(sparkSession, METADATA_CSV_PATH, schemaTypedMetaData)
    val sectsrc = timeElapsed({
      df2.select("Sector", "Index").orderBy("Sector").collect()
    }, "Populating Dataframes for 'METADATA FILE' Ended")
    val sectindxtoCase = sectsrc.map { case Row(a: String, b: String) => (a, b) }


    //startDelete("directory", "myg")


    //Check if File Exists
    if (new java.io.File(FILTEREDATA_CSV_FILENAME).exists == true) {
      //if already file exists:

      //step1: Delete this file
      timeElapsed(deleteFile(FILTEREDATA_CSV_FILENAME), FILTEREDATA_CSV_FILENAME + " Checking File System for Existing File with Same Name")
      //step2: Create new file
      new File(FILTEREDATA_CSV_FILENAME)
    }

    //if file is not found:
    else {
      //create file
      new File(FILTEREDATA_CSV_FILENAME)

    }

    // writing the filtered file that must be used by the app for further analysis
    import scala.collection.mutable.ListBuffer
    var fullOutput = new ListBuffer[String]()

    fullOutput += "" //used as Primary key later on
    fullOutput += "Sector"
    fullOutput += "Index"
    fullOutput += "Date"
    fullOutput += "Open"
    fullOutput += "High"
    fullOutput += "Low"
    fullOutput += "Close"
    fullOutput += "Volume"
    fullOutput += "Gain"
    fullOutput += "Perc_Gain"
    fullOutput += "Previous Close"


    val writer = CSVWriter.open(FILTEREDATA_CSV_FILENAME, append = true)
    //part that writes the col headers (executed only once)
    writer.writeRow(fullOutput) //to write column headers
    fullOutput.clear()

    var i = 1 //previous day counter
    val size = datasrctoCase.size //map size

    //creating the "filteredData.csv" file
    //datasourceCSV loop
    timeElapsed(for ((ind, d, o, h, l, c, v, a) <- datasrctoCase) {
      //metadataCSV loop

      for ((sector, index) <- sectindxtoCase) {

        //if index of metadataCSV MATCH index of sourcedataCSV
        if (ind.equalsIgnoreCase(index)) {

          fullOutput += "" //PK
          fullOutput += sector //sector value
          fullOutput += ind //index value


          var simpleDateFormat: SimpleDateFormat = new SimpleDateFormat("mm/dd/yyyy")
          var dateCh: java.util.Date = simpleDateFormat.parse(d.toString)
          val nd = new SimpleDateFormat("yyyy-mm-dd").format(dateCh)


          fullOutput += nd //date value
          fullOutput += o.toString //open value
          fullOutput += h.toString //high value
          fullOutput += l.toString //low value
          fullOutput += c.toString //close value
          fullOutput += v.toString //volume
          //[REMOVED] fullOutput += a.toString //adj Close [REMOVED]

          val gain = c - o; //close-open
          fullOutput += gain.toString //Gain
          fullOutput += ((gain / o) * 100).toString //%Gain


          //get previous day close
          if(i<size) {
            val splitted = datasrctoCase(i).toString().split(',')
            fullOutput += splitted(5)
          }
          //last day -> no previous day put 0
          else{ fullOutput+="0"}
          //increment previous day counter
          i=i+1

          writer.writeRow(fullOutput)

          //clear the outputlist for next row
          fullOutput.clear()


          //println("ROW ADDED: " + sector + " -> " + ind + " : " +d)
        }
      }

    }, FILTEREDATA_CSV_FILENAME + " File Generated & Filtered Successfully")

    //Close File Writer
    writer.close()


    //Now after having the final data --> load it to DB
    timeElapsed(print(""), "Synchronization Started")
    timeElapsed(LoadCSVtoDB(FILTEREDATA_CSV_FILENAME), "Synchronization Ended")
    timeElapsed(notifyWeb("DONE"), "Updating Status")
    timeElapsed(print(""), "Program Execution Ended")

  }
}
