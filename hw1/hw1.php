<?php
//Homework 1
//Eric Lobato
//01-18-2016

isset ( $_REQUEST['i'] ) ? $i = $_REQUEST['i'] : $i = "";
echo "
<html>
 <head> <title> Homework 1 </title> </head>
 <body>
";
$x=rand(0,20);
//$x=2; //DEBUG
if ( $i == Null ) {
 echo "
 <form method=post action=hw1.php>
 Please guess a number between 0 and 20:
 <input type= text id=\"i\" name=\"i\">
 <input type=submit value =\"Submit\" /></p>
 </form>";
} elseif ($i > 20) {
 echo "Error! Guess Out of Range!";
 echo "
 <form method=post action=hw1.php>
 Try another number between 0 and 20:
 <input type= text id=\"i\" name=\"i\">
 <input type=submit value =\"Submit\" /></p>
 </form>";
} elseif ($i < 0) {
 echo "Error! Guess Out of Range!";
 echo "
 <form method=post action=hw1.php>
 Try another number between 0 and 20:
 <input type= text id=\"i\" name=\"i\">
 <input type=submit value =\"Submit\" /></p>
 </form>";
} elseif ($i == $x) {
 echo "You guessed correctly!<br />";
 echo "Do you want to play again? <br />";
 echo "
 <form method=post action=hw1.php>
 Try another number between 0 and 20:
 <input type= text id=\"i\" name=\"i\">
 <input type=submit value =\"Submit\" /></p>
 </form>";
} elseif ($i < $x) {
 echo "Your guess of $i was too low!<br />";
 echo "The real value was $x.";
 echo "
 <form method=post action=hw1.php>
 Try another number between 0 and 20:
 <input type= text id=\"i\" name=\"i\">
 <input type=submit value =\"Submit\" /></p>
 </form>";
} elseif ($i > $x) {
 echo "Your guess of $i was too high!<br />";
 echo "The real value was $x.";
 echo "
 <form method=post action=hw1.php>
 Try another number between 0 and 20:
 <input type= text id=\"i\" name=\"i\">
 <input type=submit value =\"Submit\" /></p>
 </form>";
}

echo "</body> </html>";
?> 
