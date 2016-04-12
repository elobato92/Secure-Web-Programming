<?php
// Name     : add.php (hw7)
// Purpose  : deals with pages that involve adding characters
// Author   : Eric Lobato eric.lobato@colorado.edu
// Version  : 1.0
// Date:    : 03/11/2016
session_start();
include_once('hw7-lib.php');
connect($db);

if (!isset($_SESSION['authenticated']))
{
  authenticate($db, $postUser,$postPass);
}
include_once('header.php');

icheck($s);
icheck($sid);
icheck($cid);
icheck($bid);

switch($s)
{
    default;
    case 4: // add a character page
        echo "
            <form method=post action=add.php> 
            <table> <tr> <td colspan=2> Add Character to Books </td> </tr>
            <tr> <td> Character Name </td> <td> <input type=text name=characterName value=\"\"> </td> </tr>
            <tr> <td> Race </td> <td> <input type=text name=characterRace value=\"\"> </td> </tr>
            <tr> <td> Side </td> <td> <input type=\"radio\" name=\"characterSide\" value=\"good\"> Good  <input type=\"radio\" name=\"characterSide\" value=\"evil\"> Evil </td> </tr>
            <tr> <td colspan=2> <input type=hidden name=s value=5> <input type=submit name=submit value=submit> </td></tr>
            </table> 
            </form>";
    break;

case 5: //case that deals with adding character to DB
        $characterName=htmlspecialchars($characterName);
        $characterRace=htmlspecialchars($characterRace);
        $characterSide=htmlspecialchars($characterSide);

        $characterName=mysqli_real_escape_string($db, $characterName);
        $characterRace=mysqli_real_escape_string($db, $characterRace);
        $characterSide=mysqli_real_escape_string($db, $characterSide);
        if ($stmt = mysqli_prepare($db, "INSERT INTO characters set characterid='', name=?,race=?,side=?"))
            {
            mysqli_stmt_bind_param($stmt, "sss", $characterName, $characterRace, $characterSide);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            }
        if ($stmt = mysqli_prepare($db, "SELECT characterid from characters where name=? and race=? and side=? order by characterid desc limit 1"))
            {
            mysqli_stmt_bind_param($stmt, "sss", $characterName, $characterRace, $characterSide);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$cid);
            while(mysqli_stmt_fetch($stmt))
                {
                $cid=$cid;
                }
            //echo"cid5 is: $cid";

            echo "<form method=post action=add.php> 
            Add a picture for $characterName: <br></br>
            <tr> <td> URL for picture </td> <td> <input type=text name=characterURL value=\"\"> </td> </tr>
            <tr> <td colspan=2> <input type=hidden name=s value=7><input type=hidden name=cid value=$cid><input type=hidden name=characterName value=$characterName> <input type=submit name=submit value=submit> </td></tr>
            </form>";
            mysqli_stmt_close($stmt);
            }
        else {
            echo "Error with Query!";}
        break;

