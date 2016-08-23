<?php
/*
	StudentID: 4974948
	Student Name: Mohammad Khan
	
*/

session_start();

//turning off default error reporting of php
error_reporting(0);

$auction_xml_file = "../../data/auction.xml"; //setting path for the auction.xml file ad from data folder

$res = array();


if(isset($_POST['action']) && $_POST['action'] == 'get_categories')
{
		
	//if auction.xml file does not already exist, create one
    if(!file_exists($auction_xml_file))
    {
        $fp = fopen($auction_xml_file, 'w+');
        fwrite($fp, utf8_decode('<?xml version="1.0" encoding="UTF-8"?><listings></listings>'));
        fclose($fp);
        chmod($auction_xml_file, 0777); //Set all the read/write permissions to true
    }
    
    $xml_doc = new DOMDocument;
    $xml_doc->preserveWhiteSpace = false;
    $xml_doc->Load($auction_xml_file);
    $xpath = new DOMXPath($xml_doc);

    $list = $xpath->query("//listings/listing/category");
    $data_array = array();
    if($list->length > 0)
    {
        for($i=0; $i<$list->length; $i++)
        {
            $data = $list->item($i)->nodeValue;
            array_push($data_array,$data);
        }
        $data_array = array_unique($data_array);
    }
    
    $res['cats'] = $data_array;
    echo json_encode($res);
}



