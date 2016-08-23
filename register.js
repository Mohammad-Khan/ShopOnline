/*
	 Student ID: 4974948
	 Student Name: Mohammad Khan
	 
	 This page is responsible for processing the registration details
 */
 
function submitform()
{
    var xml_http_request = false;  
    if (window.XMLHttpRequest) {
        xml_http_request = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xml_http_request = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    if(xml_http_request) {
        var serialized = $('form').serialize();
        xml_http_request.open("POST", "action_register.php", true); 
        xml_http_request.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xml_http_request.send(serialized);
        xml_http_request.onreadystatechange = function() 
        { 
            if ((xml_http_request.readyState == 4) && (xml_http_request.status == 200)) { 
                var responseText = jQuery.parseJSON(xml_http_request.responseText);
                                
                if(responseText.result == "fail")
                {
                    $('#err_msg').html(responseText.message);
                }
                else
                {
                    window.location.href="listing.htm";
                }
            } 
        } 
    }
    return false;
}

