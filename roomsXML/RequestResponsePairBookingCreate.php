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

	public function get_MealTypes() {
		$meal_types=array();
		foreach ( $this->roomsdata['Booking']['HotelBooking'] as $hotelbooking_entry ) {
			$meal_types[]=$hotelbooking_entry['Room']['MealType']['@attributes']['text'];
		}
		return $meal_types;
	}
	public function get_RoomTypes() {
		$room_types=array();
		foreach ($this->roomsdata['Booking']['HotelBooking'] as $hotelbooking_entry) {
			$room_types[]=$hotelbooking_entry['Room']['RoomType']['@attributes']['text'];
		}
		return $room_types;
	}

	public function get_add_infos() {
		$add_infos=array();
		foreach ($this->roomsdata['Booking']['HotelBooking'] as $hotelbooking_entry) {
			$room_infos=array();
			if (array_key_exists('Message',$hotelbooking_entry['Room']['Messages'])) {
				foreach ( $hotelbooking_entry['Room']['Messages']['Message'] as $message_entry ) {
					if ( array_key_exists( 'Type', $message_entry ) ) {
						if ( ! empty( $message_entry['Type'] ) and ! ctype_space( $message_entry['Type'] ) ) {
							$type = esc_html( $message_entry['Type'] );
						}
					}
					if ( array_key_exists( 'Text', $message_entry ) ) {
						if ( ! empty( $message_entry['Text'] ) and ! ctype_space( $message_entry['Text'] ) ) {
							$text = esc_html( $message_entry['Text'] );
						}
					}

					$room_infos[] = ( isset( $type ) ? $type : '' ) . ( ( isset( $type ) and isset( $text ) ) ? ': ' : '' ) . ( isset( $text ) ? $text : '' );

				}
			}

			if ( empty( $room_infos ) ) {
				$room_infos[] = '-';
			}
			$imploded = implode( '<br/>', $room_infos );
			$add_infos[] = html_entity_decode($imploded);
		}

		return $add_infos;
	}


	public static function create_pair_from_request( $request, $api) {

		$pair = new RequestResponsePairBookingCreate( $request, $api);
		self::$current_pair = $pair;
		$_SESSION[ self::$key ] = $pair;

		return $pair;

	}



	function get_raw_price() {
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