//process ajax request for listing
if(isset($_POST['action']) && $_POST['action'] == 'listing')
{

	$error = "";
	$success = "";
	
	$customer_id = $_SESSION['id'];

	//get values from the listing details form
	
	$name = trim($_POST["name"]);
	$category = trim($_POST["category"]);
	$other_category = trim($_POST["other_category"]);
	$description = trim($_POST["description"]);
	
	$start_price = trim($_POST["start_price"]);
	$buy_now_price = trim($_POST["buy_now_price"]);
	$reserve_price = trim($_POST["reserve_price"]);
	$day = trim($_POST["day"]);
	$hour = trim($_POST["hour"]);
    $minute = trim($_POST["minute"]);
	
	if(empty($name))
        {
            $error = "Please provide all details for listing an item !";
        }
        if(empty($category) || ($category == "other" && empty($other_category)))
        {
            $error = "Please provide all details for listing an item !";
        }
        if(empty($description))
        {
            $error = "Please provide all details for listing an item !";
        }
        if(empty($start_price))
        {
            $error = "Please provide all details for listing an item !";
        }
		if($start_price <=0)
        {
            $error = "Start price must be greate then 0!";
        }
        if(empty($buy_now_price))
        {
            $error = "Please provide all details for listing an item !";
        }
		if($buy_now_price <=0)
        {
            $error = "Buy now price must be greater than 0 !";
        }
        if(empty($reserve_price))
        {
            $error = "Please provide all details for listing an item !";
        }
		if($reserve_price <=0)
        {
            $error = "Reserve price must be greater than 0 !";
        }
        if($day == 0 && $hour == 0 && $minute == 0)
        {
            $error = "Please provide a reasonable gap for duration !";
        }
		if($start_price > $reserve_price)
        {
            $error = "Start price must be less than or equal to reserve price !";
        }
        if($reserve_price >= $buy_now_price)
        {
            $error = "Reserve price must be less than buy it now price !";
        }
	
			
	//Check if no error found, and execute the rest of the code
	if($error == "")
	{	
        //if auction.xml file does not exist, create one
        if(!file_exists($auction_xml_file))
        {
            $fp = fopen($auction_xml_file, 'w+');
            fwrite($fp, utf8_decode('<?xml version="1.0" encoding="UTF-8"?><listings></listings>'));
            fclose($fp);
            chmod($auction_xml_file, 0777); //set all the options for read/write to true
        }

            	
		if($category == "other")
        {
            $category = $other_category;
        }
                    
        $auction_xml_file = realpath($auction_xml_file);
        $xml_doc = new DOMDocument('1.0');
        $xml_doc->load($auction_xml_file);

        $root = $xml_doc->documentElement;
        $tag_listing = $xml_doc->createElement('listing');
        $item_no = uniqid();
        $tag_item_no = $xml_doc->createElement( "item_no" );
        $tag_item_no->appendChild($xml_doc->createTextNode( $item_no));
        $tag_listing->appendChild( $tag_item_no );
                    
        $tag_customer_id = $xml_doc->createElement( "customer_id" );
        $tag_customer_id->appendChild($xml_doc->createTextNode( $customer_id));
        $tag_listing->appendChild( $tag_customer_id );
                    
        $tag_name = $xml_doc->createElement( "name" );
        $tag_name->appendChild($xml_doc->createTextNode( $name));
        $tag_listing->appendChild( $tag_name );
                    
        $tag_category = $xml_doc->createElement( "category" );
        $tag_category->appendChild($xml_doc->createTextNode( $category));
        $tag_listing->appendChild( $tag_category );
                    
        $tag_description = $xml_doc->createElement( "description" );
        $tag_description->appendChild($xml_doc->createTextNode( $description));
        $tag_listing->appendChild( $tag_description );
                    
        $tag_start_price = $xml_doc->createElement( "start_price" );
        $tag_start_price->appendChild($xml_doc->createTextNode( $start_price));
        $tag_listing->appendChild( $tag_start_price );
                    
        $tag_buy_now_price = $xml_doc->createElement( "buy_now_price" );
        $tag_buy_now_price->appendChild($xml_doc->createTextNode( $buy_now_price));
        $tag_listing->appendChild( $tag_buy_now_price );
                    
        $tag_reserve_price = $xml_doc->createElement( "reserve_price" );
        $tag_reserve_price->appendChild($xml_doc->createTextNode( $reserve_price));
        $tag_listing->appendChild( $tag_reserve_price );
                   
        $now = date("Y-m-d H:i:s");
        $tag_created_datetime = $xml_doc->createElement( "created_datetime" );
        $tag_created_datetime->appendChild($xml_doc->createTextNode( $now));
        $tag_listing->appendChild( $tag_created_datetime );
                   
        $end_datetime = date("Y-m-d H:i:s", strtotime($now."+ ".$day." days"));
        $end_datetime = date("Y-m-d H:i:s", strtotime($end_datetime."+ ".$hour." hours"));
        $end_datetime = date("Y-m-d H:i:s", strtotime($end_datetime."+ ".$minute." minutes"));
                   
        $tag_end_datetime = $xml_doc->createElement( "end_datetime" );
        $tag_end_datetime->appendChild($xml_doc->createTextNode( $end_datetime));
        $tag_listing->appendChild( $tag_end_datetime );
                   
        $tag_status = $xml_doc->createElement( "status" );
        $tag_status->appendChild($xml_doc->createTextNode( "in progress"));
        $tag_listing->appendChild( $tag_status );
                  
        $tag_bidder_id = $xml_doc->createElement( "bidder_id" );
        $tag_bidder_id->appendChild($xml_doc->createTextNode( ""));
        $tag_listing->appendChild( $tag_bidder_id );
                   
        $tag_current_bid_price = $xml_doc->createElement( "current_bid_price" );
        $tag_current_bid_price->appendChild($xml_doc->createTextNode( $start_price));
        $tag_listing->appendChild( $tag_current_bid_price );
                    
        $root->appendChild( $tag_listing );
        $xml_doc->save($auction_xml_file);
                   
        $success = '<h4 style="color:green; font-weight:bold;">Thank you! Your item has been listed with ShopOnline. \nThe item number is '.$item_no.', and \nthe bidding starts at: '.date("H:i:s", strtotime($now)).' on '.date("Y-m-d", strtotime($now)).'. <br><a href="javascript:void(0)" onclick="window.location.href=&quot;listing.htm&quot;">List another item</a></h4>';

	}
                
if($error != "")
{
    $res['result']="fail";
    $res['message']=$error;
}
else
{
    $res['result']="succ";
    $res['message']=$success;
}
    echo json_encode($res);
}
?>