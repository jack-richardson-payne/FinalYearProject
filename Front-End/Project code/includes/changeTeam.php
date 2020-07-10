<?php
// file called when user wants to change favourite team
session_start();
if(isset($_POST['teamChange-submit'])){
    require 'dbh.php';
    // updates team name session variable
    $_SESSION['userTeam'] = $_POST['FavteamChange'];

    $idGetter = 'SELECT teamId, teamName FROM teams WHERE teamName = "'.$_SESSION['userTeam'].'"';
    if($result = mysqli_query($conn, $idGetter)){
        $row = mysqli_fetch_assoc($result);
        // gets team id
        $_SESSION['favTeamId'] = $row['teamId'];

        $sqlIns = "UPDATE users SET team = ? WHERE email='".$_SESSION['userEmail']."'";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sqlIns)){
            //checks if input is valid (not SQL code)
            header("Location: ../profile.php?error=dbError2");
            exit();
        }
        else{
    

            mysqli_stmt_bind_param($stmt, "i", $_SESSION['favTeamId']); // i = int 
            mysqli_stmt_execute($stmt);
            header("Location: ../profile.php?success=TeamChanged");
        exit();
        }
        
    }
    else{
        header("Location: ../index.php?error=DbError");
        exit();
    }

}
else{
    header("Location: ../index.php");
    exit();
}