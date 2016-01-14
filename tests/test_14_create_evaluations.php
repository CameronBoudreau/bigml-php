<?php
if (!class_exists('bigml')) {
  include '../bigml/bigml.php';
}
class BigMLTestCreateDataset extends PHPUnit_Framework_TestCase
{
    protected static $username; # "you_username"
    protected static $api_key; # "your_api_key"

    protected static $api;

    public static function setUpBeforeClass() {
       self::$api =  new BigML(self::$username, self::$api_key, true);
       ini_set('memory_limit', '512M');
    }
    /*
     Successfully creating an evaluation
    */

    public function test_scenario1() {
      $data = array(array('filename' => 'data/iris.csv', 
			  'measure' => 'average_phi',
			  'value' => 1));


      foreach($data as $item) {
          print "\nSuccessfully creating an evaluation\n";
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

          print "And I create model\n";
          $model = self::$api->create_model($dataset->resource);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $model->code);
 
          print "And I wait until the model is ready\n";
          $resource = self::$api->_check_resource($model->resource, null, 3000, 30);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "When I create an evaluation for the model with the dataset\n";
          $evaluation = self::$api->create_evaluation($model, $dataset);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $evaluation->code);
 
          print "And I wait until the evaluation is ready\n";
          $resource = self::$api->_check_resource($evaluation->resource, null, 10000, 50);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

	  print "Then the measured " . $item["measure"] ." is " . $item["value"] . "\n";
          $evaluation = self::$api->get_evaluation($evaluation->resource);
          $this->assertEquals(floatval($evaluation->object->result->model->{$item["measure"]}), floatval($item["value"]));

      } 
    }

    /*  Successfully creating an evaluation for an ensemble */
    public function test_scenario2() {
        $data = array(array('filename' => 'data/iris.csv',
                          'number_of_models' => 5,
                          'measure' => 'average_phi',
                          'value' => '0.98029', 
                          'tlp' => 1));
        foreach($data as $item) {
          print "\nSuccessfully creating an evaluation for an ensemble\n";
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

          print "And I create a ensemble of ". $item["number_of_models"] . " models and " . $item["tlp"] . " tlp\n";
          $ensemble = self::$api->create_ensemble($dataset->resource, array("number_of_models"=> $item["number_of_models"], "tlp"=> $item["tlp"],"seed" => 'BigML', 'sample_rate'=> 0.70));
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $ensemble->code);

          print "And I wait until the ensemble is ready\n";
          $resource = self::$api->_check_resource($ensemble->resource, null, 10000, 50);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "When I create an evaluation for the model with the dataset\n";
          $evaluation = self::$api->create_evaluation($ensemble, $dataset);
          $this->assertEquals(BigMLRequest::HTTP_CREATED, $evaluation->code);

          print "And I wait until the evaluation is ready\n";
          $resource = self::$api->_check_resource($evaluation->resource, null, 10000, 50);
          $this->assertEquals(BigMLRequest::FINISHED, $resource["status"]);

          print "Then the measured " . $item["measure"] ." is " . $item["value"] . "\n";
          $evaluation = self::$api->get_evaluation($evaluation->resource);
          $this->assertEquals(floatval($evaluation->object->result->model->{$item["measure"]}), floatval($item["value"]));

        }
    }
}    