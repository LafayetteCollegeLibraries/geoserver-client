<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Session.php";
require_once __DIR__ . "/../Workspace.php";

use GeoServer\Session as Session;
use GeoServer\Workspace as Workspace;
use GeoServer\DataStore as DataStore;

class DataStoreTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {

    $this->plugin = new Guzzle\Plugin\Mock\MockPlugin();
    $this->consumer = new Guzzle\Http\Client();
    $this->consumer->addSubscriber($this->plugin);

    $this->url = 'https://localhost/geoserver';
    $this->pass = 'secret';

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200));
    $this->session = new Session('admin', $this->pass, $this->url, $this->consumer);

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('workspace' => array()))));
    $this->workspace = new WorkSpace($this->session->client, 'test_workspace_a');
  }

  public function testRead() {

    try {

      $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/zip'), json_encode(array('dataStore' => array()))));
      $data_store = new DataStore($this->session->client, 'test_data_store_a', $this->workspace);
    } catch(Exception $e) {

      $this->fail($e->getMessage());
    }
  }

  public function testCreate() {

    $file_path = 'file:///tmp/data_store_a.shp.zip';

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/zip'), json_encode(array('dataStore' => array()))));
    $data_store = new DataStore($this->session->client, 'test_data_store_b', $this->workspace, $file_path);
  }
}
