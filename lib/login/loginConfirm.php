<?php
session_start();
$valid = TRUE;
$uname = $_REQUEST['uname'];
$pwd = md5($_REQUEST['psw']);
if (!isset($uname,$pwd)){
    echo "Please complete all fields.";
    echo "<br>";
    $valid = FALSE;
}
//ceil(log10($number)) is the number's length

$con = mysqli_connect("localhost", "crabz", "88yGu2XF", "crabz");
//$con = mysqli_connect("localhost", "", "", "test");
if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$stmt = $con->prepare("SELECT password, userId FROM user WHERE userName= ?");
$stmt->bind_param("s" , $uname);
//$sql = ("SELECT userName,password FROM user WHERE userName=".$uname." AND password=".$pwd);

//$rst = $con->query($sql);
$stmt->execute();
$stmt->bind_result($pwdsql, $uId);
$stmt->store_result();
$stmt->fetch();
if($stmt->num_rows==1) {
  if($pwd == $pwdsql) {
    $_SESSION['userId']=$uId;
    $stmt->close();
    $stmt = $con->prepare("SELECT id FROM admin WHERE id = ?");
    $stmt->bind_param("i" , $uId);
    $stmt->execute();
    if($stmt->fetch()){
      $_SESSION['admin'] = true;
      unset($_SESSION['loginError']);
      $stmt->close();
      $con->close();
      echo "Logged in as an admin...Redirecting";
      header("Refresh: 2; URL = ../../views/viewAccount.php");
    }
    else {
      unset($_SESSION['loginError']);
      $stmt->close();
      $con->close();
      echo "Logged in as user...Redirecting";
      header("Refresh: 2; URL = ../../views/viewAccount.php");
    }
  }
  else {
    $stmt->close();
    $con->close();
    $_SESSION['loginError'] = true;
    header("Location: ../../views/viewLogin.php");
  }
}
else {
  $stmt->close();
  $con->close();
  $_SESSION['loginError'] = true;
  header("Location: ../../views/viewLogin.php");
}
//$row = mysqli_fetch_array($rst, MYSQLI_ASSOC) ;

//if(mysqli_num_rows($rst) ==0 ){
//    echo "not there";
//}else{
	//echo "loged in succesfully";
//}


?>
