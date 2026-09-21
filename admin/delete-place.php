<?php

session_start();

include "../config/database.php";


// Make sure admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}


// Check place ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: dashboard.php");
    exit;
}

$place_id = (int) $_GET["id"];


// Delete the place
$sql = "
    DELETE FROM places
    WHERE place_id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $place_id
);


if ($stmt->execute()) {

    header("Location: dashboard.php?deleted=1");
    exit;

} else {

    die("Unable to delete the place.");

}

?>