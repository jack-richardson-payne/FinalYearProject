<?php 
    require "header.php"
?>

    <body>
        <div class="Border-main">
            <?php
                
                echo '<div class="jump">';              
                    echo '<a href="searchResult.php#Top" style="border-bottom:1px solid black;">Top</a>';
                echo '</div>';
                echo '<a name="Top" />';
                    //require 'includes/insert.php';
                require 'includes/dbh.php';
                if(!isset($_SESSION['userId'])){
                    header("Location: index.php");
                    exit();
                }
                echo '<div class="errors">';
                if(isset($_GET['error'])){
                    if($_GET['error'] == 'noMatches'){
                        echo '<p>No matches for this team!</p>';
                    }
                    else if($_GET['error'] == 'dbError' || $_GET['error'] == 'dbError1'){
                        echo '<p>Database Error!</p>';
                    }
                    else if($_GET['error'] == 'alreadyLogged'){
                        echo '<p>Match already Logged!</p>';
                    }
                    
                }
                else if(isset($_GET['success'])){
                    if($_GET['success'] == 'added'){
                        echo '<p style="color:green">Match successfully Logged!</p>';
                    }
                    else if($_GET['success'] == 'deleted'){
                        echo '<p style="color:green">Match successfully Deleted!</p>';

                    }
                }
                echo '</div>';
                if(isset($_POST['team'])){
                    if($_POST['team'] == 'none'){
                        header("Location: index.php?error=noTeamSelected");
                        exit();
                    }
                    else{
                        $team = $_POST['team'];
                    }
                }
                else if(empty($team)){
                    $team = $_SESSION['searchedTeam'];
                }

                if(isset($_POST['Date'])){
                    $date = $_POST['Date'];
                }
                else if(empty($date)){
                    $date = $_SESSION['dateSearched'];
                }
                
                // reset to favourite
                $_SESSION['defaultStatsId'] = $_SESSION['favTeamId'];
                $_SESSION['defaultStatsName'] = $_SESSION['userTeam'];
                $_SESSION['defaultStatsUserId'] = $_SESSION['userId'];
                $_SESSION['defaultStatsUserEmail'] = $_SESSION['userEmail'];
                // reset dates to all time
                $_SESSION['dateStart'] = '2015-07-01';
                $_SESSION['dateEnd'] = '2020-06-01';
                $_SESSION['dateTitle'] = 'All Time';
                
                echo '<div class="rotate-message">Rotate Device</div>';
                echo '<div class="headers">';
                echo '<h3 >'.$team.' search results:</h3>';
                echo '<p>Below are the search results for '.$team.', press the <img src="pictures/+.PNG" height=2% width=2%> button to log that you have attended the match. Press <img src="pictures/i.PNG" height=2% width=2%> to see more information about the match. Click on team names to search for matches for that team.</p>';
                echo '</div>';
                
                $_SESSION['searchedTeam'] = $team;
                $_SESSION['dateSearched'] = $date;
                if(!isset($_SESSION['searchedTeam'])){
                    header("Location: index.php");
                    exit(); 
                }
                //all users logged matches
                $matchChecker = "SELECT user_matches.matchVal FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal=".$_SESSION['userId']."";
                if($userMatches = mysqli_query($conn, $matchChecker)){
                    //no db error
                    $storeArray = Array();
                    while($rows = mysqli_fetch_array($userMatches)){
                        //put id's in array
                        $storeArray[] = $rows['matchVal'];
                    }
                    mysqli_free_result($userMatches);
                }
                else{
                    header("Location: index.php?error=dbError5");
                    exit(); 
                }
                if($date != 'AllTime'){

                    if($date == '19/20'){
                        $dateStart = '2019-07-01';
                        $dateEnd = '2020-06-01';
                      
                    }
                    else if($date == '18/19'){
                        $dateStart = '2018-07-01';
                        $dateEnd = '2019-06-01';
                        
                    }
                    else if($date == '17/18'){
                        $dateStart = '2017-07-01';
                        $dateEnd = '2018-06-01'; 
                           
                    }
                    else if($date == '16/17'){
                        $dateStart = '2016-07-01';
                        $dateEnd = '2017-06-01';    
                    }
                    else if($date == '15/16'){
                        $dateStart = '2015-07-01';
                        $dateEnd = '2016-06-01'; 
                    }
                    echo '<a name="'.$date.'" />';
                    $sql = "SELECT * FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId WHERE hometable.home = '$team' OR awaytable.away ='$team' GROUP BY hometable.matchId ORDER BY hometable.date DESC) AS result WHERE date BETWEEN '".$dateStart."' AND '".$dateEnd."'";
                    if($result = mysqli_query($conn, $sql)){
                        // just selected season 
                        if(mysqli_num_rows($result) > 0){
                            echo '<h2 style="float:left;margin-left: 10%; margin-top:2%;width:75%; text-decoration:underline;">20'.$date.'</h2>';
                            echo "<table class='outTable'>";
                                echo "<tr>"; 
                                    echo "<th style='border-left:none;'></th>";                                 
                                    echo "<th>Date</th>";
                                    echo "<th>Home</th>";
                                    echo "<th>Home goals</th>";
                                    echo "<th>Away goals</th>";
                                    echo "<th>Away</th>";
                                    echo "<th style='border-right:none;'></th>";                                    
                                echo "</tr>";
                                $counter = 1;
                            while($row = mysqli_fetch_assoc($result)){
                                if(in_array($row["matchId"], $storeArray)){
                                    echo '<tr style="background-color: #80ff80;">';
                                    
                                }
                                else{
                                    echo '<tr>';
                                }
                                echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='searchResult'><input type='hidden' name='pos' value='".$date."'><button type='submit' name='matchId' value=". $row['matchId']." >i</button></form></td>";                        

                                if($row['home'] == $team){
                                    //make team searched bold if home
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='".$date."'><button type='submit' name='matchId' value=".$row['matchId']." >+</button></form></td>";                        
                                }
                                else if($row['away'] == $team){
                                    //make team searched bold if away
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                   echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='".$date."'><button type='submit' name='matchId' value=". $row['matchId']." >+</button></form></td>";                        
                                }
                                echo "</tr>";
                                
                            }
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                            
                        }
                        else{
                            header("Location: index.php?error=noMatchessearch");
                            exit(); 
                        } 
                    } 
                    else{
                        header("Location: index.php?error=dbError");
                        exit();
                    }

                }
                else{
                    // all possible seasons
                    echo '<a name="19/20" />';
                    $sql = "SELECT * FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId WHERE hometable.home = '$team' OR awaytable.away ='$team' GROUP BY hometable.matchId ORDER BY hometable.date DESC) AS result WHERE date BETWEEN '2019-07-01' AND '2020-06-01'";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<h2 style="float:left;margin-left: 10%; margin-top:2%;width:75%; text-decoration:underline;">2019/20</h2>';
                            echo "<table class='outTable'>";
                                echo "<tr>"; 
                                    echo "<th style='border-left:none;'></th>";                                 
                                    echo "<th>Date</th>";
                                    echo "<th>Home</th>";
                                    echo "<th>Home goals</th>";
                                    echo "<th>Away goals</th>";
                                    echo "<th>Away</th>";
                                    echo "<th style='border-right:none;'></th>";                                    
                                echo "</tr>";
                                $counter = 1;
                            while($row = mysqli_fetch_assoc($result)){
                                if(in_array($row["matchId"], $storeArray)){
                                    echo '<tr style="background-color: #80ff80;">';
                                    
                                }
                                else{
                                    echo '<tr>';
                                }
                                echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='searchResult'><input type='hidden' name='pos' value='19/20'><button type='submit' name='matchId' value=". $row['matchId']." >i</button></form></td>";                        

                                if($row['home'] == $team){
                                    //make team searched bold if home
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='19/20'><button type='submit' name='matchId' value=".$row['matchId']." >+</button></form></td>";                        
                                }
                                else if($row['away'] == $team){
                                    //make team searched bold if away
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                   echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='19/20'><button type='submit' name='matchId' value=". $row['matchId']." >+</button></form></td>";                        
                                }
                                echo "</tr>";
                                
                            }
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                            
                        } 
                    } 
                    else{
                        header("Location: index.php?error=dbError");
                        exit();
                    }

                    echo '<a name="18/19" />';
                    // 2018/19
                    $sql = "SELECT * FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId WHERE hometable.home = '$team' OR awaytable.away ='$team' GROUP BY hometable.matchId ORDER BY hometable.date DESC) AS result WHERE date BETWEEN '2018-07-01' AND '2019-06-01'";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<h2 style="float:left;margin-left: 10%; margin-top:2%;width:75%; text-decoration:underline;">2018/19</h2>';
                            echo "<table class='outTable'>";
                                echo "<tr>";    
                                    echo "<th style='border-left:none;'></th>";                                 
                                    echo "<th>Date</th>";
                                    echo "<th>Home</th>";
                                    echo "<th>Home goals</th>";
                                    echo "<th>Away goals</th>";
                                    echo "<th>Away</th>";
                                    echo "<th style='border-right:none;'></th>";                                     
                                echo "</tr>";
                                $counter = 1;
                            while($row = mysqli_fetch_assoc($result)){
                                if(in_array($row["matchId"], $storeArray)){
                                    echo '<tr style="background-color: #80ff80;">';
                                    
                                }
                                else{
                                    echo '<tr>';
                                }
                                echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='searchResult'><input type='hidden' name='pos' value='18/19'><button type='submit' name='matchId' value=". $row['matchId']." >i</button></form></td>";                        

                                if($row['home'] == $team){
                                    //make team searched bold if home
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='18/19'><button type='submit' name='matchId' value=".$row['matchId']." >+</button></form></td>";                        
                                }
                                else if($row['away'] == $team){
                                    //make team searched bold if away
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                   echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='18/19'><button type='submit' name='matchId' value=". $row['matchId']." >+</button></form></td>";                        
                                }
                                echo "</tr>";
                                
                            }
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                            
                        } 
                    } 
                    else{
                        header("Location: index.php?error=dbError");
                        exit();
                    }
                    echo '<a name="17/18" />';
                    // 2017/18
                    
                    $sql = "SELECT * FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId WHERE hometable.home = '$team' OR awaytable.away ='$team' GROUP BY hometable.matchId ORDER BY hometable.date DESC) AS result WHERE date BETWEEN '2017-07-01' AND '2018-06-01'";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<h2 style="float:left;margin-left: 10%; margin-top:2%;width:75%; text-decoration:underline;">2017/18</h2>';
                            echo "<table class='outTable'>";
                                echo "<tr>"; 
                                    echo "<th style='border-left:none;'></th>";                                 
                                    echo "<th>Date</th>";
                                    echo "<th>Home</th>";
                                    echo "<th>Home goals</th>";
                                    echo "<th>Away goals</th>";
                                    echo "<th>Away</th>";
                                    echo "<th style='border-right:none;'></th>";                                     
                                echo "</tr>";
                                $counter = 1;
                            while($row = mysqli_fetch_assoc($result)){
                                if(in_array($row["matchId"], $storeArray)){
                                    echo '<tr style="background-color: #80ff80;">';
                                    
                                }
                                else{
                                    echo '<tr>';
                                }
                                echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='searchResult'><input type='hidden' name='pos' value='17/18'><button type='submit' name='matchId' value=". $row['matchId']." >i</button></form></td>";                        

                                if($row['home'] == $team){
                                    //make team searched bold if home
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='17/18'><button type='submit' name='matchId' value=".$row['matchId']." >+</button></form></td>";                        
                                }
                                else if($row['away'] == $team){
                                    //make team searched bold if away
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                   echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='17/18'><button type='submit' name='matchId' value=". $row['matchId']." >+</button></form></td>";                        
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                            
                        } 
                    } 
                    else{
                        header("Location: index.php?error=dbError");
                        exit();
                    }
                    echo '<a name="16/17" />';
                    // 2016/17
                    
                    $sql = "SELECT * FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId WHERE hometable.home = '$team' OR awaytable.away ='$team' GROUP BY hometable.matchId ORDER BY hometable.date DESC) AS result WHERE date BETWEEN '2016-07-01' AND '2017-06-01'";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<h2 style="float:left;margin-left: 10%; margin-top:2%;width:75%; text-decoration:underline;">2016/17</h2>';
                            echo "<table class='outTable'>";
                                echo "<tr>"; 
                                    echo "<th style='border-left:none;'></th>";                                 
                                    echo "<th>Date</th>";
                                    echo "<th>Home</th>";
                                    echo "<th>Home goals</th>";
                                    echo "<th>Away goals</th>";
                                    echo "<th>Away</th>";
                                    echo "<th style='border-right:none;'></th>";                                     
                                echo "</tr>";
                                $counter = 1;
                            while($row = mysqli_fetch_assoc($result)){
                                if(in_array($row["matchId"], $storeArray)){
                                    echo '<tr style="background-color: #80ff80;">';
                                    
                                }
                                else{
                                    echo '<tr>';
                                }
                                echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='searchResult'><input type='hidden' name='pos' value='16/17'><button type='submit' name='matchId' value=". $row['matchId']." >i</button></form></td>";                        

                                if($row['home'] == $team){
                                    //make team searched bold if home
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='16/17'><button type='submit' name='matchId' value=".$row['matchId']." >+</button></form></td>";                        
                                }
                                else if($row['away'] == $team){
                                    //make team searched bold if away
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                   echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='16/17'><button type='submit' name='matchId' value=". $row['matchId']." >+</button></form></td>";                        
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                            
                        } 
                    } 
                    else{
                        header("Location: index.php?error=dbError");
                        exit();
                    }
                    echo '<a name="15/16" />';
                    //2015/16
                    
                    $sql = "SELECT * FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId WHERE hometable.home = '$team' OR awaytable.away ='$team' GROUP BY hometable.matchId ORDER BY hometable.date DESC) AS result WHERE date BETWEEN '2015-07-01' AND '2016-06-01'";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<h2 style="float:left;margin-left: 10%; margin-top:2%;width:75%; text-decoration:underline;">2015/16</h2>';
                            echo "<table class='outTable'>";
                                echo "<tr>"; 
                                    echo "<th style='border-left:none;'></th>";                                 
                                    echo "<th>Date</th>";
                                    echo "<th>Home</th>";
                                    echo "<th>Home goals</th>";
                                    echo "<th>Away goals</th>";
                                    echo "<th>Away</th>";
                                    echo "<th style='border-right:none;'></th>";                                     
                                echo "</tr>";
                                $counter = 1;
                            while($row = mysqli_fetch_assoc($result)){
                                if(in_array($row["matchId"], $storeArray)){
                                    echo '<tr style="background-color: #80ff80;">';
                                    
                                }
                                else{
                                    echo '<tr>';
                                } 
                                echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='searchResult'><input type='hidden' name='pos' value='15/16'><button type='submit' name='matchId' value=". $row['matchId']." >i</button></form></td>";                        

                                if($row['home'] == $team){
                                    //make team searched bold if home
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='15/16'><button type='submit' name='matchId' value=".$row['matchId']." >+</button></form></td>";                        
                                }
                                else if($row['away'] == $team){
                                    //make team searched bold if away
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['home']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['home']."</button></form></td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['away']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['away']."</button></form></td>";
                                    // creates a button with name and value = match id
                                    // post hides id in URL
                                    echo "<td> <form action='includes/insert.php' method='post'><input type='hidden' name='pos' value='15/16'><button type='submit' name='matchId' value=". $row['matchId']." >+</button></form></td>";                        
                                }
                                echo "</tr>";
                            }
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                            
                        } 
                    } 
                    else{
                        header("Location: index.php?error=dbError");
                        exit();
                    }
                }
                echo '</div>';           
            ?>
        </div>
    </body>

<?php 
    require "footer.php"
?>