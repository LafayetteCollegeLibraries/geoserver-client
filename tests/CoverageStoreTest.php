<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Session.php";
require_once __DIR__ . "/../Workspace.php";

use GeoServer\Session as Session;
use GeoServer\Workspace as Workspace;
use GeoServer\CoverageStore as CoverageStore;

class CoverageStoreTest extends PHPUnit_Framework_TestCase {

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

      $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('coverageStore' => array()))));
      $coverage_store = new CoverageStore($this->session->client, 'test_coverage_store_a', $this->workspace);
    } catch(Exception $e) {

      $this->fail($e->getMessage());
    }
  }

  public function testCreate() {

    $file_path = 'file:///tmp/coverage_store_a.geo.tiff';

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('coverageStore' => array()))));
    $coverage_store = new CoverageStore($this->session->client, 'test_coverage_store_b', $this->workspace, $file_path);
  }

  /**
   * @todo Implement
   *
   */
  public function testUpdate() {


  }

  /**
   * Testing for the deleting of Coverage Stores
   *
   */
  public function testDelete() {

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('coverageStore' => array()))));
    $coverage_store = new CoverageStore($this->session->client, 'test_coverage_store_c', $this->workspace);

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200));
    $coverage_store->delete();
  }

  /**
   * @todo Implement
   *
   */
  public function testCreateCoverage() {


  }
}
