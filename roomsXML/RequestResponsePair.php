<?php

/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 7/12/17
 * Time: 12:45 PM
 */
abstract class RequestResponsePair {
	protected $request;
	protected $response;


	public function get_request() {
		return $this->request;
	}

	public function get_response() {
		return $this->response;
	}


	public function has_as_request( $new_request ) {


		if ( $this->request == $new_request ) {
			return true;
		}

		return false;

	}


}