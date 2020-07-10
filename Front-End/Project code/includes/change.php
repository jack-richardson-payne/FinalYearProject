<?php 
// file called to update the mystats.php page to selected values
session_start();
if(isset($_POST['change-submit'])){

    require 'dbh.php';
    //check date
    if($_POST['DateChange'] == 'AllTime'){
        $_SESSION['dateStart'] = '2015-07-01';
        $_SESSION['dateEnd'] = '2020-06-01';
        $_SESSION['dateTitle'] = 'All Time';
    }
    else if($_POST['DateChange'] == '19/20'){
        $_SESSION['dateStart'] = '2019-07-01';
        $_SESSION['dateEnd'] = '2020-06-01';
        $_SESSION['dateTitle'] = '19/20';
    }
    else if($_POST['DateChange'] == '18/19'){
        $_SESSION['dateStart'] = '2018-07-01';
        $_SESSION['dateEnd'] = '2019-06-01';
        $_SESSION['dateTitle'] = '18/19';
    }
    else if($_POST['DateChange'] == '17/18'){
        $_SESSION['dateStart'] = '2017-07-01';
        $_SESSION['dateEnd'] = '2018-06-01'; 
        $_SESSION['dateTitle'] = '17/18';    
    }
    else if($_POST['DateChange'] == '16/17'){
        $_SESSION['dateStart'] = '2016-07-01';
        $_SESSION['dateEnd'] = '2017-06-01';    
        $_SESSION['dateTitle'] = '16/17';  
    }
    else if($_POST['DateChange'] == '15/16'){
        $_SESSION['dateStart'] = '2015-07-01';
        $_SESSION['dateEnd'] = '2016-06-01'; 
        $_SESSION['dateTitle'] = '15/16';       
    }

    // change defualt team values to update page 
    $_SESSION['defaultStatsName'] = $_POST['teamChange'];
    $_SESSION['defaultStatsUserId'] = $_POST['UserChange'];
    // get selected email 
    $emailGet = "SELECT email FROM users WHERE userId = ".$_SESSION['defaultStatsUserId']."";
    $emailSel = mysqli_query($conn, $emailGet);
    $row2 = mysqli_fetch_assoc($emailSel);
    $_SESSION['defaultStatsUserEmail'] = $row2['email'];

    $idGetter = 'SELECT teamId, teamName FROM teams WHERE teamName = "'.$_SESSION['defaultStatsName'].'"';
    if($result = mysqli_query($conn, $idGetter)){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['defaultStatsId'] = $row['teamId'];
        header("Location: ../myStats.php?success=changed");
        exit();
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
