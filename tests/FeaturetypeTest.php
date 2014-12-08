<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Client.php";
require_once __DIR__ . "/../Workspace.php";
require_once __DIR__ . "/../DataStore.php";
require_once __DIR__ . "/../Featuretype.php";

use GeoServer\Client as Client;
use GeoServer\Session as Session;
use GeoServer\Workspace as Workspace;
use GeoServer\Datastore as Datastore;
use GeoServer\Featuretype as Featuretype;

class FeaturetypeTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {

    $this->plugin = new Guzzle\Plugin\Mock\MockPlugin();
    $this->consumer = new Guzzle\Http\Client();
    $this->consumer->addSubscriber($this->plugin);

    $this->url = 'https://localhost/geoserver';
    $this->pass = 'secret';
    $this->file_path = 'file://' . __DIR__ . '/fixtures/test_shp.zip';

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200));
    $this->session = new Session('admin', $this->pass, $this->url, $this->consumer);

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('workspace' => array()))));
    $this->workspace = new WorkSpace($this->session->client, 'test_workspace_a');

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('dataStore' => array()))));
    $this->data_store = new Datastore($this->session->client, 'test_data_store_a', $this->workspace);
  }

  public function testRead() {

    try {

      $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('featuretype' => array()))));

      $feature_type = new Featuretype($this->session->client, 'test_feature_type_a', $this->data_store);
    } catch(Exception $e) {

      $this->fail($e->getMessage());
    }
  }

  function testCreate() {

    try {

      $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('featuretype' => array()))));
      $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('featuretype' => array()))));

      $feature_type = new Featuretype($this->session->client, 'test_feature_type_b', $this->data_store);

      $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('featuretype' => array()))));

      $feature_type->create($this->file_path);
    } catch(Exception $e) {

      $this->fail($e->getMessage());
    }
  }

  function testUpdate() {


  }

  function testDelete($recurse = FALSE) {


  }
}
