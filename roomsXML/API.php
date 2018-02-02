<?php
/**
 * Created by PhpStorm.
 * User: ullika
 * Date: 5/12/17
 * Time: 3:40 PM
 */


class API {
	public static $no_max_price = 'no_max';
	public $constants;

	/**
	 * API constructor.
	 *
	 * @param $constants Constants
	 */
	public function __construct( $constants ) {
		$this->constants = $constants;
	}


	function sendCurlRequest($xml, $soapaction ) {
		$url_dev=$this->constants->url_api_dev;
		$url_live = $this->constants->url_api_live;



		$mode=$this->constants->running_mode;
		// SET REQ URL
		if ( $mode==RoomsXML::$mode_dev ) {
			$url = $this->constants->url_api_dev;
		} elseif ($mode==RoomsXML::$mode_live) {
			$url = $this->constants->url_api_live;
		} else {
			die("Error: please specify a running mode ('dev'/'live') \n");
		}



		// SET SOAP ACTION
		// SET HEADER
		$header = array(
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: \"" . $soapaction . "\"",
			"Content-length: " . strlen( $xml ),
		);

		// DO SOAP REQUEST
		$soap_do = curl_init();
		curl_setopt( $soap_do, CURLOPT_URL, $url );
		curl_setopt( $soap_do, CURLOPT_CONNECTTIMEOUT, 1000 );
		curl_setopt( $soap_do, CURLOPT_TIMEOUT, 1000 );
		curl_setopt( $soap_do, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $soap_do, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $soap_do, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt( $soap_do, CURLOPT_POST, true );
		curl_setopt( $soap_do, CURLOPT_POSTFIELDS, $xml );
		curl_setopt( $soap_do, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $soap_do, CURLOPT_ENCODING, 'gzip,deflate' );
		$response = curl_exec( $soap_do );
		if ( $response === false ) {
			$err = 'Curl error: ' . curl_error( $soap_do );
			curl_close( $soap_do );
			print $err;
		} else {
			curl_close( $soap_do );
		}

		return $response;
	}

	/**
	 * @param $vacation Vacation
	 *
	 * @return null|string
	 */
	public function getRoomsXmlSection( $vacation ) {
		$rooms_xml = null;
		for ( $i = 0; $i < $vacation->rooms; $i ++ ) {
			$rooms_xml .= "<Room>
				      <Guests>";

			for ( $j = 0; $j < $vacation->adults; $j ++ ) {
				$adult_names=$vacation->adult_names;

				if ( $i==0 && array_key_exists( $j,$adult_names ) ) {
					$rooms_xml .= '<Adult title="' . $adult_names[ $j ][0] . '" first="' . $adult_names[ $j ][1] . '" last="' . $adult_names[ $j ][2] . '"></Adult>';
				} else {

					$rooms_xml .= '<Adult title="Mrs." first="test' . str_repeat('R',$i).str_repeat('I',$j) . '" last="testadult" />';
				}
			}
			if ( $vacation->kids ) {
				$child_ages = $vacation->child_ages;
				$child_names = $vacation->child_names;
				$k = 0;

				foreach ( $child_ages as $z => $c ) {

					if ($i==0 && array_key_exists( $k,$child_names ) ) {
						$rooms_xml .= '<Child age="' . $c . '" title="' . $child_names[ $k ][0] . '" first="' . $child_names[ $k ][1] . '" last="' . $child_names[ $k ][2] . '"/>';
					} else {
						$rooms_xml .= '<Child age="' . $c . '" title="Ms." first="test' .str_repeat('R',$i). str_repeat('I',$z) . '" last="testchild"/>';

					}

					$k ++;

				}
			}

			$rooms_xml .= "</Guests>
				    </Room>";
		}

		return $rooms_xml;
	}

	/**
	 * @param $rooms_xml string
	 * @param $id
	 * @param $vacation Vacation
	 * @param bool $is_acc_id
	 *
	 * @return string
	 */
	function getRequestXml( $rooms_xml, $id, $vacation, $is_acc_id = false ) {
		$num_days = $this->getNumDays( $vacation->startdate, $vacation->enddate );
		$formatedStart = $this->convertDate( $vacation->startdate );
		$min_price = $vacation->minprice;
		$max_price = $vacation->maxprice;

		$xml = '<?xml version="1.0"?>
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
			        <soap:Body>
			        <AvailabilitySearch xmlns="http://www.reservwire.com/namespace/WebServices/Xml">
			         <xiRequest>
					  <Authority>
					    <Org>' . $this->constants->org_id . '</Org>
					    <User>' . $this->constants->user . '</User>
					    <Password>' . $this->constants->pw . '</Password>
					    <Currency>' . strtoupper( $this->constants->currency ) . '</Currency>
					    <Language>' . $this->constants->language . '</Language>
					    <Version>' . $this->constants->api_ver . '</Version>
					  </Authority>';
		if ( ! $is_acc_id ) {
			$xml = $xml . ' <RegionId>' . $id . '</RegionId>';
		} else {
			$xml = $xml . ' <HotelId>' . $id . '</HotelId>';
		}
		$xml = $xml . '
					  <HotelStayDetails>
					    <ArrivalDate>' . $formatedStart . '</ArrivalDate>
					    <Nights>' . $num_days . '</Nights>
					    <Nationality>'.$vacation->nationality.'</Nationality>
					    ' . $rooms_xml . '
					  </HotelStayDetails>
					  <HotelSearchCriteria>
					    <AvailabilityStatus>allocation</AvailabilityStatus>
					    <DetailLevel>basic</DetailLevel>';
		if ( $min_price != 0 ) {
			$xml = $xml . '<MinPrice>' . $min_price . '</MinPrice>';
		}
		if ( $max_price != API::$no_max_price ) {
			$xml = $xml . '<MaxPrice>' . $max_price . '</MaxPrice>';
		}


		$xml = $xml . '</HotelSearchCriteria>
					  </xiRequest>
					</AvailabilitySearch>
				  </soap:Body>
			</soap:Envelope>';

		return $xml;
	}


	/**
	 * @param $roomsxml
	 * @param $result_id
	 * @param $vacation Vacation
	 * @param bool $dry_run
	 *
	 * @return string
	 */
	function getBookingXML( $roomsxml, $result_id, $dry_run = true ) {

		$mode = ( $dry_run ) ? 'prepare' : 'confirm';

		$xml = '<?xml version="1.0"?>

<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
  <soap:Body>
    <BookingCreate xmlns="http://www.reservwire.com/namespace/WebServices/Xml">
      <xiRequest>
		<Authority>
		<Org>' . $this->constants->org_id . '</Org>
		<User>' . $this->constants->user . '</User>
		<Password>' . $this->constants->pw . '</Password>
		<Currency>' . strtoupper( $this->constants->currency ) . '</Currency>
		<Language>' . $this->constants->language . '</Language>
		<Version>' . $this->constants->api_ver . '</Version>
		</Authority>
        <QuoteId>' . $result_id . '</QuoteId>
        <HotelStayDetails>
          <ArrivalDate>' . '0001-01-01' . '</ArrivalDate>
          <Nights>' . 0 . '</Nights>
          ' . $roomsxml . '
        </HotelStayDetails>
        <CommitLevel>' . $mode . '</CommitLevel>
      </xiRequest>
    </BookingCreate>
  </soap:Body>
</soap:Envelope>';


		return $xml;
	}


	function getNumDays( $startdate, $enddate ) {

		$dStart = new DateTime( $this->convertDate( $startdate ) );
		$dEnd   = new DateTime( $this->convertDate( $enddate ) );

		$dDiff    = $dStart->diff( $dEnd );
		$num_days = $dDiff->days;

		return $num_days;

	}

	function getBookingXmlCancel( $bookingId ) {
		$xml = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
				  <soap:Body>
					<BookingCancel xmlns="http://www.reservwire.com/namespace/WebServices/Xml">
					  <xiRequest>
						<Authority>
						  	<Org>' . $this->constants->org_id . '</Org>
							<User>' . $this->constants->user . '</User>
							<Password>' . $this->constants->pw . '</Password>
							<Currency>' . strtoupper( $this->constants->currency ) . '</Currency>
							<Language>' . $this->constants->language . '</Language>
							<Version>' . $this->constants->api_ver . '</Version>
						</Authority>
						<BookingId>' . $bookingId . '</BookingId>
						<CommitLevel>confirm</CommitLevel>
					  </xiRequest>
					</BookingCancel>
				  </soap:Body>
				</soap:Envelope>';

		return $xml;
	}

	function convertDate( $date ) {
		//converts a date from the custom format into standart
		$formatstring = $this->constants->input_date_format;
		$delimiter    = strpos( $date, "/" ) !== false ? "/" : "-";
		$x            = explode( $delimiter, $date );
		$y            = explode( $delimiter, $formatstring );

		$dpos = array_search( 'dd', $y );
		$day  = $x[ $dpos ];

		$mpos  = array_search( 'mm', $y );
		$month = $x[ $mpos ];

		$ypos = array_search( 'yyyy', $y );
		if ($ypos==false) {
			$ypos = array_search( 'yy', $y );
		}
		$year = $x[ $ypos ];

		return $year . "-" . $month . "-" . $day;
	}

	function getArrayFromXmlResponseAvail( $response ) {
		$availability = array();
		// GET XML RESPONSE
		$soap = simplexml_load_string( $response );
		$soap->registerXPathNamespace( "soap", "http://www.w3.org/2003/05/soap-envelope" );
		$data = $soap->xpath( '//soap:Body' );

		// CONVERT OBJECT TO ARRAY
		$array = json_decode( json_encode( (array) $data ), 1 );

		// GET AVAILABILITY FROM XML RESPONSE
		if ( isset( $array[0]['AvailabilitySearchResponse']['AvailabilitySearchResult']['HotelAvailability'] ) ) {
			$availability = $array[0]['AvailabilitySearchResponse']['AvailabilitySearchResult']['HotelAvailability'];
		}

		if ( array_key_exists('Hotel',$availability)) {
			$availability = array( '0' => $availability );
		}

		foreach ( $availability as &$availability_entry ) {
			$result = $availability_entry['Result'];
			if ( array_key_exists('@attributes',$result) ) {
				$availability_entry['Result'] = array( '0' => $result );
			}
			foreach ( $availability_entry['Result'] as &$result_entry ) {
				$room = $result_entry['Room'];

				if ( array_key_exists('Price',$room) ) {
					$result_entry['Room'] = array( '0' => $room );
				}
			}
		}

		return $availability;
	}

	function getArrayFromXmlResponseBooking( $response ) {
		// GET XML RESPONSE
		$soap = simplexml_load_string( $response );
		$soap->registerXPathNamespace( "soap", "http://www.w3.org/2003/05/soap-envelope" );
		$data = $soap->xpath( '//soap:Body' );
		// CONVERT OBJECT TO ARRAY
		$array = json_decode( json_encode( (array) $data ), 1 );

		// GET AVAILABILITY FROM XML RESPONSE
		$booking = array();
		if ( isset( $array[0]['BookingCreateResponse']['BookingCreateResult'] ) ) {
			$booking = $array[0]['BookingCreateResponse']['BookingCreateResult'];
		} else {
			return $array[0];
		}

		if ( array_key_exists('Room',$booking['Booking']['HotelBooking']) ) {
			$booking['Booking']['HotelBooking'] = array( '0' => $booking['Booking']['HotelBooking'] );
		}

		foreach ( $booking['Booking']['HotelBooking'] as &$hotelbooking_entry ) {

			$room_entry = &$hotelbooking_entry['Room'];

			if ( array_key_exists('SellingPrice', $room_entry['NightCost']) ) {

				$room_entry['NightCost'] = array( '0' => $room_entry['NightCost'] );

			}
			if ( array_key_exists('Message',$room_entry['Messages']) && array_key_exists('Type',$room_entry['Messages']['Message']) ) {
				$room_entry['Messages']['Message'] = array( '0' => $room_entry['Messages']['Message'] );
			}
			if ( array_key_exists('Amount',$room_entry['CanxFees']['Fee'])) {
				$room_entry['CanxFees']['Fee'] = array( '0' => $room_entry['CanxFees']['Fee'] );
			}
		}


		//to do: make the structure of the array independent of the no. of nights, messages and fees.

		return $booking;
	}

}