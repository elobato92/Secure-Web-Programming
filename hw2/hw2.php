<?php
//Name    : hw2.php
//Purpose : A php page that shows files
//Author  : Eric Lobato eric.lobato@colorado.edu
//Version : 1.0
//Date    : 01/16/2016

$DaysArray=array("Monday","Tuesday", "Wednesday", "Thursday",
    "Friday","Saturday","Sunday");
isset($_REQUEST['SubmitForm']) ? $SubmitForm = $_REQUEST['SubmitForm'] : $SubmitForm ="";
isset($_REQUEST['files']) ? $files = $_REQUEST['files'] : $files="";

if ($SubmitForm != Null)
{
    $FileNumber=$files;
    switch ($FileNumber)
       {
       default:
          echo "Something Bad Happened!";
       case "Days":
           for ($i=0; $i<7; $i++)
           {
               echo " The $i day of the week is $DaysArray[$i]<br>";
           }
            break;
       case is_numeric($FileNumber):
           printfile($FileNumber);
           break;
           }
}
elseif ($SubmitForm == Null)
{    
    echo "
 <!DOCTYPE html>
  <head><title> TLEN 5839 HW2: Eric Lobato </title></head>
  <body>
   <form action=\"hw2.php\" method=\"post\">
    <select name=\"files\">
        <option value=\"\"> Select ... </option>
            <option value=\"Days\"> Days</option>
            <option value=\"1\"> 1</option>
            <option value=\"2\"> 2</option>
            <option value=\"3\"> 3</option>
            <option value=\"4\"> 4</option>
    </select>
<input type=\"submit\" value=\"Submit\" name=\"SubmitForm\">
</form>
    </body>
    </html>"
    ;
}
function printfile($number)
{
    $CorrectName="file$number.txt";
    if (file_exists($CorrectName))
    {
        $lines=file($CorrectName);
        $counter = 0;
                foreach ($lines as $line)
                {
                    $counter++;
                    if (substr ($line,0,1) == "#")
                    {
                       continue;
                    }
                    else 
                    {
                        echo "$line <br>";
                    }
                    if ($counter >100)
                    {break;}
                }
        }
    else
        { echo "File Not Found!"; }
}
?>
