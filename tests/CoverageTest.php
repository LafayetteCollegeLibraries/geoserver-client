<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Session.php";
require_once __DIR__ . "/../Workspace.php";
require_once __DIR__ . "/../CoverageStore.php";
require_once __DIR__ . "/../Coverage.php";

use GeoServer\Session as Session;
use GeoServer\Workspace as Workspace;
use GeoServer\CoverageStore as CoverageStore;
use GeoServer\Coverage as Coverage;

class CoverageTest extends PHPUnit_Framework_TestCase {

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

    $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('coverageStore' => array()))));
    $this->coverage_store = new CoverageStore($this->session->client, 'test_coverage_store_a', $this->workspace);
  }

  public function testRead() {

    try {

      $this->plugin->addResponse(new Guzzle\Http\Message\Response(200, array('Content-Type' => 'application/json'), json_encode(array('coverage' => array()))));
      $coverage_store = new Coverage($this->session->client, 'test_coverage_store_a', $this->workspace);
    } catch(Exception $e) {

      $this->fail($e->getMessage());
    }
  }
}
