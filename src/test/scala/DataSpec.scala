import java.io.File
import java.sql.Connection

import org.apache.spark.{SparkConf, SparkContext}
import org.apache.spark.sql.{Row, SparkSession}
import org.apache.spark.sql.types._
import org.scalatest.{FlatSpec, Matchers, _}

/**
  * @author Team4
  */

class DataSpec extends FlatSpec with Matchers with TryValues with Inside with BeforeAndAfterEach with BeforeAndAfterAll {

  "timeElapsed" should
    "return the time taken to evaluate a block in millis and the block's result" in {
    val result = Dataanalysis.timeElapsed((1 to 10).reduceLeft[Int](_ + _), "Time Test")
    //time.toInt should be <= 1000  // it should take less than 1 second!
    result shouldBe 55
  }

  "getTime" should
    "return time in string whose length should be 8" in {
    val time = Dataanalysis.getTime()
    time.length shouldBe 8
  }


  "deleteFile" should
    "delete file if existing" in {
    new File("deleteTest.csv")
    val ret = Dataanalysis.deleteFile("deleteTest.csv")
    ret should not be false
  }

  "loadFileToDF" should
    "load the file to a dataframe successfully" in {
    val sparkSession = SparkSession.builder
      .master("local") // same as local[*]
      .appName("Testing")
      .getOrCreate()
    val file = "DFTest.csv"
    val create1 = "a,a"
    val create2 = "b,b"
    val schema = new StructType()
      .add("Test1", StringType, false, "Test1")
      .add("Test2", StringType, false, "Test2")
    val df = Dataanalysis.loadFileToDF(sparkSession, file, schema: StructType)
    val result = df.select("*").collect()
    result === Array(("a","a"),("b","b")).map(Row.fromTuple)
  }

  "getConnection" should "not be null" in {
    var connection: Connection = null
    Dataanalysis.getConnection()
    connection should not be null
  }

  "getRDD" should "not be null" in {
    val conf = new SparkConf().setAppName("csvParser").setMaster("local[*]").set("spark.sql.warehouse.dir", "file:///C:/Users/Srini/spark-warehouse")
    val sc = new SparkContext(conf)
    val SOURCE_FILE = "ClassifyData.csv";
    val csv = sc.textFile(SOURCE_FILE)
    val parsedData = ClassifyClose.getRDD(csv)
    parsedData should not be null
  }
}

