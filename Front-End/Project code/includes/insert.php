<?php
// file to log a match

require 'dbh.php';
session_start();


$matchId = $_POST['matchId'];
$userId = $_SESSION['userId'];
$position = $_POST['pos'];


$select = "SELECT * FROM user_matches WHERE userVal=? AND matchVal=?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $select)){
    //checks if input is valid (not SQL code)
    header("Location: ../searchResult.php?error=dbError1");
    exit();
}
else{
    
    mysqli_stmt_bind_param($stmt, "ii", $userId, $matchId); 
    mysqli_stmt_execute($stmt);
    $checker = mysqli_stmt_get_result($stmt);
    if($row = mysqli_fetch_assoc($checker)){
        // match already logged 
        header("Location: ../searchResult.php?error=alreadyLogged#".$position."");
        exit();
    }
    else{
        $insert = "INSERT INTO user_matches (userVal, matchVal) VALUES (?, ?)";
        $stmt2 = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($stmt2, $insert)){
            //checks if input is valid (not SQL code)
            header("Location: ../searchResult.php?error=dbError1");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt2, "ii", $userId, $matchId);
            mysqli_stmt_execute($stmt2);
            // return to past position
            header("Location: ../searchResult.php?success=added#".$position."");
            exit();
        }
    }

}


?>