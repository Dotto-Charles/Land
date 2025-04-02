<?php 
	session_start();
	
	include('..config/db.php');

	session_destroy();
	session_unset();

	header('Location: login.php');

?>