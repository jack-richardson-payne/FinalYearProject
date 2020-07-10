<?php
// file that gathers all the users stats using session variables and default values, then assign them to values. 

    require 'dbh.php';
    if(!isset($_SESSION['userId'])){
        header("Location: index.php");
        exit();
    }
    // check is user logged any matches
    $user = $_SESSION['userId'];           
    $matchChecker = "SELECT COUNT(user_matches.matchVal) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal=".$_SESSION['userId']."";
    if($check = mysqli_query($conn, $matchChecker)){
        
        $row = mysqli_fetch_assoc($check);
    
        $totalNumMatches= $row['COUNT(user_matches.matchVal)'];
        if($totalNumMatches == 0){
            // No matches Logged 
            header("Location: index.php?error=noMatchesLogged");
            exit();
        }
    
    }
    else{
        header("Location: index.php?error=dbError");
        exit();
    }


    $graphSQL = "SELECT output.name AS teamName, SUM(output.apps) AS games, SUM(output.goals) AS goals FROM (SELECT home.teamName AS name, COUNT(DISTINCT home.matchId) AS apps, SUM(home.FTHG) AS goals FROM (SELECT teams.teamName, us.matchId, us.FTHG FROM teams JOIN (SELECT * FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS us WHERE teams.teamId = us.homeTeam) AS home GROUP BY home.teamName UNION ALL SELECT away.teamName AS name2, COUNT(DISTINCT away.matchId) AS apps2, SUM(away.FTAG) AS goals2 FROM (SELECT teams.teamName, us2.matchId, us2.FTAG FROM teams JOIN (SELECT * FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS us2 WHERE teams.teamId = us2.awayTeam) AS away GROUP BY away.teamName) AS output GROUP BY output.name ORDER BY games DESC, name";
    if($graphresult = mysqli_query($conn, $graphSQL)){
        $BigArray = Array();
        while($rows = mysqli_fetch_array($graphresult)){
            //put id's in array
            $SmallArray = Array();
            $SmallArray[] = $rows['teamName'];
            $SmallArray[] = $rows['games'];
            $SmallArray[] = $rows['goals'];
            
            $BigArray[] = $SmallArray;
        }
        mysqli_free_result($graphresult);
    }
    else {
        header("Location: index.php?error=dbError1");
        exit(); 
    }


    $dataPoints = array();
    foreach($BigArray as $line){
        $dataPoints[] = array("label"=> $line[0], "y"=> (int)$line[2]);
        
    }
    
    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    $teamAll = "SELECT COUNT(matches.matchId) AS total FROM matches WHERE matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."' AND (matches.homeTeam = ".$_SESSION['defaultStatsId']." OR matches.awayTeam = ".$_SESSION['defaultStatsId'].")";
    if($teamquery = mysqli_query($conn, $teamAll)){
        $teamrow = mysqli_fetch_assoc($teamquery);

        $totalTeamGames = $teamrow['total'];


    }
    else {
        header("Location: index.php?error=dbError1");
        exit(); 
    }


    $popped = Array();
    $nameArray = Array();
    $matchArray = Array();
    $goalArray = Array();
    $gpgArray = Array();
    $colourArray = Array();
    foreach($BigArray as $element){
        $nameArray[] = $element[0];
        $matchArray[] = (int)$element[1];
        if((int)$element[2] == 0){
            $goalArray[] = 0;
        }else{

        
        $goalArray[] = (int)$element[2];
        }

        $gpgArray[] = ((int)$element[2]/(int)$element[1]);
        $colourArray[] = rand_color();
    }

    $graphSQLteam = "SELECT (Awin + Hwin) AS wins, (Adraw + Hdraw) AS draws, (ALoss + HLoss) AS loss FROM (SELECT * FROM (SELECT * FROM (SELECT COUNT(matches.result) AS Hwin FROM matches JOIN user_matches WHERE matches.homeTeam = ".$_SESSION['defaultStatsId']." AND matches.result = 'H' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS homeWins JOIN (SELECT COUNT(matches.result) AS Hdraw FROM matches JOIN user_matches WHERE matches.homeTeam = ".$_SESSION['defaultStatsId']." AND matches.result = 'D' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS homeDraws JOIN (SELECT COUNT(matches.result) AS HLoss FROM matches JOIN user_matches WHERE matches.homeTeam = ".$_SESSION['defaultStatsId']." AND matches.result = 'A' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS homeLoss) AS home JOIN (SELECT * FROM (SELECT COUNT(matches.result) AS Awin FROM matches JOIN user_matches WHERE matches.awayTeam = ".$_SESSION['defaultStatsId']." AND matches.result = 'A' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS awayWins JOIN (SELECT COUNT(matches.result) AS Adraw FROM matches JOIN user_matches WHERE matches.awayTeam = ".$_SESSION['defaultStatsId']." AND matches.result = 'D' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS awayDraws JOIN (SELECT COUNT(matches.result) AS ALoss FROM matches JOIN user_matches WHERE matches.awayTeam = ".$_SESSION['defaultStatsId']." AND matches.result = 'H' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['defaultStatsUserId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS AwayLoss) AS away) AS games";
    if($graphresult2 = mysqli_query($conn, $graphSQLteam)){
        $rowgraph = mysqli_fetch_assoc($graphresult2);
        $winArray = Array();
        $drawArray = Array();
        $lossArray = Array();
        $winArray[] = $rowgraph['wins'];
        $drawArray[] = $rowgraph['draws'];
        $lossArray[] = $rowgraph['loss'];


    }
    else {
        header("Location: index.php?error=dbError1");
        exit(); 
    }

    $mostSeen = $BigArray[0][0];

    
    //get totals table
    $totals = "SELECT COUNT(user_matches.matchVal), SUM(matches.FTHG), SUM(matches.FTAG), SUM(matches.HTHG), SUM(matches.HTAG), SUM(matches.hShot), SUM(matches.aShot), SUM(matches.hShotTar), SUM(matches.aShotTar), SUM(matches.hFouls), SUM(matches.aFouls), SUM(matches.hCorners), SUM(matches.aCorners), SUM(matches.hYellow), SUM(matches.aYellow), SUM(matches.hRed), SUM(matches.aRed) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal=".$_SESSION['defaultStatsUserId']." AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."'";
    if($result = mysqli_query($conn, $totals)){
        //if matches logged
        $row = mysqli_fetch_assoc($result);
        
        $totalNumMatches= $row['COUNT(user_matches.matchVal)'];
        if($totalNumMatches == 0){
            // No matches Logged 
            header("Location: index.php?error=noMatches");
            exit();
        }
        $totalFTHG= $row['SUM(matches.FTHG)'];     
        $totalFTAG= $row['SUM(matches.FTAG)'];            
        $totalAllGoals = $totalFTHG + $totalFTAG;
        $totalhShot= $row['SUM(matches.hShot)'];
        $totalaShot= $row['SUM(matches.aShot)'];
        $totalhShotTar= $row['SUM(matches.hShotTar)'];
        $totalaShotTar= $row['SUM(matches.aShotTar)'];
        $totalhFouls= $row['SUM(matches.hFouls)'];
        $totalaFouls= $row['SUM(matches.aFouls)'];
        $totalhCorners= $row['SUM(matches.hCorners)'];
        $totalaCorners= $row['SUM(matches.aCorners)'];
        $totalhYellow= $row['SUM(matches.hYellow)'];
        $totalaYellow= $row['SUM(matches.aYellow)'];
        $totalhRed= $row['SUM(matches.hRed)'];
        $totalaRed= $row['SUM(matches.aRed)'];

        $teamHome = "SELECT COUNT(user_matches.matchVal), SUM(matches.FTHG), SUM(matches.HTHG), SUM(matches.hShot), SUM(matches.hShotTar), SUM(matches.hFouls), SUM(matches.hCorners), SUM(matches.hYellow), SUM(matches.hRed) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal=".$_SESSION['defaultStatsUserId']." AND matches.homeTeam =".$_SESSION['defaultStatsId']." AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."'";
        $teamAway = "SELECT COUNT(user_matches.matchVal), SUM(matches.FTAG), SUM(matches.HTAG), SUM(matches.aShot), SUM(matches.aShotTar), SUM(matches.aFouls), SUM(matches.aCorners), SUM(matches.aYellow), SUM(matches.aRed) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal=".$_SESSION['defaultStatsUserId']." AND matches.awayTeam =".$_SESSION['defaultStatsId']." AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."'";
        $awayOpp = "SELECT COUNT(user_matches.matchVal), SUM(matches.FTAG), SUM(matches.aShot), SUM(matches.aShotTar), SUM(matches.aFouls), SUM(matches.aCorners), SUM(matches.aYellow), SUM(matches.aRed) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal=".$_SESSION['defaultStatsUserId']." AND matches.homeTeam =".$_SESSION['defaultStatsId']." AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."'";
        $homeOpp = "SELECT COUNT(user_matches.matchVal), SUM(matches.FTHG), SUM(matches.hShot), SUM(matches.hShotTar), SUM(matches.hFouls), SUM(matches.hCorners), SUM(matches.hYellow), SUM(matches.hRed) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal=".$_SESSION['defaultStatsUserId']." AND matches.awayTeam =".$_SESSION['defaultStatsId']." AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."'";
        if($result2 = mysqli_query($conn, $teamHome)){
            // user has home matches logged for favourite team
            $row = mysqli_fetch_assoc($result2);
            $homeNumMatches= $row['COUNT(user_matches.matchVal)'];
            // could add these if above isnt 0
            
            if($homeNumMatches == 0){
                echo '<div class="errors">';
                echo '<p style="float:left;">No home matches logged for selected team!</p>';
                echo '</div>'; 
                $homeFTHG= 0;              
                $homeShot= 0;
                $homeShotTar= 0;
                $homeFouls= 0;
                $homeCorners= 0;
                $homeYellow= 0;
                $homeRed= 0;
            }    
            else {
                $homeFTHG= $row['SUM(matches.FTHG)'];               
                $homeShot= $row['SUM(matches.hShot)'];
                $homeShotTar= $row['SUM(matches.hShotTar)'];
                $homeFouls= $row['SUM(matches.hFouls)'];
                $homeCorners= $row['SUM(matches.hCorners)'];
                $homeYellow= $row['SUM(matches.hYellow)'];
                $homeRed= $row['SUM(matches.hRed)'];
            }
        }
        else {
            header("Location: index.php?error=dbError");
            exit();
        }

        if($result3 = mysqli_query($conn, $teamAway)){
            // user has away matches logged for favourite team
            $row = mysqli_fetch_assoc($result3);
            $awayNumMatches= $row['COUNT(user_matches.matchVal)'];

            if($awayNumMatches == 0){
                
                echo '<div class="errors">';
                echo '<p style="float:left;">No away matches logged for selected team!</p>';
                echo '</div>'; 
                $awayFTAG= 0;               
                $awayShot= 0;
                $awayShotTar= 0;
                $awayFouls= 0;
                $awayCorners= 0;
                $awayYellow= 0;
                $awayRed= 0;
            } 
            else { 
                $awayFTAG= $row['SUM(matches.FTAG)'];               
                $awayShot= $row['SUM(matches.aShot)'];
                $awayShotTar= $row['SUM(matches.aShotTar)'];
                $awayFouls= $row['SUM(matches.aFouls)'];
                $awayCorners= $row['SUM(matches.aCorners)'];
                $awayYellow= $row['SUM(matches.aYellow)'];
                $awayRed= $row['SUM(matches.aRed)'];
            }       
        }
        else {
            header("Location: index.php?error=dbError1");
            exit();
        } 
        $favNumMatches = $awayNumMatches + $homeNumMatches;
        $favGoals = $awayFTAG + $homeFTHG;               
        $favShot = $awayShot + $homeShot;
        $favShotTar = $awayShotTar + $homeShotTar;
        $favFouls = $awayFouls + $homeFouls;
        $favCorners = $awayCorners + $homeCorners;
        $favYellow = $awayYellow + $homeYellow;
        $favRed = $awayRed + $homeRed;

        if($result = mysqli_query($conn, $homeOpp)){
            // user has home matches logged for favourite team
            $row = mysqli_fetch_assoc($result);
            $opphomeNumMatches= $row['COUNT(user_matches.matchVal)'];
            // could add these if above isnt 0
            
            if($opphomeNumMatches == 0){
                
                $opphomeFTHG= 0;              
                $opphomeShot= 0;
                $opphomeShotTar= 0;
                $opphomeFouls= 0;
                $opphomeCorners= 0;
                $opphomeYellow= 0;
                $opphomeRed= 0;
            }    
            else {
                $opphomeFTHG= $row['SUM(matches.FTHG)'];               
                $opphomeShot= $row['SUM(matches.hShot)'];
                $opphomeShotTar= $row['SUM(matches.hShotTar)'];
                $opphomeFouls= $row['SUM(matches.hFouls)'];
                $opphomeCorners= $row['SUM(matches.hCorners)'];
                $opphomeYellow= $row['SUM(matches.hYellow)'];
                $opphomeRed= $row['SUM(matches.hRed)'];
            }
        }
        else {
            header("Location: index.php?error=dbError");
            exit();
        }

        if($result = mysqli_query($conn, $awayOpp)){
            // user has away matches logged for favourite team
            $row = mysqli_fetch_assoc($result);
            $oppawayNumMatches= $row['COUNT(user_matches.matchVal)'];

            if($oppawayNumMatches == 0){
                
                
                $oppawayFTAG= 0;               
                $oppawayShot= 0;
                $oppawayShotTar= 0;
                $oppawayFouls= 0;
                $oppawayCorners= 0;
                $oppawayYellow= 0;
                $oppawayRed= 0;
            } 
            else { 
                $oppawayFTAG= $row['SUM(matches.FTAG)'];               
                $oppawayShot= $row['SUM(matches.aShot)'];
                $oppawayShotTar= $row['SUM(matches.aShotTar)'];
                $oppawayFouls= $row['SUM(matches.aFouls)'];
                $oppawayCorners= $row['SUM(matches.aCorners)'];
                $oppawayYellow= $row['SUM(matches.aYellow)'];
                $oppawayRed= $row['SUM(matches.aRed)'];
            }       
        }
        else {
            header("Location: index.php?error=dbError1");
            exit();
        } 
        $oppNumMatches = $oppawayNumMatches + $opphomeNumMatches;
        $oppGoals = $oppawayFTAG + $opphomeFTHG;               
        $oppShot = $oppawayShot + $opphomeShot;
        $oppShotTar = $oppawayShotTar + $opphomeShotTar;
        $oppFouls = $oppawayFouls + $opphomeFouls;
        $oppCorners = $oppawayCorners + $opphomeCorners;
        $oppYellow = $oppawayYellow + $opphomeYellow;
        $oppRed = $oppawayRed + $opphomeRed;

        $teamWins = "SELECT * FROM (SELECT * FROM (SELECT COUNT(matches.matchId) AS homeWins FROM matches JOIN user_matches ON matches.matchId = user_matches.matchVal WHERE user_matches.userVal =".$_SESSION['defaultStatsUserId']." AND matches.homeTeam =".$_SESSION['defaultStatsId']." AND matches.FTHG > matches.FTAG AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') as homeTable JOIN (SELECT COUNT(matches.matchId) AS awayWins FROM matches JOIN user_matches ON matches.matchId = user_matches.matchVal WHERE user_matches.userVal =".$_SESSION['defaultStatsUserId']." AND matches.awayTeam =".$_SESSION['defaultStatsId']." AND matches.FTHG < matches.FTAG AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS awayTable) AS wins JOIN (SELECT * FROM (SELECT COUNT(matches.matchId) AS homeGames FROM matches JOIN user_matches ON matches.matchId = user_matches.matchVal WHERE user_matches.userVal =".$_SESSION['defaultStatsUserId']." AND matches.homeTeam =".$_SESSION['defaultStatsId']." AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') as homeTable JOIN (SELECT COUNT(matches.matchId) AS awayGames FROM matches JOIN user_matches ON matches.matchId = user_matches.matchVal WHERE user_matches.userVal =".$_SESSION['defaultStatsUserId']." AND matches.awayTeam =".$_SESSION['defaultStatsId']." AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS awayTable) AS games";
        if($result4 = mysqli_query($conn, $teamWins)){
            // user has away matches logged for favourite team
            $row = mysqli_fetch_assoc($result4);
            $favHomeWins = $row['homeWins'];
            $favAwayWins = $row['awayWins'];
            $favHomeGames = $row['homeGames'];
            $favAwayGames = $row['awayGames'];
            $favTotalWins = $favHomeWins + $favAwayWins;
        }
        else {
            header("Location: index.php?error=dbError1");
            exit();
        } 

        $homeWins = "SELECT COUNT(matches.matchId) FROM matches JOIN user_matches ON matches.matchId = user_matches.matchVal WHERE user_matches.userVal =".$_SESSION['defaultStatsUserId']." AND matches.result = 'H' AND date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."'";
        if($result5 = mysqli_query($conn, $homeWins)){

            $row = mysqli_fetch_assoc($result5);
            $TotalHomeWins = $row['COUNT(matches.matchId)'];
        }
        else {
            header("Location: index.php?error=dbError1");
            exit(); 
        }
    }
    else {
        header("Location: index.php?error=dbError1");
        exit(); 
    }