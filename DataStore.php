<?php

namespace GeoServer;

include_once __DIR__ . "/vendor/autoload.php";
include_once __DIR__ . "/Resource.php";

use GeoServer\Resource as Resource;

/**
 * Class for handling vector data sets
 * @todo Implement
 *
 */
class Datastore extends Resource {

  const FILE = 0;
  const URL = 1;
  const EXTERNAL = 2;

  public static function extension_to_str($extension) {

    switch($extension) {
    case FILE:

      $value = 'file';
      break;
    case URL:

      $value = 'url';
      break;
    case EXTERNAL:

      $value = 'external';
      break;
    default:

      throw new Exception("Unsupported datastore extension: " . $extension);
    }

    return $value;
  }

  public $workspace;
  private $base_path;
  private $post_put_path;

  function __construct($client, $name, $workspace, $file_path = NULL) {

    $this->workspace = $workspace;
    $this->base_path = 'workspaces/' . $this->workspace->name . '/datastores';
    $this->file_path = $file_path;

    parent::__construct($client, $name);
  }

  /**
   * Create a data store
   * @todo Abstract for other entities within GeoServer
   *
   */
  public function create($file_path = NULL) {

    if(is_null($file_path)) {

      $file_path = $this->file_path;
    }
    $fh = fopen($file_path, "rb");

    if(!preg_match('/zip?$/', $file_path)) {

      throw new Exception("Unsupported file format for $file_path");
    }
    $response = $this->client->put($this->base_path . '/' . $this->name . '/file.shp',
				   $fh,
				   array('content-type' => 'application/zip'));

    if(!$response->isSuccessful()) {

      throw new Exception("Failed to create a coverage store from $file_path");
    }

    return $this->read();
  }

  /**
   * Load all data stores
   *
   */
  protected function read() {

    $response = $this->client->get($this->base_path . '/' . $this->name . '.json', array(), array('content-type' => 'application/json'));

    // If this coverage store cannot be found, and if a file path was set...
    if($response->getStatusCode() == 404 and !is_null($this->file_path)) {

      // ...attempt to create the coverage store.
      return $this->create();
    } elseif(!$response->isSuccessful()) {

      throw new Exception("Failed to retrieve the data store $name");
    }

    $data = $response->json();

    foreach($data['dataStore'] as $property => $value) {

      $values = array();

      switch($property) {

      case 'features':

	// Retrieve the coverage stores
	$response = $this->client->get($this->base_path . '/' . $this->name . '/features.json', array(), array('content-type' => 'application/json'));
	$data = $response->json();

	if(array_key_exists('features', $data) and !empty($data['features'])) {

	  foreach($data['features'] as $key => $value) {

	    $feature = array_shift($value);
	    $values[$feature['name']] = new Featuretype($this->client, $feature['name'], $this);
	  }

	  $this->{$property} = $values;
	}
	break;

      default:

	break;
      }
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

    $this->client->delete($this->base_path, array('recurse' => $recurse));
  }
}
