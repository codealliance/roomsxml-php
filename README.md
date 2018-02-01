This is a framework for communication between your php website and the roomsXML service API.

Hint: Read the roomsXML API specification / FAQ before using this framework. You need
to know what a region id is, what a booking prepare request is and so on.


#### \#0 How to use:
- create an instance of the RoomsXML class. Use the methods to set your options.
    you can/must specify urls, input date format, running mode (live/dev) and many more (see [\# 1](#1-rooms-xml-options)).
    This is probably only necessary once per execution of your program. You stick to this roomsXML object
    all the time.

- once you have set up your RoomsXML object, you can almost start to interact with the service.
    Follow the instructions below every time you want to perform the request.

- create an instance of the Vacation class. here you specify all information regarding your query (see [\# 2](#2-how-to-start-a-vacation)).
    you can/must specify number of adults, number of kids, start and end date, etc.

- now perform the request towards roomsXML using the methods provided by the RoomsXML class (see [\# 3](#3-roomsxml-requests)).
     These are for example: availability_search_region, availability_search_hotel, booking_prepare,...
     
#### \#1 Rooms XML options

<table>
  <tr>
    <th>Option</th>
    <th>Explanation</th>
    <th>Method</th>
    <th>Parameters</th>
  </tr>
  <tr>
    <td>Running Mode</td>
    <td>If in development mode, <br>the framework will use the dev URLs,<br>Live mode will use the live URLs.</td>
    <td>set_running_mode($mode)</td>
    <td>String: 'dev' or 'live'</td>
  </tr>
  <tr>
    <td>Language</td>
    <td>The language you want to speak <br>with RoomsXML. <br>Accepts an ISO language identifier</td>
    <td>set_language($language)</td>
    <td>String:  'en', 'fr', etc.</td>
  </tr>
  <tr>
    <td>Api Version</td>
    <td>The version of the RoomsXML API</td>
    <td>set_api_ver($ver)</td>
    <td>String: e.g. '1.25'</td>
  </tr>
  <tr>
    <td>Input Date Format</td>
    <td>The date format you want to use<br>when you set up a new vacation.<br>Default is 'mm/dd/yyyy'</td>
    <td>set_input_date_format($format)</td>
    <td>String:<br>Allowed delimiters:<br>'-' and '/'.<br>Day: 'dd', Month: 'mm'<br>Year: 'yyyy'. 'yy' doesnt<br>work.</td>
  </tr>
  <tr>
    <td>Currency</td>
    <td>The currency for the return prices.<br>Accepts an ISO-4217 / SWIFT <br>three character currency code<br>(e.g. USD, GBP,EUR).</td>
    <td>set_currency($currency)</td>
    <td>String: e.g. 'EUR'</td>
  </tr>
  <tr>
    <td>Live URL</td>
    <td>The Live URL</td>
    <td>set_live_url($url)</td>
    <td>String</td>
  </tr>
  <tr>
    <td>Dev URL</td>
    <td>The Dev URL</td>
    <td>set_dev_url($url)</td>
    <td>String</td>
  </tr>
  <tr>
    <td>Organisation</td>
    <td>The organisation name for the<br>log-in at RoomsXML</td>
    <td>set_org($org)</td>
    <td>String</td>
  </tr>
  <tr>
    <td>User</td>
    <td>Your username for log-in</td>
    <td>set_user($user)</td>
    <td>String</td>
  </tr>
  <tr>
    <td>Password</td>
    <td>Your Password</td>
    <td>set_password($pw)</td>
    <td>String</td>
  </tr>
  <tr>
    <td>Soap Action Availability<br>Search URL</td>
    <td>The soap URL for availability<br>search</td>
    <td>set_soap_action_avail_url($url)</td>
    <td>String</td>
  </tr>
  <tr>
    <td>Soap Action Booking URL</td>
    <td>The soap URL for booking</td>
    <td>set_soap_action_book_url($url)</td>
    <td>String</td>
  </tr>
  <tr>
    <td>Soap Action Cancel URL</td>
    <td>The soap URL for cancelling a<br>booking.</td>
    <td>set_soap_action_bookcancel($url)</td>
    <td>String</td>
  </tr>
</table>

#### \#2 How to start a vacation

Parameters for instantiation of a vacation object:

**Please be aware that this framework is not capable of performing multi-room
requests with different configurations
(e.g. 1st room with 2 adults, 2. room with 1 adult and 1 child)

If you specify a vacation with 2 rooms, 1 adult and 2 kids, that 
means you want two rooms with 1 adult and 2 kids each.**

<table>
  <tr>
    <th>Parameter</th>
    <th>Explanation</th>
    <th>Type</th>
    <th>Example<br></th>
  </tr>
  <tr>
    <td>$rooms</td>
    <td>Number of rooms<br></td>
    <td>int</td>
    <td>1<br></td>
  </tr>
  <tr>
    <td>$adults</td>
    <td>Number of adults<br></td>
    <td>int</td>
    <td>2<br></td>
  </tr>
  <tr>
    <td>$kids</td>
    <td>Number of kids<br></td>
    <td>int</td>
    <td>0<br></td>
  </tr>
  <tr>
    <td>$startdate</td>
    <td>Startdate in your<br>specified input format<br></td>
    <td>String</td>
    <td>'05/23/2018'<br></td>
  </tr>
  <tr>
    <td>$enddate</td>
    <td>Enddate in your <br>specified input format<br></td>
    <td>String</td>
    <td>'05/30/2018'</td>
  </tr>
  <tr>
    <td>$child_ages (optional)<br></td>
    <td>Ages of the children<br></td>
    <td>Array</td>
    <td>array(4,5)</td>
  </tr>
  <tr>
    <td>$adult_names<br>(somewhat optional)</td>
    <td>The names of the adults<br>on the vacation.<br>An array of arrays.<br>Each contains title, <br>first and last name<br>(in this order).<br>HINT: for booking<br>requests you need<br>to give a vacation<br>with at least one<br>adult name.<br></td>
    <td>Multi-dim <br>Array</td>
    <td>array(array('Mr.','Ron','Weasley'),<br>array('Mr.','Harry','Potter'))<br></td>
  </tr>
  <tr>
    <td>$child_names(optional)</td>
    <td>Child Names.<br>Data structure like <br>adult names above.<br></td>
    <td>Multi-dim<br>Array</td>
    <td>array(array('Ms.'Childy','Child')<br></td>
  </tr>
  <tr>
    <td>$minprice(optional)</td>
    <td>Minimum price in<br>specified currency.<br></td>
    <td>int</td>
    <td>200<br></td>
  </tr>
  <tr>
    <td>$maxprice(optional)</td>
    <td>Maximum price in<br>specified currency<br></td>
    <td>int</td>
    <td>1000</td>
  </tr>
</table>

#### \#3 RoomsXML requests

Finally you want to send your requests t0 the
roomsXML service API!

You can do this using the methods below.

<table>
  <tr>
    <th>Method</th>
    <th>Explanation</th>
    <th>Parameters</th>
  </tr>
  <tr>
    <td>availability_search_hotel($hotel_id, $vacation, $return_type)</td>
    <td>Availability search for a specific hotel by ID.<br>If the return type is 'array' you get an array<br>with all relevant hotel information.<br>If the return type is 'obj' you get an<br>object that also contains the requests and<br>the responses as string. </td>
    <td>$hotel_id: int<br>$vacation: Vacation<br>$return_type: String<br> 'array'/'obj'</td>
  </tr>
  <tr>
    <td>availability_search_region($region_id,$vacation,$return_type)</td>
    <td>Like above but search for all hotels in a<br>specific region. You get a list with all region IDs<br>from roomsXML.</td>
    <td>$region_id: int<br>$vacation: Vacation<br>$return_type: String<br>'array'/'obj'</td>
  </tr>
  <tr>
    <td>booking_prepare($quote_id,$vacation,$return_type)</td>
    <td>Performs a booking request in prepare <br>mode with the quote_id from an availability search.<br>You need to do this twice before you can do <br>an actual booking at roomsXML. Return<br>type like availability_search_hotel.</td>
    <td>$quote_id: int<br>$vacation: Vacation<br>$return_type: String<br>'array'/'obj'</td>
  </tr>
  <tr>
    <td>booking_confirm($quote_id, $vacation, $return_type)</td>
    <td>Performs a real booking request with a quote_id<br>from an availability search. Note that 1. you have<br>to run booking prepare twice before and<br>2. you are not allowed to give a vacation without<br>at least one adult name!</td>
    <td>$quote_id: int<br>$vacation: Vacation<br>$return_type: String<br>'array'/'obj'</td>
  </tr>
  <tr>
    <td>booking_cancel($booking_id)</td>
    <td>Cancels the booking with the given booking id.</td>
    <td>$booking_id: int</td>
  </tr>
</table>





