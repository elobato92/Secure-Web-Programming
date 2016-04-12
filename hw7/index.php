<?php
// Name     : index.php (hw7)
// Purpose  : a dynamic database reader
// Author   : Eric Lobato eric.lobato@colorado.edu
// Version  : 1.0
// Date:    : 03/11/2016
include_once('/var/www/html/hw7/hw7-lib.php');
include_once('header.php');
connect($db);
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

}

?>
