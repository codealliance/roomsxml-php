<?php

/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 15/09/17
 * Time: 16:10
 */

function loadFrameworkClass($name) {
	require_once( __DIR__ . "/../roomsXML/" . $name . ".php");
}

spl_autoload_register("loadFrameworkClass");

$r = new RoomsXML();
$r->set_currency( 'EUR' );
$r->set_dev_url("http://www.roomsxmldemo.com/RXLStagingServices/ASMX/XmlService.asmx");
$r->set_running_mode( RoomsXML::$mode_dev );
$r->set_org( 'sps' );
$r->set_user( 'xmltest' );
$r->set_password( 'xmltest' );
$r->set_api_ver( '1.25' );
$r->set_language( 'en' );
$r->set_soap_action_avail_url("http://www.reservwire.com/namespace/WebServices/Xml/AvailabilitySearch");
$r->set_soap_action_book_url("http://www.reservwire.com/namespace/WebServices/Xml/BookingCreate");
$r->set_soap_action_bookcancel("http://www.reservwire.com/namespace/WebServices/Xml/BookingCancel");

$urlaub=new Vacation(1,1,2,'11/01/2019', '11/06/2019',array(12,13));
$obj = $r->availability_search_region( 52612, $urlaub, RoomsXML::$return_type_obj );

$ids=$obj->get_result_ids() ;
print_r( $obj->get_result_ids() );
$quote=0;
if ($ids) {
	$obj2 = $r->booking_prepare( $ids[0], $urlaub, RoomsXML::$return_type_obj );
	$attempts=0;
	foreach ($ids as $id) {
		echo "attempts: $attempts";
		$attempts++;
		$obj2 = $r->booking_prepare( $id, $urlaub, RoomsXML::$return_type_obj );
		if ( ! $obj2->has_failed() ) {
			$quote = $id;
			break;
		}
	}
	echo "booking prepare successful";
	$obj3=$r->booking_confirm( $quote, $urlaub, $obj );
	print_r( $obj3 );
}



