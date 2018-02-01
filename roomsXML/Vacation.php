<?php
/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 13/09/17
 * Time: 17:04
 */

class Vacation {

	public $rooms;
	public $adults;
	public $kids;
	public $startdate;
	public $enddate;
	public $child_ages;
	public $adult_names;
	public $child_names;
	public $minprice;
	public $maxprice;


	function __construct($rooms, $adults, $kids, $startdate, $enddate, $child_ages=array(), $adult_names=array(), $child_names=array(), $minprice=0, $maxprice=null) {
		$this->rooms = $rooms;
		$this->adults = $adults;
		$this->kids = $kids;
		$this->startdate = $startdate;
		$this->enddate = $enddate;
		$this->child_ages=$child_ages;
		$this->adult_names=$adult_names;
		$this->child_names=$child_names;
		$this->minprice=$minprice;
		$this->maxprice = $maxprice==null ? API::$no_max_price : $maxprice;
	}


}