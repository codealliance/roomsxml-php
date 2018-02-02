<?php
/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 13/09/17
 * Time: 16:45
 */

//this is the user interface for the roomsxml framework

class RoomsXML {
	public $constants;
	public $api;
	public static $return_type_array='array';
	public static $return_type_obj = 'obj';
	public static $mode_dev='dev';
	public static $mode_live='live';

	function set_running_mode($mode) {
		$this->constants->running_mode=$mode;
	}

	function set_language($language) {
		$this->constants->language=$language;
	}
	function set_api_ver($ver) {
		$this->constants->api_ver = $ver;
	}

	function set_input_date_format($format) {
		$this->constants->input_date_format = $format;
	}

	function __construct() {
		$this->constants=new Constants();
		$this->api=new API($this->constants);
	}

	function set_currency($currency) {
		$this->constants->currency = $currency;
	}

	function set_live_url($url) {
		$this->constants->url_api_live=$url;
	}
	function set_dev_url($url) {
		$this->constants->url_api_dev=$url;
	}
	function set_org($org) {
		$this->constants->org_id = $org;
	}
	function set_user($user) {
		$this->constants->user = $user;
	}
	function set_password($pw) {
		$this->constants->pw = $pw;
	}

	function set_soap_action_avail_url($url) {
		$this->constants->soap_action_avail = $url;
	}

	function set_soap_action_book_url($url) {
		$this->constants->soap_action_book = $url;

	}

	function set_soap_action_bookcancel($url) {
		$this->constants->soap_action_bookcancel = $url;
	}

	function availability_search_hotel($hotel_id, $vacation, $return_type) {

		$xml=$this->api->getRoomsXmlSection($vacation);
		$request=$this->api->getRequestXml($xml, $hotel_id, $vacation, true);
		$pair = RequestResponsePairAvail::create_pair_from_request( $request, $this->api );
		if ($return_type==RoomsXML::$return_type_array) {
			return $pair->get_roomsdata();
		} else {
			return $pair;
		}
	}

	/* @param $vacation Vacation
	 * @param $region_id int

	 */
	function availability_search_region($region_id, $vacation, $return_type) {

		$xml=$this->api->getRoomsXmlSection($vacation);

		$request=$this->api->getRequestXml($xml, $region_id, $vacation, false);
		$pair = RequestResponsePairAvail::create_pair_from_request( $request, $this->api);
		if ($return_type==RoomsXML::$return_type_array) {
			return $pair->get_roomsdata();
		} else {
			return $pair;
		}

	}

	function booking_prepare($quote_id, $vacation, $return_type) {
		$xml=$this->api->getRoomsXmlSection($vacation);
		$request=$this->api->getBookingXML($xml, $quote_id, true);
		$pair = RequestResponsePairBookingCreate::create_pair_from_request( $request, $this->api );
		if ($return_type=='Array') {
			return $pair->get_roomsdata();
		} else {
			return $pair;
		}
	}

	function booking_confirm($quote_id, $vacation, $return_type) {
		$xml=$this->api->getRoomsXmlSection($vacation);
		$request=$this->api->getBookingXML($xml, $quote_id, false);
		$pair = RequestResponsePairBookingCreate::create_pair_from_request( $request, $this->api );
		if ($return_type=='Array') {
			return $pair->get_roomsdata();
		} else {
			return $pair;
		}
	}

	function booking_cancel($booking_id) {
		$request = $this->api->getBookingXmlCancel($booking_id );
		$pair = RequestResponsePairBookingCancel::create_pair_from_request( $request, $this->api );
		return $pair;

	}


}