<?php

namespace GeoServer;

include_once __DIR__ . "/vendor/autoload.php";
include_once __DIR__ . "/Resource.php";

use GeoServer\Resource as Resource;

/**
 * Class for handling a raster data set
 *
 */

class Coverage extends Resource {

  public $coveragestore;
  private $base_path;
  public $granules;

  function __construct($client, $name, $coverage_store) {

    $this->coverage_store = $coverage_store;
    $this->base_path = 'workspaces/' . $this->coverage_store->workspace->name . '/coveragestores/' . $this->coverage_store->name . '/coverages';

    $this->post_path = $this->base_path;

    parent::__construct($client, $name);

    /*
    $this->get_path = $this->base_path . '/' . $this->name;
    $this->put_path = $this->get_path;
    $this->delete_path = $this->put_path;
    */
  }

  /**
   * Create remote resource.
   */
  public function create($file_path) {

    //return $this->client->post($this->post_path);
    $fh = fopen($file_path, "rb");

    if(!preg_match('/tiff?$/', $file_path)) {

      throw new \Exception("Unsupported file format for $file_path");
    }
    $response = $this->client->post($this->post_path . '.json',
				    $fh,
				    array('content-type' => 'image/tiff'));

    if(!$response->isSuccessful()) {

      throw new Exception("Failed to create a coverage store from $file_path");
    }

    return $this->read();
  }

  /**
   * Read remote resource.
   */
  public function read() {

    //return $this->client->get($this->get_path);
    $response = $this->client->get($this->base_path . '/' . $this->name . '.json', array(), array('content-type' => 'application/json'));

    //$data = $response->json();
    // If this coverage store cannot be found, and if a file path was set...
    if($response->getStatusCode() == 404 and !is_null($this->file_path)) {

      // ...attempt to create the coverage store.
      return $this->create();
    } elseif(!$response->isSuccessful()) {

      throw new \Exception("Failed to retrieve the coverage store $name");
    }

    $data = $response->json();

    foreach($data['coverage'] as $property => $value) {

      $this->{$property} = $values;
    }

    return $this;
  }

  /**
   * Update remote resource.
   */
  function update($file, $extension = 'shp', $configure = 'first', $target = 'shp', $update = 'append', $charset = 'utf-8') {

    $this->client->put($this->put_path, array());
  }

  /**
   * Delete remote resource.
   */
  function delete($recurse = FALSE) {

    $this->client->delete($this->delete_path, array('recurse' => $recurse));
  }
}
