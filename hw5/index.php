<?php
// Name     : hw5.php
// Purpose  : a dynamic database reader
// Author   : Eric Lobato eric.lobato@colorado.edu
// Version  : 1.0
// Date:    : 02/21/2016

include_once('/var/www/html/hw5/hw5-lib.php');
include_once('header.php');
connect($db);

isset ( $_REQUEST['s'] ) ? $s = strip_tags($_REQUEST['s']) : $s = "";
isset ( $_REQUEST['bid'] ) ? $bid = strip_tags($_REQUEST['bid']) : $bid = "";
isset ( $_REQUEST['sid'] ) ? $sid = strip_tags($_REQUEST['sid']) : $sid = "";
isset ( $_REQUEST['cid'] ) ? $cid = strip_tags($_REQUEST['cid']) : $cid = "";


isset ( $_REQUEST['characterName'] ) ? $characterName= strip_tags($_REQUEST['characterName']) : $characterName = "";
isset ( $_REQUEST['characterRace'] ) ? $characterRace= strip_tags($_REQUEST['characterRace']) : $characterRace = "";
isset ( $_REQUEST['characterSide'] ) ? $characterSide= strip_tags($_REQUEST['characterSide']) : $characterSide = "";
isset ( $_REQUEST['characterURL'] ) ? $characterURL= strip_tags($_REQUEST['characterURL']) : $characterURL = "";
isset ( $_REQUEST['books'] ) ? $books= strip_tags($_REQUEST['books']) : $books= "";

icheck($s);
icheck($sid);
icheck($cid);
icheck($bid);

switch($s)
{
    case 0: // stories page
       echo "<table> <tr> <td> <b> <u> Stories </b> </u> </td></tr> \n";
        $query="SELECT storyid, story from stories";
        $result=mysqli_query($db, $query);
        while($row=mysqli_fetch_row($result))
            {
            echo "<tr> <td> $row[0] </td>
            <td> <a href=index.php?s=1&sid=$row[0]>
                 $row[1] </a></td></tr> \n";
            }
        echo "</table>";
        break;

    case 1: //Books page
       echo "<table> <tr> <td> <b> <u> Books </b> </u> </td></tr> \n";
        $sid=mysqli_real_escape_string($db, $sid);
        if ($stmt = mysqli_prepare($db, "SELECT bookid, title FROM books WHERE storyid = ?;"))
        {
            mysqli_stmt_bind_param($stmt, "s", $sid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$bid,$title);
                while(mysqli_stmt_fetch($stmt))
                {
                $bid=htmlspecialchars($bid);
                $title=htmlspecialchars($title);
                echo "<tr><td> <a href=index.php?bid=$bid&s=2>$title</a></td></tr>";
                }
                mysqli_stmt_close($stmt);
        }
        echo "</table>";
        break;

    case 2: // characters page
       echo "<table> <tr> <td> <b> <u> Characters </b> </u> </td></tr> \n";
        $bid=mysqli_real_escape_string($db, $bid);
        if ($stmt = mysqli_prepare($db, "select characters.characterid,name from books, characters, appears where appears.bookid=books.bookid and appears.characterid=characters.characterid and books.bookid=?;"))
        {
            mysqli_stmt_bind_param($stmt, "s", $bid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$cid,$name);
            while(mysqli_stmt_fetch($stmt))
            {
                $cid=htmlspecialchars($cid);
                $name=htmlspecialchars($name);
                echo "<tr><td> <a href=index.php?cid=$cid&s=3>$name</a></td></tr>";
            }
            mysqli_stmt_close($stmt);
        }
echo "</table>";
    break;

    case 3: // list of appearances
        echo "<table> <tr> <td colspan=3> <b> <u> Appearances </b> </u> </td> </tr> <tr> <td> Character </td> <td> Book </td> <td> Story </td></tr>\n";
        $cid=mysqli_real_escape_string($db, $cid);
        if ($stmt = mysqli_prepare($db,
                        "SELECT name, title, story
                        FROM (SELECT A.name, A.characterid, A.title, B.story
                              FROM (SELECT A.name, A.characterid, B.title, B.storyid
                                    FROM (SELECT A.appearsid, A.bookid, A.characterid, B.name
                                          FROM appears A
                                          INNER JOIN characters B
                                          ON A.characterid = B.characterid) A
                                    INNER JOIN books B
                                    ON A.bookid=B.bookid) A
                              INNER JOIN stories B
                              ON A.storyid=B.storyid) A
                        WHERE A.characterid=?;"))
        {
            mysqli_stmt_bind_param($stmt, "s", $cid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$character,$title,$story);
            while(mysqli_stmt_fetch($stmt))
            {
                $character=htmlspecialchars($character);
                $title=htmlspecialchars($title);
                $story=htmlspecialchars($story);
                echo "<tr>
                        <td><a href=index.php?>$character</a></td>
                        <td><a href=index.php?>$title</a></td>
                        <td><a href=index.php?>$story</a></td>
                      </tr>";
            }
            mysqli_stmt_close($stmt);
        }
        echo "</table>";
        break;

    case 4: // add a character page
        echo "
            <form method=post action=index.php> 
            <table> <tr> <td colspan=2> Add Character to Books </td> </tr>
            <tr> <td> Character Name </td> <td> <input type=text name=characterName value=\"\"> </td> </tr>
            <tr> <td> Race </td> <td> <input type=text name=characterRace value=\"\"> </td> </tr>
            <tr> <td> Side </td> <td> <input type=\"radio\" name=\"characterSide\" value=\"good\"> Good  <input type=\"radio\" name=\"CharacterSide\" value=\"evil\"> Evil </td> </tr>
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

            echo "<form method=post action=index.php?s=7> 
            Add a picture for $characterName: <br></br>
            <tr> <td> URL for picture </td> <td> <input type=text name=characterURL value=\"\"> </td> </tr>
            <tr> <td colspan=2> <input type=hidden name=s value=7><input type=hidden name=cid value=$cid><input type=hidden name=characterName value=$characterName> <input type=submit name=submit value=submit> </td></tr>
            </form>";
            mysqli_stmt_close($stmt);
            }
        else {
            echo "Error with Query!";}
        break;

case 6: //page with characters and their pictures
       echo "<table> <tr> <td> <b> <u> Stories </b> </u> </td></tr> \n";
        $query=" select pictures.characterid, name, url from characters,pictures where characters.characterid=pictures.characterid;";
        $result=mysqli_query($db, $query);
        while($row=mysqli_fetch_row($result))
            {
            echo "
            <td> <a href=index.php?s=3&cid=$row[0]>$row[1]  </a></td></tr> \n
            <td> <img src=$row[2]></td> <tr>";
            }
        echo "</table>";
        break;

case 7:
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
                echo"<form method=post action=index.php?>
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

case 8:
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
        echo"<form method=post action=index.php?>
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
}

function icheck($s)
{
    if ($s != null)
    {
        if(!is_numeric($s))
        {
        print "<b> ERROR: </b>
        Invalid Syntax.  ";
        exit;
        }
    }
}

?>
