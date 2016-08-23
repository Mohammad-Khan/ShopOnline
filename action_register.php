<?php
/*
	Student ID: 4974948
	Student Name: Mohammad Khan
	This page is responsible for processiong all the registration detail in collaboration wit register.js
*/

//turning off the default error reporting of php
error_reporting(0);

$customer_xml_file = "../../data/customer.xml";

$res = array();

//run the code only if the form is submitted
if(isset($_POST))
{
//form validation error message
$error = "";

	
	$fname = trim($_POST['fname']);
	$lname = trim($_POST['lname']);
	$email = trim($_POST['email']);
	
	
	//dummy passwords, new system generated password will be sent to user
	$password = trim($_POST['password']);
	$re_password = trim($_POST['re_password']);

			
	//check all fields, if any field is empty report an error
	if(empty($fname))
	{
        $error = "Please provide all details!";
	}
	if(empty($lname))
	{
        $error = "Please provide all details!";
	}
    if(! validate_email($email))
	{
		$error = "Please provide all details!";
	}
	
	//dummy passwords, no use..
	if (empty($password)) {
            $error = "Please enter password";
    }
	elseif($password != $re_password)
	{
		$error = "Passwords does not match with each other!";
	}
			

//See if no error in registration details, execute rest of the code
if($error == "")
{	
	//if customer.xml does not exist, create one in data folder
    if(!file_exists($customer_xml_file))
    {
        $fp = fopen($customer_xml_file, 'w+');
        fwrite($fp, utf8_decode('<?xml version="1.0" encoding="UTF-8"?><customers></customers>'));
        fclose($fp);
        chmod($customer_xml_file, 0777); //Setting all the read/write permissions to true for customer.xml file
    }

            	
	$xml_doc = new DOMDocument();
    $xml_doc->load( $customer_xml_file );
                
    $xpath = new DOMXPath($xml_doc);
    
	//checking if this email account is already registered
    $customers = $xpath->query("//customers/customer[email='".$email."']");
    if($customers->length > 0)
    {
        $error = "This email is already registered!";
    }
    else
    {
        $customer_xml_file = realpath($customer_xml_file);
        $xml_doc = new DOMDocument('1.0');
        $xml_doc->load($customer_xml_file);

        $root = $xml_doc->documentElement;
        $customer = $xml_doc->createElement('customer');
                    
        $customer_id = uniqid();
        $node_id = $xml_doc->createElement( "id" );
        $node_id->appendChild($xml_doc->createTextNode( $customer_id));
        $customer->appendChild( $node_id );
                                       
        $node_name = $xml_doc->createElement( "name" );
        $node_name->appendChild($xml_doc->createTextNode( $fname));
        $customer->appendChild( $node_name );

        $node_surname = $xml_doc->createElement( "surname" );
        $node_surname->appendChild($xml_doc->createTextNode( $lname));
        $customer->appendChild( $node_surname );

        $node_email = $xml_doc->createElement( "email" );
        $node_email->appendChild($xml_doc->createTextNode( $email));
        $customer->appendChild( $node_email );

		$password = random_password();
		$node_password = $xml_doc->createElement( "password" );
        $node_password->appendChild($xml_doc->createTextNode( $password));
        $customer->appendChild( $node_password );

        $root->appendChild( $customer );

        $xml_doc->save($customer_xml_file);
                    
        //Sending email to the customer
                    
        $message = "Dear ".$name.", welcome to use ShopOnline! Your customer id is ".$customer_id." and the password is ".$password;
        $headers = "From: registration@shoponline.com.au";
		mail($email, "Welcome to ShopOnline !", $message, $headers, "-r 4974948@student.swin.edu.au");
                   
        session_start();

        $_SESSION['id'] = $customer_id;
        $_SESSION['name'] = $fname;
        $_SESSION['surname'] = $lname;
        $_SESSION['email'] = $email;
		}
		
	}
	  
	if($error != "")
	{
		$res['result']="fail";
		$res['message']=$error;
	}
	else
	{
		$res['message']="succ";
	}
    echo json_encode($res);
}

function random_password() {
	$length = 6;
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}


//This email validation code has been taken from stackoverflow http://stackoverflow.com/questions/13719821/email-validation-using-regular-expression-in-php

function validate_email($email) {
    $isValid = true;
    $atIndex = strrpos($email, "@");

    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);

        if ($localLen < 1 || $localLen > 64) {
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            $isValid = false;
        } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            $isValid = false;
        } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }

    if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain ,"A"))) {
            $isValid = false;
    }
    }

    return $isValid;
}
?>