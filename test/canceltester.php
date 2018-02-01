<?php
/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 18/09/17
 * Time: 15:20
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
$res=$r->booking_cancel(720107509);
print_r( $res );