<?php
/*
	Student ID: 4974948
	Student Name: Mohammad Khan
*/
session_start();

//turning off the default error reporting of php
error_reporting(0);

$auction_xml_file = "../../data/auction.xml";

$res = array();

//action get auction items
if(isset($_GET['action']) && $_GET['action'] == 'get_auction_items')
{
	//check if auction file exist, if not create one
    if(!file_exists($auction_xml_file))
    {
        $fp = fopen($auction_xml_file, 'w+');
        fwrite($fp, utf8_decode('<?xml version="1.0" encoding="UTF-8"?><listings></listings>'));
        fclose($fp);
        chmod($auction_xml_file, 0777); //setting all read/write permissions for auction xml file to true
    } 
    
    $xml_doc = new DOMDocument;
    $xml_doc->preserveWhiteSpace = false;
    $xml_doc->Load($auction_xml_file);
    $xpath = new DOMXPath($xml_doc);

    $listing_items = $xpath->query("//listings/listing");
    $auction_items = array();
	
    if($listing_items->length > 0)
    {
		//retrieving all the listed item for bidding
        for($i=0; $i<$listing_items->length; $i++)
        {
            $auction_items[$i]['item_no'] = $xpath->query("//listings/listing[".($i+1)."]/item_no")->item(0)->nodeValue;
            $auction_items[$i]['customer_id'] = $xpath->query("//listings/listing[".($i+1)."]/customer_id")->item(0)->nodeValue;
            $auction_items[$i]['name'] = $xpath->query("//listings/listing[".($i+1)."]/name")->item(0)->nodeValue;
            $auction_items[$i]['category'] = $xpath->query("//listings/listing[".($i+1)."]/category")->item(0)->nodeValue;
            $auction_items[$i]['description'] = $xpath->query("//listings/listing[".($i+1)."]/description")->item(0)->nodeValue;
            $auction_items[$i]['start_price'] = $xpath->query("//listings/start_price")->item(0)->nodeValue;
            $auction_items[$i]['buy_now_price'] = $xpath->query("//listings/listing[".($i+1)."]/buy_now_price")->item(0)->nodeValue;
            $auction_items[$i]['reserve_price'] = $xpath->query("//listings/listing[".($i+1)."]/reserve_price")->item(0)->nodeValue;
            $auction_items[$i]['created_datetime'] = $xpath->query("//listings/listing[".($i+1)."]/created_datetime")->item(0)->nodeValue;
            $auction_items[$i]['end_datetime'] = $xpath->query("//listings/listing[".($i+1)."]/end_datetime")->item(0)->nodeValue;
            $auction_items[$i]['status'] = $xpath->query("//listings/listing[".($i+1)."]/status")->item(0)->nodeValue;
            $auction_items[$i]['bidder_id'] = $xpath->query("//listings/listing[".($i+1)."]/bidder_id")->item(0)->nodeValue;
            $auction_items[$i]['current_bid_price'] = $xpath->query("//listings/listing[".($i+1)."]/current_bid_price")->item(0)->nodeValue;
            
            $end_dt_time = new DateTime(date('Y-m-d H:i:s', strtotime($auction_items[$i]['end_datetime'])));
            $curr_dt_time = new DateTime(date('Y-m-d H:i:s'));

			//finding time remaining for bidding
            $interval = date_diff($end_dt_time, $curr_dt_time);

            $auction_items[$i]['remaining_datetime'] = $interval->format('%a days %h hours %i minutes and %s seconds remaining');
            if($auction_items[$i]['status'] == "in progress" && $end_dt_time < $curr_dt_time)
            {
                $auction_items[$i]['status'] = "expired";
            }
        }
    }
    
	//Arranging retrieved auction items in a table
	if(count($auction_items) > 0)
	{
		$res = "";
		foreach($auction_items as $listing)
		{
			$res .= '<div class="auction_items">';
			$res .= '<table>';
			$res .= '<tr><td>Item No. : </td><td>'.$listing['item_no'].'</td></tr>';
			$res .= '<tr><td>Name : </td><td>'.$listing['name'].'</td></tr>';
			$res .= '<tr><td>Category : </td><td>'.$listing['category'].'</td></tr>';
			$res .= '<tr><td>Description : </td><td>'.substr($listing['description'],0,29).'...</td></tr>';
			$res .= '<tr><td>Buy it now price : </td><td>'.$listing['buy_now_price'].'</td></tr>';
			$res .= '<tr><td>Bid price : </td><td>'.$listing['current_bid_price'].'</td></tr>';
			if($listing['status'] == "in progress")
			{
				$res .= '<tr><td></td><td>'.$listing['remaining_datetime'].'</td></tr>';
				$res .= '<tr><td></td><td>'
					. '<input type="button" value="Place Bid" onclick="bid_for_item(&quot;'.$listing['item_no'].'&quot;,&quot;'.$listing['current_bid_price'].'&quot;)"/>'
					. '<input type="button" value="Buy it now" onclick="buy_auction_item(&quot;'.$listing['item_no'].'&quot;,&quot;'.$listing['buy_now_price'].'&quot;)"/>'
					. '</td></tr>';
			}
			$res .= '</table></div><br>';
			}
			echo $res;
		}
		else
		{
			echo "There are no auction items listed currently !";
		}
	} 

	
//processing a placed bid
if(isset($_GET["action"]) && $_GET["action"] == "place_bid")
{
	$item_id = $_GET["item_id"];
	$new_bid_price = $_GET["new_bid_price"];
		
	$xml_doc = simplexml_load_file($auction_xml_file);
	foreach( $xml_doc->xpath("//listings/listing[item_no='".$item_id."']") as $t ) {
		$t->current_bid_price = $new_bid_price;
		$t->bidder_id = $_SESSION["id"];
	}
	$xml_doc->asXML($auction_xml_file);
	echo "Your bid is successfully recorded, Thank you!";
}

//processing a buy it now
if(isset($_GET["action"]) && $_GET["action"] == "buy_auction_item")
{
    $item_id = $_GET["item_id"];
    $item_buy_price = $_GET["item_buy_price"];
    
    $xml_doc = simplexml_load_file($auction_xml_file);
    foreach( $xml_doc->xpath("//listings/listing[item_no='".$item_id."']") as $t ) {
        $t->current_bid_price = $item_buy_price;
        $t->bidder_id = $_SESSION['id'];
        $t->status = "sold";
    }
    $xml_doc->asXML($auction_xml_file);
    echo "Successfully purchased, Thank you!.";
}
?>