case 7: //case that adds the picture url into the database and shows first book list
        $cid=htmlspecialchars($cid);
        $characterURL-htmlspecialchars($characterURL);

        $cid =mysqli_real_escape_string($db,$cid);
        $characterURL=mysqli_real_escape_string($db, $characterURL);
        if ($stmt =mysqli_prepare($db, "INSERT INTO  pictures set pictureid='',url=?,characterid=?"))
            {
            mysqli_stmt_bind_param($stmt, "ss", $characterURL, $cid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            }
        echo "Choose which book $characterName appears in:";
            if ($stmt = mysqli_prepare($db,"SELECT distinct(a.bookid), b.title FROM books b, appears a WHERE a.bookid NOT IN (SELECT bookid FROM appears WHERE characterid=?) AND b.bookid=a.bookid"))
            {
                mysqli_stmt_bind_param($stmt, "s", $cid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$bid,$title);
                echo"<form method=post action=add.php>
                <select name =\"books\">";

                while(mysqli_stmt_fetch($stmt))
                {
                echo"
               <option value=\"$bid\"> $title </option>
                ";
                }
                $characterName=htmlspecialchars($characterName);
                echo "</select> <input type=hidden name=s value=8><input type=hidden name=cid value=$cid><input type=hidden name=characterName value=$characterName> <input type=submit name=submit value=submit> </form>";
                mysqli_stmt_close($stmt);
            }
        break;

case 8: // Case that handles additionall book appearances being added
    $books=htmlspecialchars($books);

    $books=mysqli_real_escape_string($db, $books);
    $cid=mysqli_real_escape_string($db,$cid);
    if ($stmt = mysqli_prepare($db, "INSERT INTO appears set appearsid='', bookid=?, characterid=?"))
        {
        mysqli_stmt_bind_param($stmt, "ss", $books, $cid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        }
    echo "Choose additional books that $characterName appears in or hit Done when finished.";
    if ($stmt = mysqli_prepare($db,"SELECT distinct(a.bookid), b.title FROM books b, appears a WHERE a.bookid NOT IN (SELECT bookid FROM appears WHERE characterid=?) AND b.bookid=a.bookid"))
    {
        mysqli_stmt_bind_param($stmt, "s", $cid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$bid,$title);
        echo"<form method=post action=add.php>
        <select name =\"books\">";

        while(mysqli_stmt_fetch($stmt))
        {
        echo"
        <option value=\"$bid\"> $title </option>
        ";
        }
        $characterName=htmlspecialchars($characterName);
        $cid=htmlspecialchars($cid);
        echo "</select> <input type=hidden name=s value=8><input type=hidden name=cid value=$cid><input type=hidden name=characterName value=$characterName> <input type=submit name=submit value=submit> </form>";
        echo "<a href=index.php?s=3&cid=$cid>Done </a> ";
        mysqli_stmt_close($stmt);
    }
    break;

case 90: // This is the add new user page
    adminCheck();
    echo"
            <form method=post action=add.php> 
            <table> <tr> <td colspan=2> Add new user information. </td> </tr>
            <tr> <td> User Name </td> <td> <input type=text name=Newname value=\"\"> </td> </tr>
            <tr> <td> Password </td> <td> <input type=password name=Newpass value=\"\"> </td> </tr>
            <tr> <td colspan=2> <input type=hidden name=s value=91> <input type=submit name=submit value=submit> </td></tr>
            </table> 
            </form>";
    break;

case 91:// this is where the new user is inserted into the DB and success msg displays.
    adminCheck();
    Adduser($db,$Newname,$Newpass);
    echo "$Newname has been added successfully!";
    break;

case 92:// this is the user list page
    adminCheck();
    echo "<b> Users Currently in Database: </b> <br></br> <table>";
    $query= "SELECT username from users order by userid;";
    $result= mysqli_query($db, $query);
    while($row=mysqli_fetch_row($result))
    {
        echo "<tr><td> $row[0] </td>";
    }
    echo "</table>";
    break;
case 93: //this is the update password page
    adminCheck();
    echo "Choose which user you'd like to update";
        if ($stmt = mysqli_prepare($db,"SELECT userid, username from users order by userid"))
        {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$Uid,$name);
            echo"<table>
            <form method=post action=add.php>
            <tr><td><select name =\"Uid\">";

            while(mysqli_stmt_fetch($stmt))
            {
            echo"
           <option value=\"$Uid\"> $name </option>
            ";
            }
            echo "<tr><td> New Password: <input type = password name=Newpass value=\"\"> </td></tr>";
            echo "<tr><td></select> <input type=hidden name=s value=94> <input type=submit name=submit value=submit> </form></td><tr> </table>";
            mysqli_stmt_close($stmt);
        }
    break;

case 94://success page for password update
    adminCheck();
    Changepass($db,$Uid,$Newpass);
    echo "Password updated Successfully!" ;
    break;

case 95: //this is the logins attempted list
    adminCheck();
    echo "<table> <tr> <td> <b> <u> Failed Login Attempts </b> </u> </td></tr> \n";
    echo "<tr> <td><b> IP </b> </td>";
    echo "<td><b> Count </b> </td></tr>";
    $query="SELECT  ip,COUNT(*) as count FROM login where action='deny' GROUP BY ip ORDER BY count desc;";
    $result=mysqli_query($db, $query);
    while($row=mysqli_fetch_row($result))
    {
        echo "<tr> <td> $row[0] </td>
            <td> $row[1] </td>";
    }
    echo "</table>";
    break;
}
?>
