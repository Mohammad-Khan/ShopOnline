/*
	Student ID: 4974948
	Student Name: Mohammad Khan

	This is page of code processes the bids placed in bidding.htm
*/

//Get auction items at every 5 seconds
setInterval(getAuctionItems,5000);

$('document').ready(function(){
   getAuctionItems(); 
});

function getAuctionItems()
{
    var xHR = false;  
    if (window.XMLHttpRequest) {
        xHR = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xHR = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    if(xHR) {
        xHR.open("GET", "action_bidding.php?action=get_auction_items", true); 
        xHR.onreadystatechange = function() 
        { 
            if ((xHR.readyState == 4) && (xHR.status == 200)) { 
                var responseText = xHR.responseText;
                document.getElementById("div_auction_items").innerHTML = responseText;
            } 
        } 
        xHR.send(null); 
    } 
}

//process bid from the user
function bid_for_item(item_id, old_bid_price)
{
    var bid_price = parseFloat(prompt("Please enter your bid for item"));
    if(isNaN(bid_price))
    {
        alert("Please enter numbers only");
    }
    else if(bid_price <= parseFloat(old_bid_price))
    {
        alert("Invalid bid: Bid price should be greater than the previous bid");
    }
    else
    {
        var xHR = false;  
    if (window.XMLHttpRequest) {
        xHR = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xHR = new ActiveXObject("Microsoft.XMLHTTP");
    }
        
        if(xHR) {
            xHR.open("GET", "action_bidding.php?action=place_bid&item_id="+item_id+"&new_bid_price="+bid_price, true); 
            xHR.onreadystatechange = function() 
            { 
                if ((xHR.readyState == 4) && (xHR.status == 200)) { 
                    var responseText = xHR.responseText;
                    alert(responseText);
                    getAuctionItems();
                } 
            } 
            xHR.send(null); 
        }
    }
}


//Function called when clicking on buy it now button
function buy_auction_item(item_id, item_buy_price)
{
    
    var xHR = false;  
    if (window.XMLHttpRequest) {
        xHR = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xHR = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    if(xHR) {
        xHR.open("GET", "action_bidding.php?action=buy_auction_item&item_id="+item_id+"&item_buy_price="+item_buy_price, true); 
        xHR.onreadystatechange = function() 
        { 
            if ((xHR.readyState == 4) && (xHR.status == 200)) { 
                var responseText = xHR.responseText;
                alert(responseText);
                getAuctionItems();
            } 
        } 
        xHR.send(null); 
    }
}
