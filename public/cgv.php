<?php 
session_start();
require_once ("../includes/connect.php");
include("../includes/website.functions.php");
echo buildPage("cgv","Conditions générales de vente");
?>