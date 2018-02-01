<?php

/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 7/12/17
 * Time: 12:45 PM
 */
class RequestResponsePairBookingCreate extends RequestResponsePair {
	public static $key = 'request_response_pair_booking';
	public static $current_pair;
	private $roomsdata;

	/**
	 * @return mixed
	 */
	public function get_roomsdata() {
		return $this->roomsdata;
	}

	/**
	 * RequestResponsePairBookingCreate constructor.
	 *
	 * @param $request string
	 * @param $api API
	 */
	private function __construct($request, $api) {
		$response = $api->sendCurlRequest( $request, $api->constants->soap_action_book );
		$this->request = $request;
		$this->response = $response;
		$this->roomsdata = $api->getArrayFromXmlResponseBooking( $response );
	}



	public static function create_pair_from_request( $request, $api) {

		$pair = new RequestResponsePairBookingCreate( $request, $api);
		self::$current_pair = $pair;
		$_SESSION[ self::$key ] = $pair;

		return $pair;

	}



	function get_final_price() {
		$sum=0;
		foreach ( $this->roomsdata['Booking']['HotelBooking'] as $hotelbooking_entry ) {
			$sum += $hotelbooking_entry['TotalSellingPrice']['@attributes']['amt'];
		}

		return $sum;

	}



	function has_failed() {
		if ( !$this->response ) {
			return true;
		}


		if (strpos($this->response,"<faultstring>")>0) {
			return true;
		}

		return false;
	}
}