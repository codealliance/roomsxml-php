<?php

class Constants {
	public $url_api_dev;
	public $url_api_live;
	public $soap_action_avail;
	public $soap_action_book;
	public $soap_action_bookcancel;
	public $org_id;
	public $user;
	public $pw;
	public $currency;
	public $running_mode;
	public $language;
	public $api_ver;
	public $input_date_format; //the date format that you want to use for a new vacation


	function __construct() {

		$this->input_date_format = 'mm/dd/yyyy';
	}
}