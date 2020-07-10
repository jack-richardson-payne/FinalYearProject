<?php
// file to log a user in 
if(isset($_POST['login-submit'])){

    require 'dbh.php';

    $email = $_POST['mailuid'];
    $pwd = $_POST['pwd'];

    if(empty($email)|| empty($pwd)){
        // checks form was fully filled out
        header("Location: ../index.php?error=emptyfields&mailuid=".$email);
        exit();
    }
    else {
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt  = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            //checks if input is valid (not SQL code)
            header("Location: ../index.php?error=dbError1");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $checker = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($checker)){
                //checks if user exists
                
                $hashpwd = password_verify($pwd, $row['pass']);
                if($hashpwd == false){
                    //if passwords do not match
                    header("Location: ../index.php?error=incorrectPassword");
                    exit();
                }
                else if($hashpwd == true){
                    //password corrrect
                    session_start();
                    
                    // initialise all session variables used
                    $_SESSION['userId'] = $row['userId'];
                    $_SESSION['userEmail'] = $row['email'];
                    $_SESSION['searchedTeam'];
                    $_SESSION['dateSearched'];
                    $_SESSION['lastDeleted'];
                    $_SESSION['defaultStatsName'];
                    $_SESSION['defaultStatsId'];
                    $_SESSION['defaultStatsUserId'] = $_SESSION['userId'];
                    $_SESSION['defaultStatsUserEmail'] = $_SESSION['userEmail'];
                    // get users team as session variable and assign to value
                    $teamgetter = "SELECT teamId, teamName FROM teams WHERE teamId=".$row['team']."";
                    $result = mysqli_query($conn, $teamgetter);
                    $row2 = mysqli_fetch_assoc($result);
                    $_SESSION['userTeam'] = $row2['teamName'];
                    $_SESSION['favTeamId'] = $row2['teamId'];
                    $_SESSION['dateStart'];
                    $_SESSION['dateEnd'];
                    $_SESSION['dateTitle'];
                    $_SESSION['scrollPos'];

                    header("Location: ../index.php?success=loginSuccess");
                    exit();

                }
                else{
                    // password wrong 
                    header("Location: ../index.php?error=incorrectPassword");
                    exit();
                }
            }
            else {
                // no user with that email 
                header("Location: ../index.php?error=noSuchUser");
                exit();
            }
        }
    }
}
else{
    header("Location: ../index.php");
    exit();
}
