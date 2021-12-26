<!--
 Author: Tomi
 Date: 09/11/2021
 Purpose: A trivia game managed by sessions
-->
<?php
//Start Session
session_start();

// to handle user clicking game.php?action=restart
// We need to destroy the session and clear the session cookie
if(isset($_GET['action'])){
    if($_GET['action'] == 'restart') {
        unset($_SESSION['count']);
        unset($_SESSION['userAnswer']);
    }
    header('location:trivia.php');
} // end if action set


//Document root
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

//Date format
date_default_timezone_set("America/Halifax");
$date = date('m/d/Y');

//Declaring
$pathToFile = $DOCUMENT_ROOT . "/Trivia/triviaData/triviaQuestions.txt";
$questions = array();
$answers = array();
$triviaDataArray = array();
$wrong = 0;
$correct = 0;
$count = 0;
$quizComplete = false;
$msg = "";
$percent = 0.00;
$postAnswer = "";

//If file cannot be read
if (!is_readable($pathToFile) || filesize($pathToFile) == 0) {
    echo "<html><head><link rel='stylesheet' href='css/stylesheet.css'></head><div id='container'><body>";
    echo "<h2>Trivia Questions unavailable. Our team has been notified</h2>";
    echo "<p> Current as of " . $date . "</p>";
    echo "</body></div></html>";
    exit();
} else {
    //Turning file into an array
    $triviaDataArray = file($pathToFile);

    //Loading questions and answers into different arrays
    foreach ($triviaDataArray as $allQuestionsandAnswer) {
        $triviaQAndA = explode("\t", $allQuestionsandAnswer);
        array_push($questions, $triviaQAndA[0]);
        array_push($answers, $triviaQAndA[1]);
    }
    //When submit is clicked
    if (isset($_POST['submit']) == true) {

        $postAnswer = $_POST['answer'];
        $count = intval($_SESSION['count']);

        //if Answer is empty
        if ($postAnswer == "") {
            $msg = "<p style='color: #ff0000; font-weight: bold'>Enter an answer</p>";
        } else {
//            $count++;
//            $_SESSION['count'] = $count;
            $_SESSION['count'] = ++$count;
            array_push($_SESSION['userAnswer'], $postAnswer);
            header("location: trivia.php");
        }
        //When page loads up first
    } else {
        if(isset($_SESSION['count']) && !empty($_SESSION['count'])) {
            $count = $_SESSION['count'];
        }else{
            $_SESSION['count']=0;
            $_SESSION['userAnswer'] = array();
        }

    }
}


?>

<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="css/stylesheet.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <title>Trivia</title>
</head>
<div id="container">
    <body>
    <?php
    if($count<count($questions)){
        echo '<p style="float: right">Question ' .($count+1). ' of ' .count($questions). '</p>';
    }?>
        <h2>Trivia</h2>
    <div id="formCenter">
        <form action="trivia.php" method="POST">
            <?php
            //When count is less than 6
            if($count<count($questions)){
                echo $questions[$_SESSION['count']];
                echo"<br><input type='text' class='form-control' name='answer'><br>
            <div style='text-align:center'>
            $msg
                <input type='submit' name='submit' class='btn btn-primary' id='submit'>
            </div>";

            }else{
                echo "<h4>Result</h4>";
                echo '<table class="table table-bordered table-hover">';
                echo '<thead><tr><th scope="col">#</th><th scope="col">Question</th><th scope="col">Answer</th><th scope="col">Yours</th></tr></thead>';


                //Loop to check if answers are correct
                for($i=0; $i<count($answers); $i++){
                    if (strtolower(trim($answers[$i])) == strtolower(trim($_SESSION['userAnswer'][$i]))){
                        $correct++;
//                        $_SESSION['correct'] = $correct;
                        echo "<tr class = 'table-success'>
                            <th scope='row'>".($i+1)."</th>
                            <td>".$questions[$i]."</td>
                            <td>".$answers[$i]."</td>
                            <td>".$_SESSION['userAnswer'][$i]."</td>
                            </tr>";
                    }else{
                        $wrong++;
//                        $_SESSION['wrong'] = $wrong;
                        echo "<tr class = 'table-danger'>
                            <th scope='row'>".($i+1)."</th>
                            <td>".$questions[$i]."</td>
                            <td>".$answers[$i]."</td>
                            <td>".$_SESSION['userAnswer'][$i]."</td>
                            </tr>";
                    }
                }

                //Calculating percentage
                $percent = number_format($correct/count($answers) * 100, 2);

                echo '</table>';
                echo "<h5> You got " . $percent . "%</h5>";

            }
            ?>
        </form>
    </div>
    <p><a href='trivia.php?action=restart'>Restart</a></p>
    </body>
</div>
</html>

<?php
