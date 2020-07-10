<?php
// file to follow another user
if(isset($_POST['follow-submit'])){

    require 'dbh.php';
    session_start();


    $email = $_POST['userEmail'];
    
    if(empty($email)){
        // form not filled 
        header("Location: ../profile.php?error=emptyfields");
        exit();
    }
    $select = "SELECT * FROM users WHERE email=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $select)){
        //checks if input is valid (not SQL code)
        header("Location: ../profile.php?error=dbError1");
        exit();
    }
    else{
        // statement runs
        mysqli_stmt_bind_param($stmt, "s", $email); 
        mysqli_stmt_execute($stmt);
        $Emailchecker = mysqli_stmt_get_result($stmt);
        if($row = mysqli_fetch_assoc($Emailchecker)){

            $followId = $row["userId"];

            if($followId == $_SESSION['userId']){
                // user cannot follow themselves
                header("Location: ../profile.php?error=yourself");
                exit();
            }

            $followcheck = "SELECT * FROM usersfollow WHERE followerId = ".$_SESSION['userId']." AND followingVal = ".$followId."";
            $stmt2 = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt2, $followcheck)){
                //checks if input is valid (not SQL code)
                header("Location: ../profile.php?error=dbError2");
                exit();
            }
            else{
                mysqli_stmt_execute($stmt2);
                $followcheck = mysqli_stmt_get_result($stmt2);
                if($row = mysqli_fetch_assoc($followcheck)){
                    //user is already followed
                    header("Location: ../profile.php?error=alreadyFollowing");
                    exit();
                }
                else{
                    $insert = "INSERT INTO usersfollow (followerId, followingVal) VALUES (".$_SESSION['userId'].", ".$followId.")";
                    $stmt3 = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt3, $insert)){
                        //checks if input is valid (not SQL code)
                        header("Location: ../profile.php?error=dbError3");
                        exit();
                    }
                    else{
                        
                        mysqli_stmt_execute($stmt3);
                        
                        header("Location: ../profile.php?success=followed");
                        exit();
                    }
                }
            }

        }
        else{
            // no user with that email
            header("Location: ../profile.php?error=noSuchUser");
            exit();
        }
    }

}
else{
    header("Location: ../index.php");
    exit();
}