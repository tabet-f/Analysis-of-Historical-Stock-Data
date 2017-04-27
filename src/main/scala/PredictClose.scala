

/**
  *  @author Team4
  */

import org.apache.log4j.{Level, Logger}
import org.apache.spark.mllib.regression.LabeledPoint
import org.apache.spark.mllib.tree.DecisionTree
import org.apache.spark.rdd.RDD
import org.apache.spark.{SparkConf, SparkContext}

object PredictClose {

  def getRmse(data: RDD[LabeledPoint], labelAndPreds: RDD[(Double, Double)]): Double ={
    val testCount = data.count()
    val testLoss = labelAndPreds.map { case (v, p) => math.pow(v - p, 2) }.reduce(_ + _)
    val predRmse = math.sqrt(testLoss / testCount)
    predRmse
  }

  //Main Program Execution
  def main(args: Array[String]) {

    Logger.getLogger("org").setLevel(Level.OFF)

    val SOURCE_FILE = "STOCKDATA/PredictData.csv";
    val conf = new SparkConf().setAppName("csvParser").setMaster("local[*]").set("spark.sql.warehouse.dir", "file:///C:/Users/Srini/spark-warehouse")
    val sc = new SparkContext(conf)

    val csv = sc.textFile(SOURCE_FILE)

    //To find the headers
    val header = csv.first;

    //To remove the header
    val data = csv.filter(_ (0) != header(0))

    //To create a RDD of (label, features) pairs
    val parsedData = ClassifyClose.getRDD(data)


    val splits = parsedData.randomSplit(Array(0.6, 0.2, 0.2))

    val (trainingData, testData, validationData) = (splits(0), splits(1), splits(2))
    //Optimal Parameters Selection
    val categoricalFeaturesInfo = Map[Int, Int]()
    val impurityList = "variance" :: Nil
    val maxDepthList = 5 :: 10 :: 20 :: 25 :: 30 :: Nil
    val maxBinsList = 5 :: 10 :: 20 :: 25 :: 30 :: Nil
    var parametersAndScore: List[(Double, String, Int, Int)] = Nil

    def getOptimalParameters: (Double, String, Int, Int) = {
      for (maxDepth <- maxDepthList) {
        for (maxBins <- maxBinsList) {
          for (impurity <- impurityList) {
            val model = DecisionTree.trainRegressor(trainingData, categoricalFeaturesInfo, impurity,
              maxDepth, maxBins)

            // Evaluate model on test instances and compute test error
            val labelAndPreds = testData.map { point =>
              val prediction = model.predict(point.features)
              (point.label, prediction)
            }
            val testCount = testData.count()
            val testLoss = labelAndPreds.map { case (v, p) => math.pow(v - p, 2) }.reduce(_ + _)
            val testRmse = math.sqrt(testLoss / testCount)
            println("Test Error = " + (testRmse, impurity, maxDepth, maxBins))
            parametersAndScore = (testRmse,
              impurity, maxDepth, maxBins) :: parametersAndScore
          }
        }
      }
      parametersAndScore.sortBy(_._1).sortBy(_._3).head
    }

    val optimalParameters = Dataanalysis.timeElapsed(getOptimalParameters, "Optimal Paramters obtained")

    val model = DecisionTree.trainRegressor(trainingData, categoricalFeaturesInfo, optimalParameters._2, optimalParameters._3, optimalParameters._4)

    val labelAndPreds = testData.map { point =>
      val prediction = model.predict(point.features)
      (point.label, prediction)
    }

    val labelPreds = validationData.map { point =>
      val prediction = model.predict(point.features)
      (point.label, prediction)
    }

    println("Test Mean Squared Error = " + getRmse(testData, labelAndPreds))

    println("Validation Mean Squared Error = " + getRmse(validationData, labelPreds))

  }
}

