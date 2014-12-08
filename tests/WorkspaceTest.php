<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Client.php";
require_once __DIR__ . "/../Workspace.php";

use GeoServer\Client as Client;
use GeoServer\Session as Session;
use GeoServer\Workspace as Workspace;

class WorkspaceTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {

    $this->plugin = new Guzzle\Plugin\Mock\MockPlugin();
    $this->consumer = new Guzzle\Http\Client();
    $this->consumer->addSubscriber($this->plugin);

    $this->url = 'https://localhost/geoserver';
    $this->pass = 'secret';

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200));
    $this->session = new Session('admin', $this->pass, $this->url, $this->consumer);
  }

  public function testConstruct() {

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('workspace' => array()))));
    $workspace = new WorkSpace($this->session->client, 'test_workspace_a');
  }

  public function testCreateCoverageStore() {

    $file_path = 'file:///tmp/test_coverage_store.geo.tiff';

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('workspace' => array()))));
    $workspace = new WorkSpace($this->session->client, 'test_workspace_a');

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('coverageStore' => array()))));
    $workspace->createCoverageStore('test_coveragestore_a', $file_path);
  }

  public function testDeleteCoverageStore() {

  }

  public function testCreateDataStore() {

  }

  public function testDeleteDataStore() {

  }

}