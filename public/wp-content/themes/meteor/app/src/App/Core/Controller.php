<?php

namespace App\Core;

use Timber;

class Controller {

	protected $context;

	public function addToLayout() {
		$data = [];
		$data["menu"] = $this->context["menu"];
		$data["site"] = $this->context["site"];
		return $data;
	}

	public function __construct($timberContext) {
		$this->context = $timberContext;
	}

	public function render($view,$data=[]) {
		$layoutData = $this->addToLayout();
		$data = array_merge($data, $layoutData);
		return Timber::render($view, $data);
	}

	public function getDb() {
		return $this->context["services"]->get("db");
	}

	public function getRequest() {
		return $this->context["services"]->get("request");
	}

	public function getSession() {
		return $this->context["services"]->get("session");
	}

	public function getMailer() {
		return $this->context["services"]->get("mailer");
	}

	public function getFormFactory() {
		return $this->context["services"]->get("formFactory");
	}

}
