<?php
/*
	Student ID: 4974948
	Student Name: Mohammad Khan
	
	This page is responsible for procession login details in collaboration with login.js
*/

session_start();

error_reporting(0); //turning off the default error reporting of php

$customers = "../../data/customer.xml";

$res = array();
if(isset($_POST))
{
    //error message
    $error = "";
    
    //get the details
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);
	
	
	if(empty($email))
	{
		$error = "Please enter email !";
	}
	elseif(empty($password))
	{
		$error = "Please enter password !";
	}
	else
	{
                //check if customer.xml file exists or not
                if(!file_exists($customers))
                {
                    //create customer xml file
                    $fp = fopen($customers, 'w+');
                    fwrite($fp, utf8_decode('<?xml version="1.0" encoding="UTF-8"?><customers></customers>'));
                    fclose($fp);
                    chmod($customers, 0777); 
                }

                
		//check if login details are correct or not
		$xml_doc = new DOMDocument;
                $xml_doc->preserveWhiteSpace = false;
                $xml_doc->Load($customers);
                $xpath = new DOMXPath($xml_doc);

                $customers = $xpath->query("//customers/customer[email='".$email."'][password='".$password."']");

                if($customers->length > 0)
                {    
                    session_start();

                    $_SESSION['id'] = $xpath->query("//customers/customer[email='".$email."']/id")->item(0)->nodeValue;
                    $_SESSION['name'] = $xpath->query("//customers/customer[email='".$email."']/name")->item(0)->nodeValue;
                    $_SESSION['surname'] = $xpath->query("//customers/customer[email='".$email."']/surname")->item(0)->nodeValue;
                    $_SESSION['email'] = $xpath->query("//customers/customer[email='".$email."']/email")->item(0)->nodeValue;
			
		}
		else
		{
			$error = "Wrong email id or password !";
		}
	}
        if($error != "")
        {
            $res['result']="fail";
            $res['message']=$error;
        }
        else
        {
            $res['result']="succ";
        }
        echo json_encode($res);
}

?>