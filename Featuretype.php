<?php

namespace GeoServer;

include_once __DIR__ . "/vendor/autoload.php";
include_once __DIR__ . "/Resource.php";

use GeoServer\Resource as Resource;

/**
 * Class modeling GeoServer Feature entities
 *
 */
class Featuretype extends Resource {

  public $data_store;
  private $base_path;

  function __construct($client, $name, $data_store) {

    $this->data_store = $data_store;
    $this->base_path = 'workspaces/' . $this->data_store->workspace->name . '/datastores/' . $this->data_store->name . '/featuretypes';

    $this->post_path = $this->base_path;
    $this->read_path = $this->base_path . '/' . $this->name . '.json';
    $this->put_path = $this->read_path;

    parent::__construct($client, $name);
  }

  /**
   * Create remote resource.
   */
  public function create($file_path) {

    //return $this->client->post($this->post_path);
    $fh = fopen($file_path, "rb");

    if(!preg_match('/zip?$/', $file_path)) {

      throw new \Exception("Unsupported file format for $file_path");
    }
    $response = $this->client->post($this->post_path . '.json',
				    $fh,
				    array('content-type' => 'application/zip'));

    if(!$response->isSuccessful()) {

      throw new Exception("Failed to create a coverage store from $file_path");
    }

    return $this->read();
  }

  /**
   * Read remote resource.
   */
  public function read() {

    $response = $this->client->get($this->read_path, array(), array('content-type' => 'application/json'));

    // If this coverage store cannot be found, and if a file path was set...
    if($response->getStatusCode() == 404 and !is_null($this->file_path)) {

      // ...attempt to create the coverage store.
      return $this->create();
    } elseif(!$response->isSuccessful()) {

      throw new Exception("Failed to retrieve the coverage store $name");
    }

    $data = $response->json();

    foreach($data['featuretype'] as $property => $value) {

      $this->{$property} = $values;
    }
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
