<?php
/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 15/09/17
 * Time: 14:43
 */

class RequestResponsePairBookingCancel extends RequestResponsePair {
	public $request;
	public $response;


	public function get_request() {
		return $this->request;
	}

	public function get_response() {
		return $this->response;
	}




	/**
	 * RequestResponsePairBookingCancel constructor.
	 *
	 * @param $request string
	 * @param $constants Constants
	 * @param $api API
	 */
	function __construct($request, $api) {
		$constants = $api->constants;
		$this->response = $api->sendCurlRequest( $request, $constants->soap_action_bookcancel );
		$this->request = $request;

	}



}