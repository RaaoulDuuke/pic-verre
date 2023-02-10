<?php 
session_start();
require_once ("../includes/connect.php");
include("../includes/website.functions.php");
echo buildPage("company","Qui-sommes-nous");
?>