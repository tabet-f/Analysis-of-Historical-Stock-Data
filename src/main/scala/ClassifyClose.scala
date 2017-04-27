
/**
  * @author Team4
  */

import org.apache.log4j.{Level, Logger}
import org.apache.spark.mllib.linalg.Vectors
import org.apache.spark.mllib.regression.LabeledPoint
import org.apache.spark.mllib.tree.DecisionTree
import org.apache.spark.rdd.RDD
import org.apache.spark.{SparkConf, SparkContext}


object ClassifyClose {

  def getError(data: RDD[LabeledPoint], labelAndPreds: RDD[(Double, Double)]): Double = {
    val error = labelAndPreds.filter(r => r._1 != r._2).count.toDouble / data.count()
    error
  }

  def getRDD(data: RDD[String]) = {
    data.map { line =>
      val parts = line.split(',')
      LabeledPoint(parts(0).toDouble, Vectors.dense(parts(1).toDouble))
    }.cache()
  }

  //Main Program Execution
  def main(args: Array[String]): Unit = {


    Logger.getLogger("org").setLevel(Level.OFF);

    val conf = new SparkConf().setAppName("csvParser").setMaster("local[*]").set("spark.sql.warehouse.dir", "file:///C:/Users/Srini/spark-warehouse")
    val sc = new SparkContext(conf)
    val SOURCE_FILE = "STOCKDATA/ClassifyData.csv";

    val csv = sc.textFile(SOURCE_FILE)

    //To find the headers
    val header = csv.first

    //To remove the header
    val data = csv.filter(_ (0) != header(0));

    //To create a RDD of (label, features) pairs
    val parsedData = getRDD(data)

    val splits = parsedData.randomSplit(Array(0.6, 0.2, 0.2))

    val (trainingData, testData, validationData) = (splits(0), splits(1), splits(2))

    //Optimal Parameters Selection
    val numClasses = 2
    val categoricalFeaturesInfo = Map[Int, Int]()
    val impurityList = "gini" :: "entropy" :: Nil
    val maxDepthList = 5 :: 10 :: 20 :: 25 :: 30 :: Nil
    val maxBinsList = 5 :: 10 :: 20 :: 25 :: 30 :: Nil
    var parametersAndScore: List[(Double, Int, String, Int, Int)] = Nil


    def getOptimalParameters: (Double, Int, String, Int, Int) = {
      for (maxDepth <- maxDepthList) {
        for (maxBins <- maxBinsList) {
          for (impurity <- impurityList) {
            val model = DecisionTree.trainClassifier(trainingData, numClasses, categoricalFeaturesInfo,
              impurity, maxDepth, maxBins)

            // Evaluate model on test instances and compute test error
            val labelAndPreds = testData.map { point =>
              val prediction = model.predict(point.features)
              (point.label, prediction)
            }

            val testErr = getError(testData, labelAndPreds)
            println("Test Error = " + (testErr, numClasses, impurity, maxDepth, maxBins))
            parametersAndScore = (testErr, numClasses,
              impurity, maxDepth, maxBins) :: parametersAndScore
          }
        }
      }
      parametersAndScore.sortBy(_._1).sortBy(_._3).head
    }

    //Considering Optimal Parameters
    val optimalParameters = Dataanalysis.timeElapsed(getOptimalParameters, "Optimal Paramters obtained")
    val model = DecisionTree.trainClassifier(trainingData, optimalParameters._2, categoricalFeaturesInfo, optimalParameters._3, optimalParameters._4, optimalParameters._5)
    // Evaluate model on test instances and compute test error
    val labelPreds = validationData.map { point =>
      val prediction = model.predict(point.features)
      (point.label, prediction)
    }

    println("Validation Error = " + getError(validationData, labelPreds))
  }

}
