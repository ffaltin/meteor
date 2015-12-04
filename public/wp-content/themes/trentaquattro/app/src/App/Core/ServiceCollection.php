<?php

namespace App\Core;

use Exception;

class ServiceCollection {

	protected $collection = [];

	public function add($name, $service) {
		if (!isset($this->collection[$name]))
			$this->collection[$name] = $service;
		else throw new Exception(sprintf("The Service %s already exists", ucfirst($name)));

	}

	public function remove($name) {
		if (isset($this->collection[$name]))
			unset($this->collection[$name]);
		else throw new Exception(sprintf("The Service %s does not exist", ucfirst($name)));
	}

	public function overwrite($name, $service) {
		$this->collection[$name] = $service;
	}

	public function has($name) {
		return isset($this->collection[$name]);
	}

	public function get($name) {
		if (isset($this->collection[$name]))
			return $this->collection[$name];
		else throw new Exception(sprintf("The Service %s does not exist", ucfirst($name)));
	}

}
