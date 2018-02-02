<?php
/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 05/12/17
 * Time: 16:10
 */

class RequestResponsePairBookingCancel extends RequestResponsePair {
	/* @param $request string
	 * @param $api API */
	function __construct($request,$api) {
		$response = $api->sendCurlRequest( $request, $api->constants->soap_action_bookcancel );
		$this->request = $request;
		$this->response = $response;
	}

	public static function create_pair_from_request($request, $api) {
		$pair = new RequestResponsePairBookingCancel( $request, $api );


		return $pair;
	}
}