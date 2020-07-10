<?php 
    require "header.php"
?>

    <body>
        

        <div class="Border-main">
        <button class="jumpDel" id="maker">+</button>
        <?php
        
        require 'includes/dbh.php';
        require 'includes/getStats.php';
        if(!isset($_SESSION['userId'])){
            header("Location: index.php");
            exit();
        }
        echo '<script>
        function minmax(parent, button){
            if(document.getElementById(parent).style.display == "none"){
                document.getElementById(parent).style.display = "block";
                document.getElementById(button).innerHTML = "-"
            }
            else{
                document.getElementById(parent).style.display = "none";
                document.getElementById(button).innerHTML = "+"
            }
            
        }
        </script>';
        echo '<div class="errors">';
        if(isset($_GET['success'])){
            if($_GET['success'] == 'changed'){
                echo '<p style="color:green;">Table Updated!</p>';
            } 
        }
        echo '</div>';
        echo '<div class="rotate-message">Rotate Device</div>';

            echo '<div class="headers">';
            echo '<h3>My Stats</h3>';
            echo '<p>This is your statistics section, your stats are broken down into different sections with headings to show what each section contains. The page can be updated by team, by selected season and for people you follow using the "update" bar below. Press the plus button to expand each section.</p>';
            echo '<div class="jump" id="jumps" style="display:block;">';
                echo '<a href="myStats.php#Totals">Totals</a>';
                echo '<a href="myStats.php#Pergame">Per Game</a>';
                echo '<a href="myStats.php#MostSeen">Most Watched Team</a>';
                echo '<a href="myStats.php#seen%">% Watched</a>';               
                echo '<a href="myStats.php#Goals">Team Goals</a>';
                echo '<a href="myStats.php#Compare">Compare Graph</a>';
                echo '<a href="myStats.php#Home">Per Home Game</a>';
                echo '<a href="myStats.php#Away">Per Away Game</a>';
                echo '<a href="myStats.php#Opp">Opposition</a>';
                echo '<a href="myStats.php#AppsGraph">Teams Seen (Graph)</a>';
                echo '<a href="myStats.php#GoalGraph">Goals Seen (Graph)</a>';
                echo '<a href="myStats.php#AllGraph">GPG Graph</a>';
                echo '<a href="myStats.php#Notable" style="border-bottom:1px solid black">Notable Matches</a>';
                echo '<button id="hider" style="width: 50%; font-size: 1vw; font-family: "Roboto", sans-serif;">X</button>';

            echo '</div>';
            
            echo '<script>
            document.getElementById("hider").addEventListener("click", function(){
                minmax("jumps", "hider");
            });
            document.getElementById("maker").addEventListener("click", function(){
                document.getElementById("jumps").style.display = "block";
                document.getElementById("hider").innerHTML = "X"
            });
            </script>';
            if($_SESSION['defaultStatsUserId'] == $_SESSION['userId']){
                echo '<p>Showing stats for: <b>'.$_SESSION['defaultStatsName'].'</b>, <b>'.$_SESSION['dateTitle'].'</b>, <b>Your Stats</b></p>';
            }
            else {
                echo '<p>Showing stats for: <b>'.$_SESSION['defaultStatsName'].'</b>, <b>'.$_SESSION['dateTitle'].'</b>, <b>'.$_SESSION['defaultStatsUserEmail'].'</b></p>';             
            }
            echo '</div>';
            
            // get teams 
            $sqlTeams = "SELECT * FROM teams ORDER BY teamName";
            echo '<div class="changeBar">
                    <form action="includes/change.php" method="post">
                    <select name="teamChange">
                    
                        <option value="'.$_SESSION['defaultStatsName'].'">'.$_SESSION['defaultStatsName'].'</option>';
                        if($teamResult = mysqli_query($conn, $sqlTeams)){
                            // get rows
                            while($teamrows = mysqli_fetch_array($teamResult)){
                                // Generate each option for each team in DB
                                echo '<option value="'.$teamrows['teamName'].'">'.$teamrows['teamName'].'</option>';
                            }
                        }
                        else{
                            header("Location: index.php?error=dbError1");
                            exit(); 
                        }
                    echo '</select> 
                    <select name="DateChange">
                        <option value="'.$_SESSION['dateTitle'].'">'.$_SESSION['dateTitle'].'</option>
                        <option value="AllTime">All Time</option>
                        <option value="19/20">19/20</option>
                        <option value="18/19">18/19</option>
                        <option value="17/18">17/18</option>
                        <option value="16/17">16/17</option>
                        <option value="15/16">15/16</option>
                    </select>
                    <select name="UserChange" style="width:20%;">';
                        if($_SESSION['defaultStatsUserId'] == $_SESSION['userId']){
                            echo '<option value="'.$_SESSION['userId'].'">Your Stats</option>';
                        }
                        else{
                        echo '<option value="'.$_SESSION['defaultStatsUserId'].'">'.$_SESSION['defaultStatsUserEmail'].'</option>';
                        echo '<option value="'.$_SESSION['userId'].'" style="font-size:0.9vw;">Your Stats</option>';
                        } 
                        $following = "SELECT usersfollow.followerId, usersfollow.followingVal, users.email FROM usersfollow JOIN users ON usersfollow.followingVal = users.userId WHERE usersfollow.followerId = ".$_SESSION['userId']."";
                        if($followRes = mysqli_query($conn, $following)){
                                
                            while($followrow = mysqli_fetch_assoc($followRes)){
                                if($_SESSION['defaultStatsUserEmail'] != $followrow['email']){
                                    echo '<option value="'.$followrow['followingVal'].'" style="font-size:0.9vw;">'.$followrow['email'].' Stats</option>';
                                }
                            }
                        }
                        else{
                            header("Location: index.php?error=dbError1");
                            exit(); 
                        }
                    echo '</select>
                    <button type="submit" name="change-submit">Update</button>
                    </form>
                </div>';

            echo '<div class="headers">';
            echo '<a name="Totals" />';
            echo '<h3 style="text-decoration:underline; font-size:1.5vw; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Totals '.$_SESSION['dateTitle'].'</h3>';
            /////////////////////////////////////////////////////////////////////////////////////
            echo '<button class="btn-minimize" id="toggle">+</button>';
            echo '<div id="totals" style="display:none;">';
            echo '<script>
            document.getElementById("toggle").addEventListener("click", function(){
                minmax("totals", "toggle");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This section shows totals for matches you have seen.</b> On the left are totals for all teams and on the right are totals for your selected team.</p>';
            echo '</div>';
                echo '<div class="statsTable">';
                echo '<table>';
                        echo '<tr>
                            <th>All Matches Attended</th>
                            <th style="border-right:5px solid black">All Goals Seen</th>
                            <th>'.$_SESSION['defaultStatsName'].' Matches Attended</th>
                            <th>'.$_SESSION['defaultStatsName'].' Goals Seen</th>
                        </tr>';
                        echo '<tr>
                            <td>'.$totalNumMatches.'</td>
                            <td style="border-right:5px solid black">'.$totalAllGoals.'</td>
                            <td>'.$favNumMatches.'</td>
                            <td>'.$favGoals.'</td>
                        </tr>';
                        echo '<tr>
                            <th>All Shots Seen</th>
                            <th style="border-right:5px solid black">All Shots on Target Seen</th>
                            <th>'.$_SESSION['defaultStatsName'].' Shots Seen</th>
                            <th>'.$_SESSION['defaultStatsName'].' Shots on Target Seen</th>
                        </tr>';
                        echo '<tr>
                            <td>'.($totalaShot+$totalhShot).'</td>
                            <td style="border-right:5px solid black">'.($totalaShotTar+$totalhShotTar).'</td>
                            <td>'.$favShot.'</td>
                            <td>'.$favShotTar.'</td>
                        </tr>';
                        echo '<tr>
                            <th>Total Home Win %</th>
                            <th style="border-right:5px solid black">Total Shot Accuracy</th>
                            <th>'.$_SESSION['defaultStatsName'].' Win %</th>
                            <th>'.$_SESSION['defaultStatsName'].' Shot Accuracy</th>
                        </tr>';
                        echo '<tr>
                            <td>'.number_format((float)((($TotalHomeWins)/($totalNumMatches))*100), 2, '.', '').' %</td>
                            <td style="border-right:5px solid black">'.number_format((float)((($totalhShotTar+$totalaShotTar)/($totalaShot+$totalhShot))*100), 2, '.', '').' %</td>';
                            // cant divide by zero so need to check first
                            if($favNumMatches != 0 && $favTotalWins != 0){
                                $winperc = number_format((float)((($favTotalWins)/($favNumMatches))*100), 2, '.', '');
                            }
                            else{
                                $winperc = 0;
                            }
                        echo '<td>'.$winperc.' %</td>';
                            if($favShot != 0 && $favShotTar != 0){
                                $favShotAcc = number_format((float)((($favShotTar)/($favShot))*100), 2, '.', '');
                            }
                            else{
                                $favShotAcc = 0;
                            }
                        echo '<td>'.$favShotAcc.' %</td>
                        </tr>';
                        echo '<tr>
                            <th>All Corners Seen</th>
                            <th style="border-right:5px solid black">All Fouls Seen</th>
                            <th>'.$_SESSION['defaultStatsName'].' Corners Seen</th>
                            <th>'.$_SESSION['defaultStatsName'].' Fouls Seen</th>
                        </tr>';
                        echo '<tr>
                            <td>'.($totalaCorners+$totalhCorners).'</td>
                            <td style="border-right:5px solid black">'.($totalaFouls+$totalhFouls).'</td>
                            <td>'.$favCorners.'</td>
                            <td>'.$favFouls.'</td>
                        </tr>';
                        echo '<tr>
                        <th>All Yellow Cards Seen</th>
                        <th style="border-right:5px solid black">All Red Cards Seen</th>
                        <th>'.$_SESSION['defaultStatsName'].' Yellow Cards Seen</th>
                        <th>'.$_SESSION['defaultStatsName'].' Red Cards Seen</th>
                        </tr>';
                        echo '<tr>
                            <td>'.($totalaYellow+$totalhYellow).'</td>
                            <td style="border-right:5px solid black">'.($totalaRed+$totalhRed).'</td>
                            <td >'.$favYellow.'</td>
                            <td>'.$favRed.'</td>
                        </tr>';
                echo '</table>';
                echo '</div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';
            echo '<a name="Pergame" />';
            ///////////////////////////////////////////////////////////////////////////////
            // per game
            
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Per Game '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle2">+</button>';
            echo '<div id="pergame" style="display:none;">';
            echo '<script>
            document.getElementById("toggle2").addEventListener("click", function(){
                minmax("pergame", "toggle2");
            });
            </script>';   
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This section shows stats broken down per game.</b> On the top are stats per game for your selected team. On the bottom are stats per game for all teams.</p>';
            echo '</div>';

                echo '<div class="statsTableLower">';
                    echo '<table>';
                            echo '<tr>
                                <th>'.$_SESSION['defaultStatsName'].' Goals per Game</th>
                                <th>'.$_SESSION['defaultStatsName'].' Shots per Game</th>
                                <th>'.$_SESSION['defaultStatsName'].' Shots on Target per Game</th>
                                <th>'.$_SESSION['defaultStatsName'].' Corners per Game</th>
                            </tr>';
                            echo '<tr>';
                                if($favNumMatches != 0){
                                    // cant divide by 0
                                    $gpg = number_format((float)($favGoals/$favNumMatches), 2, '.', '');
                                    $spg = number_format((float)($favShot/$favNumMatches), 2, '.', '');
                                    $stpg = number_format((float)($favShotTar/$favNumMatches), 2, '.', '');
                                    $cpg = number_format((float)($favCorners/$favNumMatches), 2, '.', '');
                                    $foulpg = number_format((float)($favFouls/$favNumMatches), 2, '.', '');
                                    $redpg = number_format((float)($favRed/$favNumMatches), 2, '.', '');
                                    $yelpg = number_format((float)($favYellow/$favNumMatches), 2, '.', '');
                                    $favTotalCards = $favRed + $favYellow;
                                    $cardspg = number_format((float)($favTotalCards/$favNumMatches), 2, '.', '');;
                                }
                                else {
                                    $gpg = 0.00;
                                    $spg = 0.00;
                                    $stpg = 0.00;
                                    $cpg = 0.00;
                                    $foulpg = 0;
                                    $redpg = 0;
                                    $yelpg = 0;
                                    $cardspg = 0;
                                }
                                echo '<td>'.$gpg.'</td>';
                                echo '<td>'.$spg.'</td>';
                                echo '<td>'.$stpg.'</td>';
                                echo '<td>'.$cpg.'</td>
                            </tr>';
                            echo '<tr>
                                <th>'.$_SESSION['defaultStatsName'].' Fouls per Game</th>
                                <th>'.$_SESSION['defaultStatsName'].' Yellow Cards per Game</th>
                                <th>'.$_SESSION['defaultStatsName'].' Red Cards per Game</th>
                                <th>'.$_SESSION['defaultStatsName'].' Cards per Game</th>
                            </tr>';
                            echo '<tr>
                                <td style="border-bottom:5px solid black">'.$foulpg.'</td>
                                <td style="border-bottom:5px solid black">'.$yelpg.'</td>
                                <td style="border-bottom:5px solid black">'.$redpg.'</td>
                                <td style="border-bottom:5px solid black">'.$cardspg.'</td>
                            </tr>';
                            echo '<tr>
                                <th style="padding-top:10px">All Goals per Game</th>
                                <th style="padding-top:10px">All Shots per Game</th>
                                <th style="padding-top:10px">All Shots on Target per Game</th>
                                <th style="padding-top:10px">All Corners per Game</th>
                            </tr>';
                            echo '<tr>';
                                
                                    
                                $totalShot = $totalhShot + $totalaShot;
                                $totalShotTar = $totalhShotTar + $totalaShotTar;
                                $totalCorners = $totalhCorners + $totalaCorners;
                                $totalFouls = $totalhFouls + $totalaFouls;
                                $totalRed = $totalhRed + $totalaRed;
                                $totalYellow = $totalhYellow + $totalaYellow;
                                $tgpg = number_format((float)($totalAllGoals/$totalNumMatches), 2, '.', '');
                                $tspg = number_format((float)($totalShot/$totalNumMatches), 2, '.', '');
                                $tstpg = number_format((float)($totalShotTar/$totalNumMatches), 2, '.', '');
                                $tcpg = number_format((float)($totalCorners/$totalNumMatches), 2, '.', '');
                                $tfoulpg = number_format((float)($totalFouls/$totalNumMatches), 2, '.', '');
                                $tredpg = number_format((float)($totalRed/$totalNumMatches), 2, '.', '');
                                $tyelpg = number_format((float)($totalYellow/$totalNumMatches), 2, '.', '');
                                $totalCards = $totalRed + $totalYellow;
                                $tcardspg = number_format((float)($totalCards/$totalNumMatches), 2, '.', '');
                                
                                echo '<td>'.$tgpg.'</td>';
                                echo '<td>'.$tspg.'</td>';
                                echo '<td>'.$tstpg.'</td>';
                                echo '<td>'.$tcpg.'</td>
                            </tr>';
                            echo '<tr>
                                <th>All Fouls per Game</th>
                                <th>All Yellow Cards per Game</th>
                                <th>All Red Cards per Game</th>
                                <th>All Cards per Game</th>
                            </tr>';
                            echo '<tr>
                                <td>'.$tfoulpg.'</td>
                                <td>'.$tyelpg.'</td>
                                <td>'.$tredpg.'</td>
                                <td>'.$tcardspg.'</td>
                            </tr>';
                    echo '</table>';
                echo '</div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ////////////////////////////////////////////////////////
            // MOST SEEN TEAM
            echo '<a name="MostSeen" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Most Watched Team '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle3">+</button>';
            echo '<div id="mostSeen" style="display:none;">';
            echo '<script>
            document.getElementById("toggle3").addEventListener("click", function(){
                minmax("mostSeen", "toggle3");
            });
            </script>'; 

                echo '<div class="statsTableLower" style="width:30%; margin-left:35%; margin-right:35%">';
                echo '<table>';
                        echo '<tr>
                                <td>'.$mostSeen.'</td>';
                        echo '</tr>
                    </table>
                </div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ////////////////////////////////////////////////////////////////
            // % seen 
            echo '<a name="seen%" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">% of '.$_SESSION['defaultStatsName'].' Matches Watched '.$_SESSION['dateTitle'].'</h3>';
            if($totalTeamGames == 0){
                $seenperc = 0;
            }
            else {
                $seenperc = number_format((float)(($favNumMatches/$totalTeamGames)*100), 2, '.', '');
            }
            echo '<button class="btn-minimize" id="toggle4">+</button>';
            echo '<div id="seen" style="display:none;">';
            echo '<script>
            document.getElementById("toggle4").addEventListener("click", function(){
                minmax("seen", "toggle4");
            });
            </script>'; 
                echo '<div class="statsTableLower" style="width:30%; margin-left:35%; margin-right:35%">';
                echo '<table>';
                        echo '<tr>
                                <td>'.$seenperc.' %</td>';
                        echo '</tr>
                    </table>
                </div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ////////////////////////////////////////////////////////////////////////
            // goals 
            echo '<a name="Goals" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">'.$_SESSION['defaultStatsName'].' '.$_SESSION['dateTitle'].' Goals For And Against</h3>';
            echo '<button class="btn-minimize" id="toggle5">+</button>';
            echo '<div id="goals" style="display:none;">';
            echo '<script>
            document.getElementById("toggle5").addEventListener("click", function(){
                minmax("goals", "toggle5");
            });
            </script>'; 
                echo '<div class="statsTableLower" style="margin-left:20%; margin-right:20%;">';
                    echo '<table>';
                        echo '<tr>
                            <th>Goals For</th>
                            <th style="width:0.4%"> </th>
                            <th style="border-right:5px solid black">Goals Against</th>
                            <th>Goal Difference </th>
                        </tr>';
                        echo '<tr>
                            <td>'.$favGoals.'</td>
                            <td style="width:0.4%">-</td>
                            <td style="border-right:5px solid black">'.$oppGoals.'</td> 
                            <td>'.($favGoals - $oppGoals).' </td>   
                        </tr>';
                    echo '</table>';
                echo '</div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ////////////////////////////////////////////////////////////////////////////////
            // team graph 
            echo '<a name="Compare" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">'.$_SESSION['defaultStatsName'].' Wins, Draws and Losses (Comparison Table) '.$_SESSION['dateTitle'].'</h3>';
            $teamArray = Array();
            $teamArray[] = $_SESSION['defaultStatsName'];
            echo '<button class="btn-minimize" id="toggle6">+</button>';
            echo '<div id="compareGraph" style="display:none;">';
            echo '<script>
            document.getElementById("toggle6").addEventListener("click", function(){
                minmax("compareGraph", "toggle6");
            });
            </script>'; 
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This section is a graph which shows your selected teams Wins, Draws and Losses.</b> Use the bar at the bottom to add another team to compare against. Graphs are interactive and bars can be toggled by pressing the keys at the top. </p>';
            echo '</div>';

                echo '<div class="bar"><canvas id="teamGraph" width="90%" height="37vw"></canvas>';
                echo '<div class="changeBar">
                        <form action="myStats.php#Goals" method="post" style="margin-left:50%">
                        <select name="graphChange" style="margin-top:1%">';
                foreach($nameArray as $name){
                    if($name != $_SESSION['defaultStatsName']){
                        echo '<option value="'.$name.'">'.$name.'</option>';
                    }
                    
                }
                echo '</select>
                
                <button type="submit" id="comparer" name="graphChange-submit" style="width:30%;">Add Team</button>

                </form>
                </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>';
            echo '</div>';
            

            if(isset($_POST['graphChange-submit'])){
                // Jumps here when changing team
                $changeName = $_POST['graphChange'];
                
                $teamgetter = "SELECT teamId FROM teams WHERE teamName='".$changeName."'";
                $getName = mysqli_query($conn, $teamgetter);
                $output = mysqli_fetch_assoc($getName);
                $teamId = $output['teamId'];
                // got teamId

                $addGraph = "SELECT (Awin + Hwin) AS wins, (Adraw + Hdraw) AS draws, (ALoss + HLoss) AS loss FROM (SELECT * FROM (SELECT * FROM (SELECT COUNT(matches.result) AS Hwin FROM matches JOIN user_matches WHERE matches.homeTeam = ".$teamId." AND matches.result = 'H' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['userId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS homeWins JOIN (SELECT COUNT(matches.result) AS Hdraw FROM matches JOIN user_matches WHERE matches.homeTeam = ".$teamId." AND matches.result = 'D' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['userId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS homeDraws JOIN (SELECT COUNT(matches.result) AS HLoss FROM matches JOIN user_matches WHERE matches.homeTeam = ".$teamId." AND matches.result = 'A' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['userId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS homeLoss) AS home JOIN (SELECT * FROM (SELECT COUNT(matches.result) AS Awin FROM matches JOIN user_matches WHERE matches.awayTeam = ".$teamId." AND matches.result = 'A' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['userId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS awayWins JOIN (SELECT COUNT(matches.result) AS Adraw FROM matches JOIN user_matches WHERE matches.awayTeam = ".$teamId." AND matches.result = 'D' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['userId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS awayDraws JOIN (SELECT COUNT(matches.result) AS ALoss FROM matches JOIN user_matches WHERE matches.awayTeam = ".$teamId." AND matches.result = 'H' AND matches.matchId = user_matches.matchVal AND user_matches.userVal = ".$_SESSION['userId']." AND matches.date BETWEEN '".$_SESSION['dateStart']."' AND '".$_SESSION['dateEnd']."') AS AwayLoss) AS away) AS games";
                if($addResult = mysqli_query($conn, $addGraph)){
                    $newrowgraph = mysqli_fetch_assoc($addResult);
                    // add to arrays
                    $winArray[] = $newrowgraph['wins'];
                    $drawArray[] = $newrowgraph['draws'];
                    $lossArray[] = $newrowgraph['loss'];
                    $teamArray[] = $changeName;
                    echo '<script>
                    document.getElementById("compareGraph").style.display = "block";
                    document.getElementById("toggle6").innerHTML = "-";
                    </script>';
    
                }
                else {
                    header("Location: index.php?error=dbError1");
                    exit(); 
                }
    
            
                
            }

            echo "<script>
                
            // easier to work with java arrays
            var wins = "; echo json_encode($winArray); echo";
            var draws = "; echo json_encode($drawArray); echo";
            var loss = "; echo json_encode($lossArray); echo";
            var teams = "; echo json_encode($teamArray); echo";

            var ctx = document.getElementById('teamGraph').getContext('2d');
            var teamCompare = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: teams,
                    datasets: [{
                        label: '# Of Wins'
                        ,data: wins,
                        backgroundColor: '#77dd77 ',
                        hoverBackgroundColor: '#00ff00 ',
                        borderColor: '#77dd77',
                        
                        borderWidth: 1
                    },
                    {
                        label: '# Of Draws'
                        ,data: draws,
                        backgroundColor: '#A9A9A9  ',
                        hoverBackgroundColor: '#808080  ',
                        hoverBorderColor: '#808080',
                        borderColor: '#A9A9A9 ',
                        borderWidth: 1
                    },
                    {
                        label: '# Of Losses'
                        ,data: loss,
                        backgroundColor: '#ff6961 ',
                        hoverBackgroundColor: '#ff0000  ',
                        borderColor: '#ff6961',
                        borderWidth: 1
                    }
                    ]
                },
                options: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: false,
                        text: 'Team Wins, Draws and Losses (Comparison Table) ".$_SESSION['dateTitle']."',
                        fontSize: 25
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }]
                    }
                }
            });

            document.getElementById('comparer').addEventListener('click', function(){
                teamCompare.update();
            });
            </script>";
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            //////////////////////////////////////////////////////////////////////////////////////////////////
            // HOME TABLE  
            echo '<a name="Home" />';     
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">'.$_SESSION['defaultStatsName'].' Per Home Game '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle7">+</button>';
            echo '<div id="home" style="display:none;">';
            echo '<script>
            document.getElementById("toggle7").addEventListener("click", function(){
                minmax("home", "toggle7");
            });
            </script>'; 
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This section shows stats per home game for your selected team.</b> If you have no home matches logged the stats will display 0.</p>';
            echo '</div>';

                echo '<div class="statsTableLower">';
                    echo '<table>';
                        echo '<tr>
                            <th>Number of Games</th>
                            <th>Number of Wins</th>
                            <th>Win %</th>
                            <th>Shot Accuracy</th>
                        </tr>';
                        echo '<tr>
                            <td >'.$favHomeGames.'</td>
                            <td >'.$favHomeWins.'</td>';
                            if($favHomeGames != 0){
                                $homeWinPer = number_format((float)((($favHomeWins)/($favHomeGames))*100), 2, '.', '');
                            } 
                            else {
                                $homeWinPer = 0;
                            }

                            if($homeShot != 0){
                                $homeShotAcc = number_format((float)((($homeShotTar)/($homeShot))*100), 2, '.', '');
                            }
                            else{
                                $homeShotAcc = 0;
                            }
                            echo '<td >'.$homeWinPer.' %</td>
                            <td >'.$homeShotAcc.' %</td>
                        </tr>';
                        echo '<tr>
                            <th>Goals per Game</th>
                            <th>Shots per Game</th>
                            <th>Shots on Target per Game</th>
                            <th>Corners per Game</th>
                        </tr>';
                        echo '<tr>';
                            if($favHomeGames != 0){
                                // cant divide by 0
                                $gphg = number_format((float)($homeFTHG/$favHomeGames), 2, '.', '');
                                $sphg = number_format((float)($homeShot/$favHomeGames), 2, '.', '');
                                $stphg = number_format((float)($homeShotTar/$favHomeGames), 2, '.', '');
                                $cphg = number_format((float)($homeCorners/$favHomeGames), 2, '.', '');
                                $foulphg = number_format((float)($homeFouls/$favHomeGames), 2, '.', '');
                                $redphg = number_format((float)($homeRed/$favHomeGames), 2, '.', '');
                                $yelphg = number_format((float)($homeYellow/$favHomeGames), 2, '.', '');
                                $totalcardshome = $homeRed + $homeYellow;
                                $cardsphg = number_format((float)($totalcardshome/$favHomeGames), 2, '.', '');;
                            }
                            else {
                                $gphg = 0.00;
                                $sphg = 0.00;
                                $stphg = 0.00;
                                $cphg = 0.00;
                                $foulphg = 0;
                                $redphg = 0;
                                $yelphg = 0;
                                $cardsphg = 0;
                            }
                            echo '<td>'.$gphg.'</td>';
                            echo '<td>'.$sphg.'</td>';
                            echo '<td>'.$stphg.'</td>';
                            echo '<td>'.$cphg.'</td>
                        </tr>';
                        echo '<tr>
                            <th>Fouls per Game</th>
                            <th>Yellow Cards per Game</th>
                            <th>Red Cards per Game</th>
                            <th>Cards per Game</th>
                        </tr>';
                        echo '<tr>
                            <td >'.$foulphg.'</td>
                            <td >'.$yelphg.'</td>
                            <td >'.$redphg.'</td>
                            <td >'.$cardsphg.'</td>
                            </tr>';
                    echo '</table>';
                echo '</div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ////////////////////////////////////////////////////////////
            // AWAY TABLE 
            echo '<a name="Away" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">'.$_SESSION['defaultStatsName'].' Per Away Game '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle8">+</button>';
            echo '<div id="away" style="display:none;">';
            echo '<script>
            document.getElementById("toggle8").addEventListener("click", function(){
                minmax("away", "toggle8");
            });
            </script>'; 
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This section shows stats per away game for your selected team.</b> If you have no away matches logged the stats will display 0.</p>';
            echo '</div>';

                echo '<div class="statsTableLower">';
                    echo '<table>';
                        echo '<tr>
                            <th>Number of Games</th>
                            <th>Number of Wins</th>
                            <th>Win %</th>
                            <th>Shot Accuracy</th>
                        </tr>';
                        echo '<tr>
                            <td >'.$favAwayGames.'</td>
                            <td >'.$favAwayWins.'</td>';
                            if($favAwayGames != 0){
                                $awayWinPer = number_format((float)((($favAwayWins)/($favAwayGames))*100), 2, '.', '');
                            } 
                            else {
                                $awayWinPer = 0;
                            }

                            if($awayShot != 0){
                                $awayShotAcc = number_format((float)((($awayShotTar)/($awayShot))*100), 2, '.', '');
                            }
                            else{
                                $awayShotAcc = 0;
                            }
                            echo '<td >'.$awayWinPer.' %</td>
                            <td >'.$awayShotAcc.' %</td>
                        </tr>';
                        echo '<tr>
                            <th>Goals per Game</th>
                            <th>Shots per Game</th>
                            <th>Shots on Target per Game</th>
                            <th>Corners per Game</th>
                        </tr>';
                        echo '<tr>';
                            if($favAwayGames != 0){
                                // cant divide by 0
                                $gpag = number_format((float)($awayFTAG/$favAwayGames), 2, '.', '');
                                $spag = number_format((float)($awayShot/$favAwayGames), 2, '.', '');
                                $stpag = number_format((float)($awayShotTar/$favAwayGames), 2, '.', '');
                                $cpag = number_format((float)($awayCorners/$favAwayGames), 2, '.', '');
                                $foulpag = number_format((float)($awayFouls/$favAwayGames), 2, '.', '');
                                $redpag = number_format((float)($awayRed/$favAwayGames), 2, '.', '');
                                $yelpag = number_format((float)($awayYellow/$favAwayGames), 2, '.', '');
                                $totalcardsaway = $awayRed + $awayYellow;
                                $cardspag = number_format((float)($totalcardsaway/$favAwayGames), 2, '.', '');;
                            }
                            else {
                                $gpag = 0.00;
                                $spag = 0.00;
                                $stpag = 0.00;
                                $cpag = 0.00;
                                $foulpag = 0;
                                $redpag = 0;
                                $yelpag = 0;
                                $cardspag = 0;
                            }
                            echo '<td>'.$gpag.'</td>';
                            echo '<td>'.$spag.'</td>';
                            echo '<td>'.$stpag.'</td>';
                            echo '<td>'.$cpag.'</td>
                        </tr>';
                        echo '<tr>
                            <th>Fouls per Game</th>
                            <th>Yellow Cards per Game</th>
                            <th>Red Cards per Game</th>
                            <th>Cards per Game</th>
                        </tr>';
                        echo '<tr>
                            <td >'.$foulpag.'</td>
                            <td >'.$yelpag.'</td>
                            <td >'.$redpag.'</td>
                            <td >'.$cardspag.'</td>
                            </tr>';
                    echo '</table>';
                echo '</div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ///////////////////////////////////////////////////////////////////////////
            // opposition table
            echo '<a name="Opp" />'; 
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Against '.$_SESSION['defaultStatsName'].' '.$_SESSION['dateTitle'].' (Oppostion Stats)</h3>';
            echo '<button class="btn-minimize" id="toggleopp">+</button>';
            echo '<div id="opp" style="display:none;">';
            echo '<script>
            document.getElementById("toggleopp").addEventListener("click", function(){
                minmax("opp", "toggleopp");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This section shows stats for the opposition of your selected team.</b> This table can be used to see how effective your team is defensively.</p>';
            echo '</div>';

                echo '<div class="statsTableLower">';
                    echo '<table>';
                        echo '<tr>
                            <th>Opposition Goals</th>
                            <th>Opposition Shots</th>
                            <th>Opposition Shots on Target</th>
                            <th>Opposition Shot Accuracy</th>
                        </tr>';
                        echo '<tr>
                            <td >'.$oppGoals.'</td>
                            <td >'.$oppShot.'</td>
                            <td >'.$oppShotTar.'</td>';
                            if($oppShot != 0 && $oppShotTar != 0){
                                $oppShotAcc = number_format((float)((($oppShotTar)/($oppShot))*100), 2, '.', '');
                            }
                            else{
                                $oppShotAcc = 0;
                            }
                        echo '<td>'.$oppShotAcc.' %</td>';
                            
                        echo '</tr>';
                        echo '<tr>
                            <th>Opposition Corners</th>
                            <th>Opposition Yellow cards </th>
                            <th>Opposition Red cards</th>
                            <th>Opposition Fouls</th>
                        </tr>';
                        echo '<tr>
                            <td >'.$oppCorners.'</td>
                            <td >'.$oppYellow.'</td>
                            <td >'.$oppRed.'</td>
                            <td >'.$oppFouls.'</td>';
                        echo  '</tr>';
                        if($oppNumMatches != 0){
                            // cant divide by 0
                            $oppgpg = number_format((float)($oppGoals/$oppNumMatches), 2, '.', '');
                            $oppspg = number_format((float)($oppShot/$oppNumMatches), 2, '.', '');
                            $oppstpg = number_format((float)($oppShotTar/$oppNumMatches), 2, '.', '');
                            $oppcpg = number_format((float)($oppCorners/$oppNumMatches), 2, '.', '');
                            
                            
                        }
                        else {
                            $oppgpg = 0.00;
                            $oppspg = 0.00;
                            $oppstpg = 0.00;
                            $oppcpg = 0.00;
                            
                        }
                        echo '<tr>
                            <th>Opposition Goals Per Game</th>
                            <th>Opposition Shots Per Game </th>
                            <th>Opposition Shots on Target Per Game</th>
                            <th>Opposition Corners Per Game</th>
                        </tr>';
                        echo '<tr>
                            <td >'.$oppgpg.'</td>
                            <td >'.$oppspg.'</td>
                            <td >'.$oppstpg.'</td>
                            <td >'.$oppcpg.'</td>';
                        echo  '</tr>';
                    echo '</table>';
                echo '</div>';
            echo '</div>';
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ///////////////////////////////////////////////////////////////////////////////
            echo '<a name="AppsGraph" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Number of Matches Attended Per Team '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle9">+</button>';
            echo '<div id="appsGraph" style="display:none;">';
            echo '<script>
            document.getElementById("toggle9").addEventListener("click", function(){
                minmax("appsGraph", "toggle9");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This graph shows the number of matches you have attended per team.</b> Teams can be added and removed using the buttons at the bottom.</p>';
            echo '</div>';

                echo '<div class="bar"><canvas id="canvas" width="90%" height="37vw"></canvas>
                
                <div class="barbut">
                <button type="button" id="remove">Remove Data</button>
                <button type="button" id="add">Add Data</button></div></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>';
            echo '</div>';
            echo "<script>
            
            var ctx = document.getElementById('canvas').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels:"; echo json_encode($nameArray);
                    echo ",
                    datasets: [{
                        label: '# Of Games'
                        ,data:";  echo json_encode($matchArray);echo ",
                        backgroundColor:";
                            echo json_encode($colourArray);
                        echo ",
                        borderColor:";
                        echo json_encode($colourArray);
                        echo ",
                        borderWidth: 1
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Number of Matches Attended Per Team ".$_SESSION['dateTitle']."',
                        fontSize: 25
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }]
                    }
                }
            });

            var storeLab = [];
            var storeDat = [];
            var emptyArray = [];

            document.getElementById('add').addEventListener('click', function(){
                if(storeLab.length == 0){
                    myChart.update();
                }
                else{
                    myChart.data.labels.push(storeLab.pop());
                    
                    myChart.data.datasets.forEach((dataset) => {
                        dataset.data.push(storeDat.pop());
                        
                    });
                    myChart.update();
                }
            });
            
            document.getElementById('remove').addEventListener('click', function(){
                if(myChart.data.labels.length == 0){
                    myChart.update();
                }
                else{
                    storeLab.push(myChart.data.labels.pop());
                    myChart.data.datasets.forEach((dataset) => {
                        storeDat.push(dataset.data.pop());
                    });
                
                    myChart.update();
                }
            });
            
            </script>";
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            //////////////////////////////////////////////////////////////////////////
            echo '<a name="GoalGraph" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Number of Goals Seen Per Team '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle10">+</button>';
            echo '<div id="goalGraph" style="display:none;">';
            echo '<script>
            document.getElementById("toggle10").addEventListener("click", function(){
                minmax("goalGraph", "toggle10");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This graph shows the number of goals you have seen per team.</b> Teams can be added and removed using the buttons at the bottom, and you can change the graph from a circle to a semi-circle.</p>';
            echo '</div>';

                echo '<div class="bar"><canvas id="canvas2" width="90%" height="30vw"></canvas>
                <div class="barbut">
                <button type="button" id="remove2" style="float:left; margin-right:0.4%">Remove Data</button>
                <button type="button" id="add2"  style="float:left">Add Data</button>
                <button type="button" id="semi" style="margin-left:-18%; margin-top:6%">Semi/Full Circle</button></div></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>';
            echo '</div>';
            echo "<script>
            
            var optionsPie = {
                tooltipTemplate: '<%= label %> - <%= value %>'
            }

            var ctx = document.getElementById('canvas2').getContext('2d');
            var doughnut = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels:"; echo json_encode($nameArray);
                    echo ",
                    datasets: [{
                        data:";  echo json_encode($goalArray);echo ",
                        backgroundColor:";
                            echo json_encode($colourArray);
                        echo ",
                        borderColor:";
                        echo json_encode($colourArray);
                        echo ",
                        borderWidth: 1
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Number of Goals Seen Per Team ".$_SESSION['dateTitle']."',
                        fontSize: 25
                    }
                    
                }
            });

            var storeLab2 = [];
            var storeDat2 = [];
            var emptyArray2 = [];

            document.getElementById('add2').addEventListener('click', function(){
                if(storeLab2.length == 0){
                    doughnut.update();
                }
                else{
                    doughnut.data.labels.push(storeLab2.pop());
                    
                    doughnut.data.datasets.forEach((dataset) => {
                        dataset.data.push(storeDat2.pop());
                        
                    });
                    doughnut.update();
                }
            });
            
            document.getElementById('remove2').addEventListener('click', function(){
                if(doughnut.data.labels.length == 0){
                    doughnut.update();
                }
                else{
                    storeLab2.push(doughnut.data.labels.pop());
                    doughnut.data.datasets.forEach((dataset) => {
                        storeDat2.push(dataset.data.pop());
                    });
                
                    doughnut.update();
                }
            });

            document.getElementById('semi').addEventListener('click', function(){
                if (window.doughnut.options.circumference === Math.PI) {
                    window.doughnut.options.circumference = 2 * Math.PI;
                    window.doughnut.options.rotation = -Math.PI / 2;
                } else {
                    window.doughnut.options.circumference = Math.PI;
                    window.doughnut.options.rotation = -Math.PI;
                }
    
                window.doughnut.update();
            });

            </script>";
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            /////////////////////////////////////////////////////////////////////////////
            echo '<a name="AllGraph" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Matches, Goals and Goals Per Game Per Team '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle11">+</button>';
            echo '<div id="allGraph" style="display:none;">';
            echo '<script>
            document.getElementById("toggle11").addEventListener("click", function(){
                minmax("allGraph", "toggle11");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This graph shows the number of matches, goals and goals per game for teams you have seen.</b> Teams can be added and removed using the buttons at the bottom, and bars can be removed by clicking the keys at the top.</p>';
            echo '</div>';

                echo '<div class="bar"><canvas id="canvas3" width="85%" height="35vw"></canvas>
                
                <div class="barbut">
                <button type="button" id="remove3">Remove Data</button>
                <button type="button" id="add3">Add Data</button></div></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>';
            echo '</div>';
            echo "<script>
            
            var ctx = document.getElementById('canvas3').getContext('2d');
            var all = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels:"; echo json_encode($nameArray);
                    echo ",
                    datasets: [{
                        label: '# Of Games'
                        ,data:";  echo json_encode($matchArray);echo ",
                        backgroundColor: '#ffb347 ',
                        hoverBackgroundColor: '#ffa500',
                        hoverBorderColor: '#ffa500',
                        borderColor: '#ffb347 ',
                        borderWidth: 1
                    },
                    {
                        label: '# Of Goals'
                        ,data:";  echo json_encode($goalArray);echo ",
                        backgroundColor: '#b19cd9 ',
                        hoverBackgroundColor: '#6a0dad  ',
                        borderColor: '#b19cd9',
                        borderWidth: 1
                    },
                    {
                        label: '# Of Goals Per Game'
                        ,data:";  echo json_encode($gpgArray);echo ",
                        backgroundColor: '#77dd77 ',
                        hoverBackgroundColor: '#00ff00 ',
                        borderColor: '#77dd77',
                        borderWidth: 1
                    }
                    ]
                },
                options: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: false,
                        text: 'Matches, Goals and Goals Per Game Per Team ".$_SESSION['dateTitle']."',
                        fontSize: 25
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }]
                    }
                }
            });

            var storeLab3 = [];
            var storeDat3 = [];
            
            

            document.getElementById('add3').addEventListener('click', function(){
                if(storeLab3.length == 0){
                    all.update();
                }
                else{
                    all.data.labels.push(storeLab3.pop());
                    // fixes ordering 
                    var a = storeDat3[storeDat3.length-1];
                    storeDat3[storeDat3.length-1] = storeDat3[storeDat3.length-3];
                    storeDat3[storeDat3.length-3] = a;
                    all.data.datasets.forEach((dataset) => {
                        dataset.data.push(storeDat3.pop());
                        
                        
                    });
                    all.update();
                }
            });
            
            document.getElementById('remove3').addEventListener('click', function(){
                if(all.data.labels.length == 0){
                    all.update();
                }
                else{
                    storeLab3.push(all.data.labels.pop());
                    all.data.datasets.forEach((dataset) => {
                        storeDat3.push(dataset.data.pop());
                        
                    });
                
                    
                    
                
                    all.update();
                }
            });

            
            
            </script>";
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ///////////////////////////////////////////////////////////////////////////////////////////
            echo '<a name="Notable" />';
            echo '<h3 style="font-size:2vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Notable Matches </h3>'; 
            echo '<button class="btn-minimize" id="toggle12">+</button>';
            echo '<div id="notable" style="display:none;">';
            echo '<script>
            document.getElementById("toggle12").addEventListener("click", function(){
                minmax("notable", "toggle12");
            });
            </script>';
                $getMatches = "SELECT user_matches.userVal, games.matchId, games.date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId GROUP BY hometable.matchId) AS games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal = ".$_SESSION['defaultStatsUserId']." ORDER BY games.date DESC";
                if($result5 = mysqli_query($conn, $getMatches)){
                    $numResults = mysqli_num_rows($result5);
                    $counter = 0;
        
                    while($row = mysqli_fetch_assoc($result5)){
                        if($counter == 0){
                            //first row
                            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Most Recent Match</h3>';

                            echo '<div class="matches">';
                            echo '<table>
                                    <tr>';
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td>". $row['home'] . "</td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td style='border-right:none'>" . $row['away'] . "</td>";
                                echo '</tr>
                                </table>';
                            echo '</div>';
                            $counter++;

                        }
                        else if (++$counter == $numResults) {
                            // last row
                            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">First Match Logged</h3>';

                            echo '<div class="matches">';
                            echo '<table>
                                    <tr>';
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td>". $row['home'] . "</td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td style='border-right:none'>" . $row['away'] . "</td>";
                                echo '</tr>
                                </table>';
                            echo '</div>';
                        }
                    }
                }
                else{
                    header("Location: index.php?error=dbError1");
                    exit();
                }
                // highest scoring game 
                $highestScoring = "SELECT user_matches.userVal, games.matchId, games.date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId GROUP BY hometable.matchId) AS games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal =".$_SESSION['defaultStatsUserId']." ORDER BY (games.FTHG + games.FTAG) DESC ,games.FTHG DESC";
                if($result6 = mysqli_query($conn, $highestScoring)){
                    $high = mysqli_fetch_assoc($result6);

                    echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Highest Scoring Match</h3>';

                    echo '<div class="matches">';
                    echo '<table>
                            <tr>';
                            echo "<td>" . $high['date'] . "</td>";
                            echo "<td>". $high['home'] . "</td>";
                            echo "<td>" . $high['FTHG'] . "</td>";
                            echo "<td>" . $high['FTAG'] . "</td>";
                            echo "<td style='border-right:none'>" . $high['away'] . "</td>";
                        echo '</tr>
                        </table>';
                    echo '</div>';
                }
                else {
                    header("Location: index.php?error=dbError1");
                    exit();
                }
                // worst discipline game
                $mostCards = "SELECT user_matches.userVal, games.matchId, games.date, games.home, games.FTHG, games.FTAG, games.away, (games.hRed + games.aRed) AS reds,  (games.hYellow + games.aYellow) AS yellows, ((games.hYellow + games.aYellow) + (games.hRed + games.aRed)) AS cards FROM (SELECT hometable.matchId, hometable.date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away, hometable.hRed, hometable.aRed, hometable.hYellow, hometable.aYellow FROM (SELECT matches.matchId, matches.date, teams.teamName AS home, matches.FTHG, matches.FTAG, matches.hRed, matches.hYellow, matches.aYellow, matches.aRed FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) AS hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) AS awaytable ON hometable.matchId = awaytable.matchId GROUP BY hometable.matchId) AS games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal =".$_SESSION['defaultStatsUserId']." ORDER BY cards DESC, reds DESC, yellows DESC";
                if($result7 = mysqli_query($conn, $mostCards)){
                    $cards = mysqli_fetch_assoc($result7);

                    echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Most Cards in a Match</h3>';

                    echo '<div class="matches">';
                    echo '<table>
                            <tr>';
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th style='border-bottom:2px solid black; font-size:1vw;'>Red Cards</th>";
                                echo "<th style='border-bottom:2px solid black; font-size:1vw;'>Yellow Cards</th>";
                                echo "<th style='border-right:none; border-bottom:2px solid black; font-size:1vw;'>Total Cards</th>";
                            echo '</tr>';

                            echo '<tr>';
                                echo "<td>" . $cards['date'] . "</td>";
                                echo "<td>". $cards['home'] . "</td>";
                                echo "<td>" . $cards['FTHG'] . "</td>";
                                echo "<td>" . $cards['FTAG'] . "</td>";
                                echo "<td>" . $cards['away'] . "</td>";
                                echo "<td>" . $cards['reds'] . "</td>";
                                echo "<td>" . $cards['yellows'] . "</td>";
                                echo "<td style='border-right:none'>" . $cards['cards'] . "</td>";
                        echo '</tr>
                        </table>';
                    echo '</div>';
                echo '</div>';
            }
            else {
                header("Location: index.php?error=dbError1");
                exit();
            } 
            echo '</div>';  
        ?>
        </div>
    </body>

<?php 
    require "footer.php"
?>