<?php
    session_start();
    $_SESSION['admin'] = false;
  if(!isset($_SESSION['userId']))
     header('location: ../views/viewLogin.php');
?>
<?php

//get connection
//$conn  = mysqli_connect($host, $user, $password, $database);
$conn = mysqli_connect("localhost", "crabz", "88yGu2XF", "crabz");
$error = mysqli_connect_error();
if ($error != null)
    {
    $output = "<p>Unable to connect to database</p>" . $error;
    exit($output);
    }
?>
<?php
if (isset($_POST['import']))
    {
    $filename = $_FILES["file1"]['tmp_name'];
		$filetype = $_FILES["file1"]["type"];
    if ($_FILES["file1"]["error"] > 0)
        {
        echo "error";
        }
    if ($_FILES["file1"]["size"] > 0)
        {
       $sql = "LOAD DATA LOCAL INFILE '$filename' INTO TABLE cvsfileimport FIELDS TERMINATED BY ',' set userid = ".$_SESSION['userId']."; ";
		//	$sql = "select * from cvsfileimport ; ";
        if ($conn->query($sql) === TRUE)
            {
            echo "new data inserted $filetype";
            }
          else
            {
            echo "error inserting the data" . "<br />" . $conn->error;
            }
        }
    }
else {
	echo "error";
}
?>