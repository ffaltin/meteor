<?php

namespace App\Core;

class Request {

	protected $collection;

	public function __construct() {
		$this->buildRequestCollection();
	}

	private function buildRequestCollection() {
		$out = [];
		foreach($_SERVER as $key=>$value) {
			if (substr($key,0,5)=="HTTP_") {
				$key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
				$out[$key]=$value;
			}else{
				$out[$key]=$value;
			}
		}

		$this->collection = $out;
	}

	/*
	 * Grab method
	 */
	public function getMethod() {
		return $this->collection["REQUEST_METHOD"];
	}

	public function isPost() {
		return $this->collection["REQUEST_METHOD"] === "POST";
	}

	public function isGet() {
		return $this->collection["REQUEST_METHOD"] === "GET";
	}

	public function getUri() {
		return $this->collection["REQUEST_URI"];
	}

	public function getContentType() {
		return $this->collection["CONTENT_TYPE"];
	}

	public function getContentLength() {
		return $this->collection["CONTENT_LENGTH"];
	}

	public function getQuery() {
		return $this->collection["QUERY_STRING"];
	}

	public function getHost() {
		return $this->collection["Host"];
	}

	public function getStatus() {
		return $this->collection["REDIRECT_STATUS"];
	}

	public function get($key,$forcePost = false) {
		if ($this->isPost()) return $_POST[$key];
		else {
			if ($forcePost) throw new Exception("You must get this field only on POST Method");
			return $_GET[$key];
		}
	}

	public function has($key, $forcePost = false) {
		return !is_null($this->get($key, $forcePost));
	}

	public function selfRedirection() {
		return header("location: " . $this->getUri());
	}

}
