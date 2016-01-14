<?php

if (!class_exists('bigml')) {
  include '../bigml/bigml.php';
} 

if (!class_exists('cluster')) {
   include '../bigml/cluster.php';
}  

if (!class_exists('anomaly')) {
   include '../bigml/anomaly.php';
}

if (!class_exists('model')) {
   include '../bigml/model.php';
}

if (!class_exists('LogisticRegression')) {
  include  '../bigml/logistic.php';
}

class BigMLTestComparePredictions extends PHPUnit_Framework_TestCase
{
    protected static $username; # "you_username"
    protected static $api_key; # "your_api_key"

    protected static $api;

    public static function setUpBeforeClass() {
       self::$api =  new BigML(self::$username, self::$api_key, false);
       ini_set('memory_limit', '512M');
       ini_set('xdebug.max_nesting_level', '300');
    }
    #
    # Successfully comparing predictions
    # 
   /* public function test_scenario1() {

        $data = array(array("filename" => "data/iris.csv", 
	 		    "data_input" => array("petal width"=> 0.5),
			    "objective"=> '000004',
			    "prediction" => "Iris-setosa"),
		      array("filename" => 'data/iris.csv',
		            "data_input" => array("petal length" => 6, "petal width"=> 2),
			    "objective" => '000004',
			    "prediction" => 'Iris-virginica'),
                      array("filename" => 'data/iris.csv',
                            "data_input" => array('petal length' => 4, '"petal width' => 1.5),
                            "objective" => '000004',
                            "prediction" => 'Iris-versicolor'),
                      array("filename" => 'data/iris_sp_chars.csv',
                            "data_input" => array('pétal.length' => 4, '"pétal&width' => 1.5),
                            "objective" => '000004',
                            "prediction" => 'Iris-versicolor'));

        foreach($data as $item) {
            print "\nSuccessfully comparing predictions:\n";

            print "Given I create a data source uploading a ". $item["filename"]. " file\n";
            $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
            $this->assertEquals(1, $source->object->status->code);

            print "And I wait until the source is ready\n";
            $resource = self::$api->_check_resource($source->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create dataset with local source\n";
            $dataset = self::$api->create_dataset($source->resource);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
            $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

            print "And I wait until the dataset is ready\n";
            $resource = self::$api->_check_resource($dataset->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create model\n";
            $model = self::$api->create_model($dataset->resource);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $model->code);

            print "And I wait until the model is ready\n";
            $resource = self::$api->_check_resource($model->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create a local model " . $model->resource . "\n";
            $localmodel = new Model($model->resource, self::$api);

            print "When I create a prediction for " . json_encode($item["data_input"]) . "\n";
	    $prediction = self::$api->create_prediction($model, $item["data_input"]);
	    $this->assertEquals(BigMLRequest::HTTP_CREATED, $prediction->code);

            print "Then the prediction for " . $item["objective"] . " is " . $item["prediction"]; 
            $this->assertEquals($item["prediction"], $prediction->object->prediction->{$item["objective"]});

            print "And I create a local prediction for " . json_encode($item["data_input"]) . "\n";
            $prediction = $localmodel->predict($item["data_input"]);

	    print "Then the local prediction is " . $item["prediction"] . "\n";
            $this->assertEquals($prediction->output, $item["prediction"]);

        }
    }
    #
    # Successfully comparing predictions
    # 
    public function test_scenario2() {
       $data = array(array("filename" => "data/spam.csv",
                           "options" => array("fields" => array("000001" => array("optype" => "text", 
										  "term_analysis" => array("case_sensitive" => true, 
													   "stem_words" => true, 
													   "use_stopwords"=> false,
													   "language" => "en")))),
			   "data_input" => array("Message" => "Mobile call"),
			   "objective" => "000000",
			   "prediction" => "ham"),
                     array("filename" => "data/spam.csv",
                           "options" => array("fields" => array("000001" => array("optype" => "text", 
			   							  "term_analysis" => array("case_sensitive" => true, 
										  	                   "stem_words" => true, 
													   "use_stopwords"=> false,
													   "language" => "en")))),
                           "data_input" => array("Message" => "A normal message"),
                           "objective" => "000000",
                           "prediction" => "ham"),
	             #array("filename" => "data/spam.csv",
                     #      "options" => array("fields" => array("000001" => array("optype" => "text", "term_analysis" => array("case_sensitive" => false, "stem_words" => false, "use_stopwords"=> false,"language" => "en")))),
                     #      "data_input" => array("Message" => "Mobile calls"),
		     #	   "objective" => "000000",
		     #	   "prediction" => "spam",
		     #     ),
		     array("filename" => "data/spam.csv",
                           "options" => array("fields" => array("000001" => array("optype" => "text", "term_analysis" => array("case_sensitive" => false, "stem_words" => false, "use_stopwords"=> false,"language" => "en")))),
                           "data_input" => array("Message" => "A normal message"),
                           "objective" => "000000",
                           "prediction" => "ham",
                          ),
                     array("filename" => "data/spam.csv",
                           "options" => array("fields" => array("000001" => array("optype" => "text", "term_analysis" => array("case_sensitive" => false, "stem_words" => true, "use_stopwords"=> true,"language" => "en")))),
                           "data_input" => array("Message" => "Mobile Call"),
                           "objective" => "000000",
                           "prediction" => "spam",
                          ),
                     array("filename" => "data/spam.csv",
                           "options" => array("fields" => array("000001" => array("optype" => "text", "term_analysis" => array("case_sensitive" => false, "stem_words" => true, "use_stopwords"=> true,"language" => "en")))),
                           "data_input" => array("Message" => "A normal message"),
                           "objective" => "000000",
                           "prediction" => "ham",
                          ),
                      #array("filename" => "data/spam.csv",
                      #     "options" => array("fields" => array("000001" => array("optype" => "text", "term_analysis" => array("token_mode" => "full_terms_only", "language" => "en")))),
                      #     "data_input" => array("Message" => "FREE for 1st week! No1 Nokia tone 4 ur mob every week just txt NOKIA to 87077 Get txting and tell ur mates. zed POBox 36504 W45WQ norm150p/tone 16+"),                           
                      #     "objective" => "000000",
                      #     "prediction" => "spam", 
                      #    ),
                      array("filename" => "data/spam.csv",
                           "options" => array("fields" => array("000001" => array("optype" => "text", "term_analysis" => array("token_mode" => "full_terms_only", "language" => "en")))),
                           "data_input" => array("Message" => "Ok"),  
                           "objective" => "000000",
                           "prediction" => "ham",
                          )
		     );

       foreach($data as $item) {
	    print "Given I create a data source uploading a ". $item["filename"]. " file\n";
            $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
            $this->assertEquals(1, $source->object->status->code);

            print "And I wait until the source is ready\n";
            $resource = self::$api->_check_resource($source->resource, null, 3000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I update the source with params " . json_encode($item["options"]) . "\n";
            $source = self::$api->update_source($source->resource, $item["options"]);

            print "And I create dataset with local source\n";
            $dataset = self::$api->create_dataset($source->resource);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
            $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

            print "And I wait until the dataset is ready\n";
            $resource = self::$api->_check_resource($dataset->resource, null, 3000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create model\n";
            $model = self::$api->create_model($dataset->resource);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $model->code);

            print "And I wait until the model is ready\n";
            $resource = self::$api->_check_resource($model->resource, null, 3000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create a local model";
            $localmodel = new Model($model->resource, self::$api);#$model->resource, self::$api);

            print "When I create a prediction for " . json_encode($item["data_input"]) . "\n";
            $prediction = self::$api->create_prediction($model, $item["data_input"]);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $prediction->code);

            print "Then the prediction for " . $item["objective"] . " is " . $item["prediction"] . "\n";
            $this->assertEquals($item["prediction"], $prediction->object->prediction->{$item["objective"]});

            print "And I create a local prediction for " . json_encode($item["data_input"]) ."\n"; 
            $prediction = $localmodel->predict($item["data_input"]);

            print "Then the local prediction is " .  $item["prediction"] ." \n";
            $this->assertEquals($prediction->output, $item["prediction"]);
       }
    }
    #
    # Successfully comparing predictions with proportional missing strategy
    #
    public function test_scenario3() {
        $data = array(array("filename" => "data/iris.csv",
                               "data_input" => array(), 
			       "objective" => "000004",
			       "prediction" => "Iris-setosa",
			       "confidence" => 0.2629),
	              array("filename" => "data/grades.csv",
                               "data_input" => array(),
                               "objective" => "000005",
                               "prediction" => "68.62224",
                               "confidence" => 27.5358),
                      array("filename" => "data/grades.csv",
                               "data_input" => array('Midterm' => 20),
                               "objective" => "000005",
                               "prediction" => "46.69889",
                               "confidence" => 37.2760),
                      array("filename" => "data/grades.csv",
                               "data_input" => array('Midterm' => 20, "Tutorial" => 90, "TakeHome" => 100),
                               "objective" => "000005",
                               "prediction" => "28.06",
                               "confidence" => 24.86634));
      
        foreach($data as $item) {
	    print "\nSuccessfully comparing predictions with proportional missing strategy\n";
            print "Given I create a data source uploading a ". $item["filename"]. " file\n";
            $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
            $this->assertEquals(1, $source->object->status->code);

            print "And I wait until the source is ready\n";
            $resource = self::$api->_check_resource($source->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create dataset with local source\n";
            $dataset = self::$api->create_dataset($source->resource);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
            $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

            print "And I wait until the dataset is ready\n";
            $resource = self::$api->_check_resource($dataset->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create model\n";
            $model = self::$api->create_model($dataset->resource);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $model->code);

            print "And I wait until the model is ready\n";
            $resource = self::$api->_check_resource($model->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I create a local model";
            $localmodel = new Model($model->resource, self::$api);

            print "When I create a proportional missing strategy prediction for " . json_encode($item["data_input"]) . "\n";
            $prediction = self::$api->create_prediction($model->resource, $item["data_input"], array('missing_strategy' => 1));
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $prediction->code);

            print "Then the prediction for " . $item["objective"] . " is " . $item["prediction"];
            $this->assertEquals($item["prediction"], $prediction->object->prediction->{$item["objective"]});

            print "And I create a local prediction for " .  json_encode($item["data_input"]) . "\n";
            $prediction = $localmodel->predict($item["data_input"], true, false, STDOUT, true, 1);

            print "Then the local prediction is  " . $item["prediction"] . "\n";
	    $prediction_value = null;
	    $confidence_value = null;
	    if (is_object($prediction)) {
	       $prediction_value = $prediction->prediction;
	       $confidence_value = $prediction->confidence;
	    } else {
               $prediction_value = $prediction[0];
               $confidence_value = $prediction[1];
	    }

            $this->assertEquals(is_numeric($prediction_value) ? round($prediction_value, 5) : $prediction_value, $item["prediction"]);
	    print "And the local prediction's confidence is " . $item["confidence"]. "\n";
	    $this->assertEquals(round($confidence_value, 4), round($item["confidence"], 4));
        }
    }
*/ 
    public function test_scenario4() {
     $data = array(array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",  
                                  						      "term_analysis" => array("case_sensitive" => true, 
													       "stem_words"=> true, 	
													       "use_stopwords"=>false, 
													       "language"=> "en")))), 
                               "data_input" => array("Type" => "ham", "Message" => "Mobile call"),
                               "centroid" => "Cluster 4",
                               "distance" => 0.35566243270259357),
                    #array("filename" => "data/spam.csv",
                    #           "options" => array("fields" => array("000001" => array("optype" => "text",
                    #                                                                  "term_analysis" => array("case_sensitive" => true,
                    #                                                                                           "stem_words"=> true,
                    #                                                                                           "use_stopwords"=>false
                    #                                                                                           )))),
                    #           "data_input" => array("Type" => "ham", "Message" => "A normal message"),
                    #           "centroid" => "Cluster 5",
                    #           "distance" => 0.375),
                   array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",
                                                                                      "term_analysis" => array("case_sensitive" => false,
                                                                                                               "stem_words"=> false,
                                                                                                               "use_stopwords"=>false,
													       "language"=>"en"
                                                                                                               )))),
                               "data_input" => array("Type" => "ham", "Message" => "Mobile calls"),
                               "centroid" => "Cluster 0",
                               "distance" => 0.5),
                   array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",
                                                                                      "term_analysis" => array("case_sensitive" => false,
                                                                                                               "stem_words"=> false,
                                                                                                               "use_stopwords"=>false,
                                                                                                               "language"=>"en"
                                                                                                               )))),
                               "data_input" => array("Type" => "ham", "Message" => "A normal Message"),
                               "centroid" => "Cluster 0",
                               "distance" => 0.5),
                   array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",
                                                                                      "term_analysis" => array("case_sensitive" => false,
                                                                                                               "stem_words"=> true,
                                                                                                               "use_stopwords"=>true,
                                                                                                               "language"=>"en"
                                                                                                               )))),
                               "data_input" => array("Type" => "ham", "Message" => "Mobile call"),
                               "centroid" => "Cluster 1",
                               "distance" => 0.38819660112501053),
                    array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",
                                                                                      "term_analysis" => array("case_sensitive" => false,
                                                                                                               "stem_words"=> true,
                                                                                                               "use_stopwords"=>true,
                                                                                                               "language"=>"en"
                                                                                                               )))),
                               "data_input" => array("Type" => "ham", "Message" => "A normal message"),
                               "centroid" => "Cluster 4",
                               "distance" => 0.3663693790437878),
                    array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",
                                                                                      "term_analysis" => array("token_mode" => "full_terms_only",
                                                                                                               "language"=>"en"
                                                                                                               )))),
                               "data_input" => array("Type" => "ham", "Message" => "FREE for 1st week! No1 Nokia tone 4 ur mob every week just txt NOKIA to 87077 Get txting and tell ur mates. zed POBox 36504 W45WQ norm150p/tone 16+"),
                               "centroid" => "Cluster 0",
                               "distance" => 0.5),
                    array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",
                                                                                      "term_analysis" => array("token_mode" => "full_terms_only",
                                                                                                               "language"=>"en"
                                                                                                               )))),
                               "data_input" => array("Type" => "ham", "Message" => "Ok"),
                               "centroid" => "Cluster 0",
                               "distance" => 0.478833312167),
                    array("filename" => "data/spam.csv",
                               "options" => array("fields" => array("000001" => array("optype" => "text",
                                                                                      "term_analysis" => array("case_sensitive" => true,
													       "stem_words"=> true,
     													       "use_stopwords"=>false,	
                                                                                                               "language"=>"en"
                                                                                                               )))),
                               "data_input" => array("Type" => "", "Message" => ""),
                               "centroid" => "Cluster 0",
                               "distance" => 0.707106781187),
                     array("filename" => "data/diabetes.csv",
                               "options" => array("fields" => new stdClass()), 
                               "data_input" => array("pregnancies" => 0, "plasma glucose" => 118, "blood pressure" => 84, 
                                                     "triceps skin thickness" => 47, "insulin"=> 230, "bmi" => 45.8, 
                                                     "diabetes pedigree" => 0.551, "age" => 31, "diabetes" => true),
                               "centroid" => "Cluster 5",
                               "distance" => 0.4006712471727391));
                     #array("filename" => "data/iris_sp_chars.csv",
                     #          "options" => array("fields" => new stdClass()),
                     #          "data_input" => array("pétal.length" => 1, "pétal&width" => 2, "sépal.length" => 1, 
                     #                                "sépal&width" => 2, "spécies"=> "Iris-setosa"),
                     #          "centroid" => "Cluster 5",
                     #          "distance" => 0.4006712471727391)); 

      foreach($data as $item) {
            print "\n Successfully comparing centroids with or without text options\n";
            print "Given I create a data source uploading a ". $item["filename"]. " file\n";
            $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
            $this->assertEquals(1, $source->object->status->code);

            print "And I wait until the source is ready\n";
            $resource = self::$api->_check_resource($source->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

            print "And I update the source with params ";
            $_source = self::$api->update_source($source->resource, $item["options"]);

            print "And I create dataset with local source\n";
            $dataset = self::$api->create_dataset($source->resource);
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
            $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

            print "And I wait until the dataset is ready\n";
            $resource = self::$api->_check_resource($dataset->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
            print "And I create a cluster\n"; 
            $cluster = self::$api->create_cluster($dataset->resource, array('seed'=>'BigML tests','cluster_seed'=> 'BigML', 'k' => 8));
            $this->assertEquals(BigMLRequest::HTTP_CREATED, $cluster->code);
            $this->assertEquals(BigMLRequest::QUEUED, $cluster->object->status->code);

            print "And I wait until the cluster is ready\n";
            $resource = self::$api->_check_resource($cluster->resource, null, 10000, 30);
            $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
           
            print "And I create a local cluster\n";

            $local_cluster = new Cluster($cluster->resource, self::$api);

            print "When I create a centroid for " . json_encode($item["data_input"]) ."\n";
            $centroid = self::$api->create_centroid($cluster->resource, $item["data_input"]);

            print  "Then the centroid is " . $item["centroid"] . " with distance " . $item["distance"] . "\n";
            $this->assertEquals(round($item["distance"], 6), round($centroid->object->distance, 6));

            print "And I create a local centroid for " . json_encode($item["data_input"]) ."\n";
            $local_centroid = $local_cluster->centroid($item["data_input"]);
            print "Then the local centroid is " . $item["centroid"] . " with distance " . $item["distance"] . "\n";
            $this->assertEquals($item["centroid"], $local_centroid["centroid_name"]);
            $this->assertEquals(round($item["distance"],6), round($local_centroid["distance"], 6));

      }

    }
/* 
    public function test_scenario5() {
      $data = array(array("filename"=> "data/iris.csv", "options" =>  array("summary_fields" => array("sepal width"), 'seed'=>'BigML tests','cluster_seed'=> 'BigML', 'k' => 8), 
                    "data_input"=> array("petal length"=> 1, "petal width"=> 1, "sepal length" => 1, "species" => "Iris-setosa"), 
                    "centroid" => "Cluster 0", "distance" => 0.7310939266123302));
      foreach($data as $item) {
          print "Successfully comparing centroids with summary fields:\n";
          print "Given I create a data source uploading a ". $item["filename"]. " file\n";
          $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
          $this->assertEquals(1, $source->object->status->code);

          print "And I wait until the source is ready\n";
          $resource = self::$api->_check_resource($source->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "And I create dataset with local source\n";
          $dataset = self::$api->create_dataset($source->resource);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
          $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

          print "And I wait until the dataset is ready " . $dataset->resource . " \n";
          $resource = self::$api->_check_resource($dataset->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
          print "And I create a cluster with options " . json_encode($item["options"]) . "\n"; 
          $cluster = self::$api->create_cluster($dataset->resource, $item["options"]); 
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $cluster->code);
          $this->assertEquals(BigMLRequest::QUEUED, $cluster->object->status->code);

          print "And I wait until the cluster is ready\n";
          $resource = self::$api->_check_resource($cluster->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
          print "And I create a local cluster\n";
          $local_cluster = new Cluster($cluster->resource, self::$api);

          print "When I create a centroid For ". json_encode($item["data_input"]) . "\n";
          $centroid = self::$api->create_centroid($cluster->resource, $item["data_input"]);

          print  "Then the centroid is " . $item["centroid"] . "with distance " . $item["distance"] . "\n";
          $this->assertEquals(round($item["distance"], 6), round($centroid->object->distance, 6));
          print "And I create a local centroid for " . json_encode($item["data_input"]) . "\n";
          $local_centroid = $local_cluster->centroid($item["data_input"]);
          print "Then the local centroid is " . $item["centroid"] . " with distance " . $item["distance"] . "\n";
          $this->assertEquals($item["centroid"], $local_centroid["centroid_name"]);
          $this->assertEquals(round($item["distance"],6), round($local_centroid["distance"], 6));

      } 
    }
    
    public function test_scenario6() {
      $data = array(array('filename'=> 'data/iris_missing2.csv', 'data_input' => array("petal width"=> 1), 
                          'objective'=> '000004', 'prediction' => 'Iris-setosa', 'confidence' => 0.8064 ),
                    array('filename'=> 'data/iris_missing2.csv', 'data_input' => array("petal width"=> 1,  "petal length" => 4), 
		          'objective'=> '000004', 'prediction' => 'Iris-versicolor', 'confidence' => 0.7847 ));

      foreach($data as $item) {
          print "\nSuccessfully comparing predictions with proportional missing strategy for missing_splits models\n";
          print "Given I create a data source uploading a ". $item["filename"]. " file\n";
          $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
          $this->assertEquals(1, $source->object->status->code);

          print "And I wait until the source is ready\n";
          $resource = self::$api->_check_resource($source->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "And I create dataset with local source\n";
          $dataset = self::$api->create_dataset($source->resource);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
          $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

          print "And I wait until the dataset is ready " . $dataset->resource . " \n";
          $resource = self::$api->_check_resource($dataset->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
          print "And I create a model with missing splits\n"; 
          $model = self::$api->create_model($dataset->resource, array("missing_splits" => true));
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $model->code);

          print "And I wait until the model is ready\n";
          $resource = self::$api->_check_resource($model->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
          print "And I create a local model\n";
          $local_model =  new Model($model->resource, self::$api);

          print "When I create a prediction for " . json_encode($item["data_input"]) . "\n";
          $prediction = self::$api->create_prediction($model, $item["data_input"], array('missing_strategy' => 1));
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $prediction->code);

          print "Then the prediction for " . $item["objective"] . " is " . $item["prediction"] . "\n";
          $this->assertEquals($item["prediction"], $prediction->object->prediction->{$item["objective"]});
          print "Then the confidence is  " . $item["confidence"] . "\n";
          $this->assertEquals(round($item["confidence"], 4), round($prediction->object->confidence, 4));

          print "And I create a proportional missing strategy local prediction for " . json_encode($item["data_input"]) . "\n";
          $local_prediction = $local_model->predict($item["data_input"], true, false, STDOUT, true, 1);

          $confidence_value = null;
	  $prediction_value = null;

	  if (is_object($local_prediction)) {
             $prediction_value  = $local_prediction->output;
	     $confidence_value = $local_prediction->confidence;
	  } else {
	     $prediction_value = $local_prediction[0]; 
	     $confidence_value = $local_prediction[1];
	  }


          print "Then the local prediction is " . $item["prediction"] . "\n";
          $this->assertEquals($prediction_value, $item["prediction"]);
          print "And the local prediction's confidence is " . $item["confidence"] . "\n";
          $this->assertEquals(round($confidence_value, 4), round($item["confidence"], 4));
 
      } 
    }

    public function test_scenario7() {
      $data = array(array('filename' => 'data/tiny_kdd.csv', 'data_input' => array("000020" => 255.0, "000004" => 183.0, "000016" => 4.0, "000024" => 0.04, "000025" => 0.01, "000026" => 0.0, "000019" => 0.25, "000017" => 4.0, "000018" => 0.25, "00001e" => 0.0, "000005" => 8654.0, "000009" => 0, "000023" => 0.01, "00001f" => 123.0) , 'score' => 0.68782));

      foreach($data as $item) {
          print "\nSuccessfully comparing scores from anomaly detectors\n";
          print "Given I create a data source uploading a ". $item["filename"]. " file\n";
          $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
          $this->assertEquals(1, $source->object->status->code);

          print "And I wait until the source is ready\n";
          $resource = self::$api->_check_resource($source->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "And create dataset with local source\n";
          $dataset = self::$api->create_dataset($source->resource);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
          $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

          print "And I wait until the dataset is ready " . $dataset->resource . " \n";
          $resource = self::$api->_check_resource($dataset->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "Then I create an anomaly detector from a dataset\n";
          $anomaly = self::$api->create_anomaly($dataset->resource);

          print "And I wait until the anomaly detector is ready\n";
          $resource = self::$api->_check_resource($anomaly->resource, null, 3000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
          print "And I create a local anomaly detector\n";
          $local_anomaly = new Anomaly($anomaly->resource, self::$api);

          print "When I create an anomaly score for " . json_encode($item["data_input"]) . "\n";
          $anomaly_score = self::$api->create_anomaly_score($anomaly->resource, $item["data_input"]);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $anomaly_score->code);

	  print "Then the anomaly score is " . $item['score'] . "\n";
          $this->assertEquals(round($item['score'], 5), round($anomaly_score->object->score,5));

          print "And I create a local anomaly score for " . json_encode($item["data_input"]) . "\n";
          $local_anomaly_score = $local_anomaly->anomaly_score($item["data_input"], false); 
	  print "Then the local anomaly score is " . $item['score'] . "\n";
          $this->assertEquals(round($item['score'], 5), round($local_anomaly_score,5));

      } 
    }

    public function test_scenario8() {
       $data = array(array('filename' => 'data/iris.csv', 
                           'data_input' => array("petal width" => 0.5, "petal length"=> 0.5, "sepal width" => 0.5, "sepal length"=>0.5), 
			   'prediction' => 'Iris-versicolor'),
		     array('filename' => 'data/iris.csv',
                           'data_input' => array("petal width" => 2, "petal length" => 6, "sepal width" => 0.5, "sepal length" => 0.5),
                           'prediction' => 'Iris-virginica'),
                     array('filename' => 'data/iris.csv',
                           'data_input' => array("petal width" => 1.5, "petal length" => 4, "sepal width" => 0.5, "sepal length" => 0.5),
                           'prediction' => 'Iris-virginica'),
                     array('filename' => 'data/iris.csv',
                           'data_input' => array("petal length" => 1),
                           'prediction' => 'Iris-versicolor'),
                     array('filename' => 'data/iris_sp_chars.csv',
                           'data_input' => array("pétal.length" => 4, "pétal&width".json_decode('"'.'\u0000'.'"') => 1.5, "sépal&width" => 0.5, "sépal.length" => 0.5),
                           'prediction' => 'Iris-virginica')
   	             );

       foreach($data as $item) {
          print "\nSuccessfully comparing logistic regression predictions\n";
          print "Given I create a data source uploading a ". $item["filename"]. " file\n";
          $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
          $this->assertEquals(1, $source->object->status->code);

          print "And I wait until the source is ready\n";
          $resource = self::$api->_check_resource($source->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "And I create dataset with local source\n";
          $dataset = self::$api->create_dataset($source->resource);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
          $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

          print "And I wait until the dataset is ready " . $dataset->resource . " \n";
          $resource = self::$api->_check_resource($dataset->resource, null, 20000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "And I create a logistic regresssion model\n"; 
          $logistic_regression = self::$api->create_logistic_regression($dataset->resource);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $logistic_regression->code);

          print "And I wait until the logistic regression model is ready " . $logistic_regression->resource . "\n";
          $resource = self::$api->_check_resource($logistic_regression->resource, null, 10000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          $logistic_regression = self::$api->get_logistic_regression($logistic_regression->resource);
 
          print "And I create a local logistic regression model\n";
          $localLogisticRegression = new LogisticRegression($logistic_regression);
 
          print "When I create a logistic regression prediction for " . json_encode($item["data_input"])  . "\n";

          $prediction = self::$api->create_prediction($logistic_regression->resource, $item["data_input"]);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $prediction->code);

          print "Then the logistic regression prediction is " . $item["prediction"] . "\n";
          $this->assertEquals($prediction->object->output, $item["prediction"]);

          print "And I create a local logistic regression prediction for " . json_encode($item["data_input"]) . "\n";
          $local_prediction = $localLogisticRegression->predict($item["data_input"]);

          print "Then the local logistic regression prediction is " . $item["prediction"] . "\n";
          $this->assertEquals($item["prediction"],  $local_prediction["prediction"]);

       }
    }

    public function test_scenario9() {
        $data = array(array("filename" => "data/spam.csv", 
	                    "options"=> array("fields" => array("000001" => array("optype"=> "text", 
			                                                          "term_analysis"=> array("case_sensitive"=> true, "stem_words" => true, 
										                          "use_stopwords" => false, "language" => "en")))),
			    "data_input" => array("Message" => "Mobile call"), 
			    "prediction" => 'ham'),
                      array("filename" => "data/spam.csv",
                            "options"=> array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("case_sensitive"=> true, "stem_words" => true,
                                                                                                          "use_stopwords" => false, "language" => "en")))),
                            "data_input" => array("Message" => "A normal message"),
                            "prediction" => 'ham'),
                      array("filename" => "data/spam.csv",
                            "options"=> array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("case_sensitive"=> false, "stem_words" => false,
                                                                                                          "use_stopwords" => false, "language" => "en")))),
                            "data_input" => array("Message" => "Mobile calls"),
                            "prediction" => 'ham'),
                      array("filename" => "data/spam.csv",
                            "options"=> array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("case_sensitive"=> false, "stem_words" => false,
                                                                                                          "use_stopwords" => false, "language" => "en")))),
                            "data_input" => array("Message" => "A normal message"),
                            "prediction" => 'ham'),
                      array("filename" => "data/spam.csv",
                            "options"=> array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("case_sensitive"=> false, "stem_words" => true,
                                                                                                          "use_stopwords" => true, "language" => "en")))),
                            "data_input" => array("Message" => "Mobile call"),
                            "prediction" => 'ham'),
                      array("filename" => "data/spam.csv",
                            "options"=> array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("case_sensitive"=> false, "stem_words" => true,
                                                                                                          "use_stopwords" => true, "language" => "en")))),
                            "data_input" => array("Message" => "A normal message"),
                            "prediction" => 'ham'),
                      array("filename" => "data/spam.csv",
                            "options"=> array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("token_mode"=> "full_terms_only", 
                                                                                                          "language" => "en")))),
                            "data_input" => array("Message" => "FREE for 1st week! No1 Nokia tone 4 ur mob every week just txt NOKIA to 87077 Get txting and tell ur mates. zed POBox 36504 W45WQ norm150p/tone 16+"),
                            "prediction" => 'ham'),
                      array("filename" => "data/spam.csv",
                            "options"=> array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("token_mode"=> "full_terms_only", 
                                                                                                          "language" => "en")))),
                            "data_input" => array("Message" => "Ok"), 
                            "prediction" => 'ham')
                     );

        foreach($data as $item) { 
           print " Successfully comparing predictions with text options\n";
           print "Given I create a data source uploading a ". $item["filename"]. " file\n";
           $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
           $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
           $this->assertEquals(1, $source->object->status->code);

           print "And I wait until the source is ready\n";
           $resource = self::$api->_check_resource($source->resource, null, 30000, 30);
           $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

           print "And I update the source with params " . json_encode($item["options"]) . "\n";
	   $source = self::$api->update_source($source->resource, $item["options"]);

           print "And I create dataset with local source\n";
           $dataset = self::$api->create_dataset($source->resource);
           $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
           $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

           print "And I wait until the dataset is ready " . $dataset->resource . " \n";
           $resource = self::$api->_check_resource($dataset->resource, null, 30000, 30);
           $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

           print "And I create a logistic regresssion model\n"; 
           $logistic_regression = self::$api->create_logistic_regression($dataset->resource);
           $this->assertEquals(BigMLRequest::HTTP_CREATED, $logistic_regression->code);

           print "And I wait until the logistic regression model is ready " . $logistic_regression->resource . "\n";
           $resource = self::$api->_check_resource($logistic_regression->resource, null, 10000, 30);
           $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
    
           $logistic_regression = self::$api->get_logistic_regression($logistic_regression->resource);

           print "And I create a local logistic regression model . " . $logistic_regression->resource . " .\n";
           $localLogisticRegression = new LogisticRegression($logistic_regression);
 
           print "When I create a logistic regression prediction for " . json_encode($item["data_input"])  . "\n";

           $prediction = self::$api->create_prediction($logistic_regression->resource, $item["data_input"]);
           $this->assertEquals(BigMLRequest::HTTP_CREATED, $prediction->code);

           print "Then the logistic regression prediction is " . $item["prediction"] . "\n";
           $this->assertEquals($prediction->object->output, $item["prediction"]);

           print "And I create a local logistic regression prediction for " . json_encode($item["data_input"]) . "\n";
           $local_prediction = $localLogisticRegression->predict($item["data_input"]);

           print "Then the local logistic regression prediction is " . $item["prediction"] . "\n";
           $this->assertEquals($item["prediction"],  $local_prediction["prediction"]);

        }
    }
    
    public function test_scenario10() {
      $data = array(array('filename' => 'data/spam.csv',
                          'objective' => '000000',
                          'options' => array("fields" => array("000001" => array("optype"=> "text",
                                                                                  "term_analysis"=> array("token_mode"=> "full_terms_only", 
                                                                                                          "language" => "en")))),
                          'data_input' => array("Message"=> "A normal message"),
                          'prediction' => 'ham',
                          'probability' => 0.9169
                         ),
                     array('filename' => 'data/movies.csv',
                           'objective' => '000002',
                           'options' => array("fields" => array("000007" => array("optype" => "items",
                                                                                   "item_analysis" => array("separator" => "\$")))),
                           'data_input' => array("gender" => "Female", 
                                                  "genres" =>"Adventure\$Action",
                                                  "timestamp" => 993906291,
                                                  "occupation" => "K-12 student",
                                                  "zipcode" => 59583,
                                                  "rating" => 3),
                           'prediction' => '25-34',
                           'probability' => 0.41686)
                   );
 
      foreach($data as $item) { 
         print "Successfully comparing predictions with text options\n";
         print "Given I create a data source uploading a ". $item["filename"]. " file\n";
         $source = self::$api->create_source($item["filename"], $options=array('name'=>'local_test_source'));
         $this->assertEquals(BigMLRequest::HTTP_CREATED, $source->code);
         $this->assertEquals(1, $source->object->status->code);

         print "And I wait until the source is ready\n";
         $resource = self::$api->_check_resource($source->resource, null, 30000, 30);
         $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

         print "And I update the source with params " . json_encode($item["options"]) . "\n";
         $source = self::$api->update_source($source->resource, $item["options"]);

         print "And I create dataset with local source\n";
         $dataset = self::$api->create_dataset($source->resource);
         $this->assertEquals(BigMLRequest::HTTP_CREATED, $dataset->code);
         $this->assertEquals(BigMLRequest::QUEUED, $dataset->object->status->code);

         print "And I wait until the dataset is ready " . $dataset->resource . " \n";
         $resource = self::$api->_check_resource($dataset->resource, null, 30000, 30);
         $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
 
         print "And I create a logistic regresssion model with objective field ". $item["objective"] . "\n"; 
         $logistic_regression = self::$api->create_logistic_regression($dataset->resource, 
                                                                        array('objective_field' => $item["objective"]));
         $this->assertEquals(BigMLRequest::HTTP_CREATED, $logistic_regression->code);

         print "And I wait until the logistic regression model is ready " . $logistic_regression->resource . "\n";
         $resource = self::$api->_check_resource($logistic_regression->resource, null, 10000, 30);
         $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);
       
         $logistic_regression = self::$api->get_logistic_regression($logistic_regression->resource);

         $logistic_regression = self::$api->get_logistic_regression("logisticregression/569643928a318f4846000cf4");

         print "And I create a local logistic regression model . " . $logistic_regression->resource . " .\n";
         $localLogisticRegression = new LogisticRegression($logistic_regression);

         $prediction = self::$api->create_prediction($logistic_regression->resource, $item["data_input"]);
         $this->assertEquals(BigMLRequest::HTTP_CREATED, $prediction->code);

         print "Then the logistic regression prediction is " . $item["prediction"] . "\n";
         $this->assertEquals($prediction->object->output, $item["prediction"]);
        
         print "And the logistic regression probability for the prediction is ". $item["probability"] ."\n"; 

         foreach ($prediction->object->probabilities as $key => $value) {
            if ($value[0] == $prediction->object->output) {
                $this->assertEquals(round($value[1],4), round($item["probability"], 4));
                break;  
            } 
         }
           
         print "And I create a local logistic regression prediction for " . json_encode($item["data_input"]) . "\n";
         $local_prediction = $localLogisticRegression->predict($item["data_input"]);
         print "Then the local logistic regression prediction is " . $item["prediction"] . "\n";
         $this->assertEquals($item["prediction"],  $local_prediction["prediction"]);

      }	
    }  */ 
}    