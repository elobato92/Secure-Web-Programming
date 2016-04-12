<?php
include_once('header.php');

echo "
<form method=post action=add.php>
    You must log in to add characters.
    <table><tr> <td> Username: </td> <td> <input type=text name=postUser>  </td> </tr>
    <tr> <td> Password: </td> <td> <input type=password name=postPass>  </td> </tr>
    <tr> <td colspan=2> <input type=submit name=login value=login> </td></tr> 
    </table>
    </form>
    ";
?>
