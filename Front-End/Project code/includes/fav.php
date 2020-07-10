<?php
// file to favourite/unfavourite a match that a user has logged

if(isset($_POST['fav-Id'])){
    session_start();
    // fav a match
    require 'dbh.php';

    $favId = $_POST['fav-Id'];
    $position = $_POST['pos'];

    $update = "UPDATE user_matches SET fav = 'Y' WHERE userVal = ".$_SESSION['userId']." AND matchVal = ".$favId."";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $update)){
        
        header("Location: ../myMatches.php?error=dbError1");
        exit();
    }
    else{

        mysqli_stmt_execute($stmt);
        header("Location: ../myMatches.php?success=favourited#".$position."");
        exit();
    }

}
if(isset($_POST['unfav-Id'])){
    session_start();
    // unfav a match
    require 'dbh.php';

    $favId = $_POST['unfav-Id'];
    $position = $_POST['pos'];

    $update = "UPDATE user_matches SET fav = 'N' WHERE userVal = ".$_SESSION['userId']." AND matchVal = ".$favId."";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $update)){
     
        header("Location: ../myMatches.php?error=dbError2");
        exit();
    }
    else{

        mysqli_stmt_execute($stmt);
        header("Location: ../myMatches.php?success=unfavourited#".$position."");
        exit();
    }

}
else{
    header("Location: ../index.php");
    exit();
}