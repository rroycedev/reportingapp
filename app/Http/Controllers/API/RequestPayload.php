<?php

namespace App\Http\Controllers\API;

class RequestPayload {
	protected $success = false;
	protected $message = "";
	protected $data = null;

	public function __construct($success, $message, $data) {
		$this->data = $data;
		$this->success = $success;
		$this->message = $message;
	}

	public function response() {
		return array("success" => $this->success, "message" => $this->message, "data" => $this->data);
	}
}