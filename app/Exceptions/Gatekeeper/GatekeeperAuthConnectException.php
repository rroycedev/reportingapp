<?php

namespace App\Exceptions\Gatekeeper;

use Exception;

class GatekeeperAuthConnectException extends Exception {
	public function report(Exception $exception) {
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $exception
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $exception) {
		$ex = new Exception("Unable to connect to Gatekeeper authorization server");

	}
}
