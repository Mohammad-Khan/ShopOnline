<?php
/*
	Student ID: 4974948
	Student Name: Mohammad Khan
	Logout page 
	Upon call, this page is supposed to log out the user from system and redirect to login page
	
*/
session_start();
unset($_SESSION);
header("location:login.htm");

?>