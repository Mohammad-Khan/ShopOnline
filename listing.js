function submitform()
{
    var xHR = false;  
    if (window.XMLHttpRequest) {
        xHR = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xHR = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    if(xHR) {
        var formDetails = $('form').serialize();
        xHR.open("POST", "action_listing.php", true); 
        xHR.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xHR.send("action=listing&"+formDetails);
        xHR.onreadystatechange = function() 
        { 
            if ((xHR.readyState == 4) && (xHR.status == 200)) { 
                var responseText = jQuery.parseJSON(xHR.responseText);
                if(responseText.result == "fail")
                {
                    $('#err_msg').html(responseText.message);
                }
                else if(responseText.result == "succ")
                {
                    $('#div_succ').show();
                    $('#div_succ').html(responseText.message);
                    $('#div_form').hide();
                }
            } 
        } 
    }
    return false;
}
    
function other_categorie(cat_val)
{
    var other_category = document.getElementById("other_category");
    if(cat_val == "other")
    {
       other_category.style.display = "inline"; 
    }
    else
    {
        other_category.style.display = "none"; 
    }
}

	
	
$('document').ready(function(){
    //generating day options list   
    for (var i = 0; i<=15; i++){
        var opt = document.createElement('option');
        opt.value = i;
        opt.innerHTML = i;
        document.getElementById('day').appendChild(opt);
    }
    
    //generating hours options list
    for (var i = 0; i<=23; i++){
        var opt = document.createElement('option');
        opt.value = i;
        opt.innerHTML = i;
        document.getElementById('hour').appendChild(opt);
    }
    
    //generating minutes options list
    for (var i = 0; i<=60; i++){
        var opt = document.createElement('option');
        opt.value = i;
        opt.innerHTML = i;
        document.getElementById('minute').appendChild(opt);
    }
    
    //get categories
    var xHR = false;  
    if (window.XMLHttpRequest) {
        xHR = new XMLHttpRequest();
    }
    else if (window.ActiveXObject) {
        xHR = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    if(xHR) {
        xHR.open("POST", "action_listing.php", true); 
        xHR.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xHR.send("action=get_categories");
        xHR.onreadystatechange = function() 
        { 
            if ((xHR.readyState == 4) && (xHR.status == 200)) { 
                var responseText = jQuery.parseJSON(xHR.responseText);
                
                for (var category in responseText.cats) {
                    //alert(responseText.categories[category]);
                    $('#slct_cat').append('<option>'+responseText.cats[category]+'</option>');
                }
                
            } 
        } 
    }
    
});