<?php
// Name     : hw9-lib.php (hw9)
// Purpose  : library files for homework 9
// Author   : Eric Lobato eric.lobato@colorado.edu
// Version  : 1.0
// Date:    : 04/03/2016
// WHITELIST for IP ADDRESSES can be found on LINE 68

isset ( $_REQUEST['s'] ) ? $s = strip_tags($_REQUEST['s']) : $s = "";
isset ( $_REQUEST['bid'] ) ? $bid = strip_tags($_REQUEST['bid']) : $bid = "";
isset ( $_REQUEST['sid'] ) ? $sid = strip_tags($_REQUEST['sid']) : $sid = "";
isset ( $_REQUEST['cid'] ) ? $cid = strip_tags($_REQUEST['cid']) : $cid = "";


isset ( $_REQUEST['characterName'] ) ? $characterName= strip_tags($_REQUEST['characterName']) : $characterName = "";
isset ( $_REQUEST['characterRace'] ) ? $characterRace= strip_tags($_REQUEST['characterRace']) : $characterRace = "";
isset ( $_REQUEST['characterSide'] ) ? $characterSide= strip_tags($_REQUEST['characterSide']) : $characterSide = "";
isset ( $_REQUEST['characterURL'] ) ? $characterURL= strip_tags($_REQUEST['characterURL']) : $characterURL = "";
isset ( $_REQUEST['books'] ) ? $books= strip_tags($_REQUEST['books']) : $books= "";
isset ( $_REQUEST['postUser'] ) ? $postUser= strip_tags($_REQUEST['postUser']) : $postUser= "";
isset ( $_REQUEST['postPass'] ) ? $postPass= strip_tags($_REQUEST['postPass']) : $postPass= "";
isset ( $_REQUEST['Newname'] ) ? $Newname= strip_tags($_REQUEST['Newname']) : $Newname= "";
isset ( $_REQUEST['Newpass'] ) ? $Newpass= strip_tags($_REQUEST['Newpass']) : $Newpass= "";
isset ( $_REQUEST['Uid'] ) ? $Uid= strip_tags($_REQUEST['Uid']) : $Uid= "";

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
function connect(&$db)
{
	$mycnf="/etc/hw9-mysql.conf";
    if (!file_exists($mycnf))
    {
        echo "Error! File not found: $mycnf";
        exit;
    }
    $mysql_ini_array=parse_ini_file($mycnf);
    $db_host=$mysql_ini_array["host"];
    $db_user=$mysql_ini_array["user"];
    $db_pass=$mysql_ini_array["pass"];
    $db_port=$mysql_ini_array["port"];
    $db_name=$mysql_ini_array["dbName"];
    $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
    if (!$db)
    {
            print "Error connecting to DB: " . mysqli_connect_error();
            exit;
    }
}
function authenticate($db,$postUser,$postPass)
{
    $_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
    $_SESSION['HTTP_USER_AGENT']=md5($_SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT']);
    $_SESSION['created']=time();
    
    if($postUser == Null ||  $postPass ==Null)
        {header("Location: /hw9/login.php");}

    $WHITELIST=array('198.18.1.186','127.0.0.1');
    $ip=$_SERVER['REMOTE_ADDR'];
    if (!in_array($ip, $WHITELIST,true))
    {ban_check($db);}

    $query="select userid,email,password,salt from users where username=?";
    if ($stmt= mysqli_prepare($db,$query))
      {
        mysqli_stmt_bind_param($stmt, "s",  $postUser);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userid, $email, $password, $salt);
        while(mysqli_stmt_fetch($stmt))
        {
            $userid=$userid;
            $password=$password;
            $salt=$salt;
            $email=$email;
        }
        mysqli_stmt_close($stmt);
        $epass=hash('sha256',$postPass.$salt);
        if ($epass == $password)
        {
            session_regenerate_id();
            $_SESSION['userid']=$userid;
            $_SESSION['email']=$email;
            $_SESSION['authenticated']="yes";
            $_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
            $action='allow';
            log_ip($db,$postUser,$action);//log that this user was allowed
        }
        else
        {
            echo "Failed to Login";
            header("Location: /hw9/login.php");
            $action='deny';
            log_ip($db,$postUser,$action);//Log that this user was denied
            error_log("**ERROR**: Tolkien App has failed login from " . $_SERVER['REMOTE_ADDR'],0);
            exit;
        }
    }
}
function checkAuth()
{
/////////////////////TEST BROWSER
    if (isset($_SESSION['HTTP_USER_AGENT']))
    {
        if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT']))
        {error_log(print_r("usr_agnt_fail",TRUE));
        logout();}
    }
    else
    {error_log(print_r("else_usr_agnt_fail",TRUE));
    logout();}
/////////////////////TEST THAT USER IP IS CONSISTANT
    if (isset($_SESSION['ip']))
    {
        if ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR'])
        {error_log(print_r("ip_fail. Remote addr =".$_SERVER['REMOTE_ADDR'],TRUE));
        logout();}
    }
    else
    {error_log(print_r("else_ip_fail",TRUE));
    logout();}

/////////////////////SET 30 MINUTE TIMEOUT
    if (isset($_SESSION['created']))
    {
        if (time() - $_SESSION['created'] > 1800)
        {error_log(print_r("timeout reached",TRUE));
        logout();}
    }
    else
    {error_log(print_r("else_time_fail",TRUE));
    logout();}
/////////////////////TEST THAT SERVER IP IS ACTUALLY SERVER
    if ("POST" == $_SERVER["REQUEST_METHOD"])
    {
        if (isset($_SERVER["HTTP_ORIGIN"]))
        {
            if($_SERVER["HTTP_ORIGIN"] != "https://100.66.1.18")
            {error_log(print_r("origin_fail. Origin=".$_SERVER["HTTP_ORIGIN"],TRUE));
            logout();}
        }
        else
            {error_log(print_r("else_origin_fail. HTTP_ORIGIN not set.",TRUE));
            logout();}
    }
}

