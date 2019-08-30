<?php 
session_start();

if(!is_null($_POST["done"])){
	session_destroy();
	header("Location: ../../".$_SESSION['currentPage']);
};
 ?>