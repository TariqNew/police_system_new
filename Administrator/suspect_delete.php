<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (isset($_SESSION['admin_id']) && 
    isset($_SESSION['role'])     &&
    isset($_GET['suspect_id'])) {

  if ($_SESSION['role'] == 'Admin') {
     include "../DB_connection.php";
     include "data/suspect.php";

     $id = $_GET['suspect_id'];
     if (removeSuspect($id, $conn)) {
     	$sm = "Successfully deleted!";
        header("Location: suspect.php?success=$sm");
        exit;
     } else {
        $em = "Unknown error occurred";
        header("Location: suspect.php?error=$em");
        exit;
     }

  } else {
    header("Location: suspect.php");
    exit;
  } 
} else {
	header("Location: ../login.php");
	exit;
}
