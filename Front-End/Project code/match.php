<?php 
    require "header.php"
?>

    <body>
        <div class="Border-main">
        <?php 
        require 'includes/dbh.php';

        if(isset($_POST['matchId'])){

            $matchId = $_POST['matchId'];
            $returnPos = $_POST['pos'];
            $returnPage = $_POST['page'];

            $sqlMatch = "SELECT * FROM matches WHERE matchId = ".$matchId."";
            if($result = mysqli_query($conn, $sqlMatch)){
                //if matches logged
                $row = mysqli_fetch_assoc($result);

                $homeTeamId = $row['homeTeam']; 
                $awayTeamId = $row['awayTeam']; 

                $teamgetter = "SELECT teamId, teamName FROM teams WHERE teamId=".$homeTeamId."";
                $result = mysqli_query($conn, $teamgetter);
                $row2 = mysqli_fetch_assoc($result);
                $homeTeam = $row2['teamName'];

                $teamgetter = "SELECT teamId, teamName FROM teams WHERE teamId=".$awayTeamId."";
                $result = mysqli_query($conn, $teamgetter);
                $row2 = mysqli_fetch_assoc($result);
                $awayTeam = $row2['teamName'];


                $date = $row['date']; 
                $FTHG= $row['FTHG'];     
                $FTAG= $row['FTAG'];            
                $hShot= $row['hShot'];
                $aShot= $row['aShot'];
                $hShotTar= $row['hShotTar'];
                $aShotTar= $row['aShotTar'];
                $hFouls= $row['hFouls'];
                $aFouls= $row['aFouls'];
                $hCorners= $row['hCorners'];
                $aCorners= $row['aCorners'];
                $hYellow= $row['hYellow'];
                $aYellow= $row['aYellow'];
                $hRed= $row['hRed'];
                $aRed= $row['aRed'];

                if($hShot != 0){
                    $hShotAcc = number_format((float)((($hShotTar)/($hShot))*100), 2, '.', '');
                }
                else{
                    $hShotAcc = 0;
                }

                if($aShot != 0){
                    $aShotAcc = number_format((float)((($aShotTar)/($aShot))*100), 2, '.', '');
                }
                else{
                    $aShotAcc = 0;
                }

                if($hShotAcc != 0){
                    $hShotConv = number_format((float)((($FTHG)/($hShotTar))*100), 2, '.', '');
                }
                else{
                    $hShotConv = 0;
                }

                if($aShotAcc != 0){
                    $aShotConv = number_format((float)((($FTAG)/($aShotTar))*100), 2, '.', '');
                }
                else{
                    $aShotConv = 0;
                }


                echo '<div class="rotate-message">Rotate Device</div>';
                // add log match, check if logged first then if not show button
                
                echo '<div class="return">';
                    echo '<a href="'.$returnPage.'.php#'.$returnPos.'">Back</a>';
                echo '</div>';

                $teamCheck = "SELECT COUNT(matchVal) FROM user_matches WHERE matchVal = ".$matchId." AND userVal = ".$_SESSION['userId']."";
                if($check = mysqli_query($conn, $teamCheck)){
                    $rowcheck = mysqli_fetch_assoc($check);
                    $totalNumMatches= $rowcheck['COUNT(matchVal)'];
                    if($totalNumMatches == 0){
                        //match not logged
                        echo "
                        <div class='adder'>
                            <form action='includes/insert.php' method='post'>
                                <input type='hidden' name='pos' value='".$returnPos."'>
                                <button type='submit' name='matchId' value=".$matchId." >Log Match</button>
                            </form>
                        </div>";                        

                    }
                    else{
                        echo "
                        <div class='adder'>
                            <form action='includes/delete.php' method='post'>
                                <input type='hidden' name='pos' value='".$returnPos."'>
                                <input type='hidden' name='page' value='".$returnPage."'>
                                <button type='submit' name='matchId' value=".$matchId." >Remove Match</button>
                            </form>
                        </div>";
                    }
                
            

                }
                else {
                    header("Location: index.php?error=dbError1");
                    exit(); 
                }

                echo '<div class="matchHead">';
                echo '<h1>'.$date.'</h1>';
                echo '<table>
                        <tr>
                            <th>'.$homeTeam.'</th>
                            <th>'.$FTHG.'</th>
                            <th>-</th>
                            <th>'.$FTAG.'</th>
                            <th>'.$awayTeam.'</th>


                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Shots: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hShot.'</td>
                            <td width: 51.5%;>'.$aShot.'</td>
                        </tr>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Shots On Target: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hShotTar.'</td>
                            <td width: 51.5%;>'.$aShotTar.'</td>
                        </tr>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Shot Accuracy: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hShotAcc.' %</td>
                            <td width: 51.5%;>'.$aShotAcc.' %</td>
                        </tr>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Shot Conversion: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hShotConv.' %</td>
                            <td width: 51.5%;>'.$aShotConv.' %</td>
                        </tr>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Corners: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hCorners.'</td>
                            <td width: 51.5%;>'.$aCorners.'</td>
                        </tr>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Fouls: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hFouls.'</td>
                            <td width: 51.5%;>'.$aFouls.'</td>
                        </tr>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Yellow Cards: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hYellow.'</td>
                            <td width: 51.5%;>'.$aYellow.'</td>
                        </tr>
                        <tr>
                            <td style="width: 10%; font-size:1.3vw">Red Cards: </td>
                            <td style="border-right:3px solid black; width: 38.5%;">'.$hRed.'</td>
                            <td width: 51.5%;>'.$aRed.'</td>
                        </tr>
                    </table>
                    </div>';
                
            }
            else{
                header("Location: index.php?error=dbError");
                exit();

            }


        }
        else{
            header("Location: index.php");
            exit();
        }



        ?>
        </div>
    </body>

<?php 
    require "footer.php"
?>