<?php
	session_start();
	
	if($_SESSION['theme']=="dark"){
		$_SESSION['theme']="classic";
	}else{
		$_SESSION['theme']="dark";
	}
	
	header("location:".$_SERVER['HTTP_REFERER']);
?>