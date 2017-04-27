name := "FinalProjectCSYE7200"

version := "1.0"

scalaVersion := "2.11.8"

val scalaTestVersion = "2.2.4"

resolvers += "Typesafe Repository" at "http://repo.typesafe.com/typesafe/releases/"

libraryDependencies ++= Seq(
  "joda-time" % "joda-time" % "2.9.2",
  "org.scala-lang.modules" %% "scala-xml" % "1.0.2",
  "org.scala-lang.modules" %% "scala-parser-combinators" % "1.0.4",
  "org.scalatest" %% "scalatest" % scalaTestVersion % "test",
  "org.ccil.cowan.tagsoup" % "tagsoup" % "1.2.1" )

val sprayGroup = "io.spray"
val sprayJsonVersion = "1.3.2"
libraryDependencies ++= List("spray-json") map {c => sprayGroup %% c % sprayJsonVersion}

libraryDependencies ++= Seq("org.apache.spark" % "spark-core_2.11" % "2.1.0",
  "org.apache.spark" % "spark-mllib_2.11" % "2.0.0")

// https://mvnrepository.com/artifact/org.apache.spark/spark-core_2.11
libraryDependencies += "org.apache.spark" % "spark-core_2.11" % "2.1.0"

// https://mvnrepository.com/artifact/org.apache.spark/spark-sql_2.11
libraryDependencies += "org.apache.spark" % "spark-sql_2.11" % "2.1.0"

//https://github.com/tototoshi/scala-csv (used to create the new CSV file)
libraryDependencies += "com.github.tototoshi" %% "scala-csv" % "1.3.4"

// https://mvnrepository.com/artifact/mysql/mysql-connector-java
libraryDependencies += "mysql" % "mysql-connector-java" % "5.1.16"

// https://mvnrepository.com/artifact/com.typesafe.akka/akka-actor_2.11
libraryDependencies += "com.typesafe.akka" % "akka-actor_2.11" % "2.5.0"