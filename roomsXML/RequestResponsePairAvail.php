<?php

/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 7/5/17
 * Time: 6:53 PM
 */
class RequestResponsePairAvail extends RequestResponsePair {
	public static $key = 'request_response_pair_avail';
	public static $current_pair;

	private $result_ids;
	private $acc_ids;
	private $roomsdata;

	/**
	 * @return mixed
	 */
	public function get_roomsdata() {
		return $this->roomsdata;
	}


	public function get_result_ids() {
		return $this->result_ids;
	}
	public function get_acc_ids() {
		return $this->acc_ids;
	}



	private function get_result_ids_from_roomsdata() {
		$result_ids=array();
		foreach ($this->roomsdata as $availability_entry) {
			foreach ($availability_entry['Result'] as $result_entry) {
				$result_ids[]=$result_entry['@attributes']['id'];
			}
		}
		return $result_ids;

	}

	private function get_acc_ids_from_roomsdata() {
		$acc_ids=array();
		foreach ( $this->roomsdata as $availability_entry ) {
			$acc_ids[] = $availability_entry['Hotel']['@attributes']['id'];
		}
		return $acc_ids;
	}

	/**
	 * RequestResponsePairAvail constructor.
	 *
	 * @param $request
	 * @param $api API
	 */
	private function __construct($request, $api) {
		$this->response = $api->sendCurlRequest( $request, $api->constants->soap_action_avail );
		$this->request = $request;
		$this->roomsdata = $api->getArrayFromXmlResponseAvail( $this->response );
		$this->result_ids = RequestResponsePairAvail::get_result_ids_from_roomsdata();
		$this->acc_ids=RequestResponsePairAvail::get_acc_ids_from_roomsdata();

	}


	public static function create_pair_from_request($request, $api) {

		$current_pair = new RequestResponsePairAvail( $request, $api );
		$_SESSION [ RequestResponsePairAvail::$key ] = $current_pair;

		RequestResponsePairAvail::$current_pair = $current_pair;
		return $current_pair;
	}


	function get_result_from_id( $result_id ) {
		foreach ( $this->roomsdata as $availability_entry) {
			foreach ( $availability_entry['Result'] as $result_entry ) {
				if ( $result_entry['@attributes']['id'] == $result_id ) {
					return $result_entry;

				}
			}

		}
	}

	function get_result_names_and_meals( $result_id ) {
		$result = $this->get_result_from_id( $result_id);
		if ( is_null( $result ) ) {
			return array();
		}
		$names      = [];

		foreach ( $result['Room'] as $room_entry ) { //result has multiple rooms

			$names[]= array('name' => $room_entry['RoomType']['@attributes']['text'],'meal' => $room_entry['MealType']['@attributes']['text']);

		}

		return $names;

	}


	function get_result_price( $room_type_id ) {
		$result = $this->get_result_from_id( $room_type_id );
		if ( is_null( $result ) ) {
			return null;
		}

		$sum = 0;
		foreach ( $result['Room'] as $room_entry ) {

			$sum += $room_entry['Price']['@attributes']['amt'];
		}
		$room_price = $sum;


		return $room_price;

	}


		}