function logout()
{
    header("Location: /hw9/logout.php");
}

function log_ip($db,$postUser,$action)
{
    if ($postUser == NULL)//because this function gets called the first time a user hits the login page:
        {exit;}         // break out if there wasn't actually a username given
    $ip=$_SERVER['REMOTE_ADDR'];
    $postUser=htmlspecialchars($postUser);
    if ($stmt = mysqli_prepare($db,"insert into login set loginid='',ip=?,user=?,date=now(),action=?"))
    {
        mysqli_stmt_bind_param($stmt, "sss", $ip, $postUser,$action);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function ban_check($db)
{
    $ip=$_SERVER['REMOTE_ADDR'];
    $query="select ip from login where date>=DATE_SUB(NOW(),INTERVAL 1 HOUR) and action ='deny' and ip='$ip' HAVING count(*)>=5";
    //error_log(print_r($query,TRUE));
    $result=mysqli_query($db,$query);
    while($row=mysqli_fetch_row($result))
    {
    if ($row[0]==$ip)
        {header("Location: /hw9/banned.php");
        exit;}
    }
}
function adminCheck()
{
    if ($_SESSION['userid'] != 1)
    {
        echo "ERROR: Functionality not enabled for nonprivledged users.";
        exit;
    }
}

function Adduser($db,$Newname, $Newpass)
{
    $randomness=mt_rand(30000, 90000);
    $Newname=htmlspecialchars($Newname);
    $Newpass=htmlspecialchars($Newpass);
    $Newsalt=hash('sha256',$randomness);
    $Newpass=hash('sha256',$Newpass.$Newsalt);
    if ($stmt = mysqli_prepare($db, "INSERT INTO users set userid='', username=?, password=? , salt= ?, email =''"))
    {
        mysqli_stmt_bind_param($stmt, "sss", $Newname, $Newpass, $Newsalt);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function Changepass($db,$Uid, $Newpass)
{
    $randomness=mt_rand(30000, 90000);
    $Uid=htmlspecialchars($Uid);
    $Newpass=htmlspecialchars($Newpass);
    $Newsalt=hash('sha256',$randomness);
    $Newpass=hash('sha256',$Newpass.$Newsalt);
    if ($stmt = mysqli_prepare($db, "UPDATE users SET password=?, salt=? where userid=?"))
    {
        mysqli_stmt_bind_param($stmt, "sss", $Newpass, $Newsalt, $Uid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>
