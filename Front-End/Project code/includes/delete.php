<?php
// file to remove a match from users logged matches

require 'dbh.php';
session_start();


$matchId = $_POST['matchId'];
$userId = $_SESSION['userId'];
$position = $_POST['pos'];
$page = $_POST['page'];

$delete = "DELETE FROM user_matches WHERE userVal=? AND matchVal=?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $delete)){
    //checks if input is valid (not SQL code) etc..
    header("Location: ../myMatches.php?error=dbError1");
    exit();
}
else{
    $_SESSION['lastDeleted'] = $matchId;
    mysqli_stmt_bind_param($stmt, "ii", $userId, $matchId);
    mysqli_stmt_execute($stmt);
    // return to same page and position
    header("Location: ../".$page.".php?success=deleted#".$position."");
    exit();
}

?>