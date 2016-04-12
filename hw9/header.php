<?php
echo "
    <html>
        <head>
            <title> Tolkien DB </title>
        </head>
    <body>
    <center>
    <a href=index.php> Story List</a> |
    <a href=index.php?s=6> Character List</a> |
    <a href=add.php> Add Characters</a> |
    ";

if (isset($_SESSION['authenticated']))
{
    echo "<span style='float:right'><a href=logout.php> Logout</a></span>";
    if ($_SESSION['userid'] == 1)
    {echo "<a href=add.php?s=90> Add New User </a> |";
    echo "<a href=add.php?s=92> User List </a> |";
    echo "<a href=add.php?s=93> Change Password </a> |";
    echo "<a href=add.php?s=95> View Login Attempts </a>";}
}

echo "<hr>";
?>

