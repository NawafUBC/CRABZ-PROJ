<?php
  session_start();
  include('../lib/db_credentials.php');
  include('../lib/getTransfers.php');
  $_SESSION['userId'] = 1;  //  Arbitary value **Assign upon loginPage

  $con = connect();
  // Check to see if user can accept
  $sql = "SELECT tId, toAcc, fromAcc, amount FROM transfer, account WHERE tId = ? AND toAcc = accNum AND ownerId = ?";
  $stmt = $con->prepare($sql);
  $stmt->bind_param('ii', $_GET['transferId'], $_SESSION['userId']);
  $stmt->execute();
  $stmt->bind_result($tId, $toAcc, $fromAcc, $amount);
  $stmt->fetch();
  if(!$stmt) {
    $transfer = false;
  }
  else
    $transfer = true;
  $stmt->close();

  if(!$_SESSION['hasClaimed']) {
    // Check to see if user confirmed
    if(isset($_POST['submit']) && $_POST['submit'] == 'Claim') {
      // Update balance
      $sql = "UPDATE account SET balance = balance + ? WHERE ownerId = ? AND accNum = ?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param('dii',$amount, $_SESSION['userId'], $toAcc);
      $stmt->execute();
      $stmt->fetch();
      $stmt->close();

      // Close transfer
      $sql = "UPDATE transfer SET status = 1 WHERE tId = ?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param('i', $_GET['transferId']);
      $stmt->execute();
      $stmt->fetch();
      $stmt->close();
      $_SESSION['hasClaimed'] = true;
    }
    else if(isset($_POST['submit']) && $_POST['submit'] == 'Revoke') {
      // Close transfer
      $sql = "UPDATE transfer SET status = -1 WHERE tId = ?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param('i', $_GET['transferId']);
      $stmt->execute();
      $stmt->fetch();
      $stmt->close();

      // Add funds back to sender
      $sql = "UPDATE account SET balance = balance + ? WHERE accNum = ?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param('di',$amount, $fromAcc);
      $stmt->execute();
      $stmt->fetch();
      $stmt->close();
      $_SESSION['hasClaimed'] = true;
    }
  }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../css/reset.css"/>
    <link rel="stylesheet" href="../css/layout.css"/>
    <link rel="stylesheet" href="../css/nav-header.css">
    <script type='text/javascript' src="../script/jquery-3.1.1.min.js"></script>
    <title>CRABZ-View Account Information</title>
  </head>
  <body class="bodyWrapper">
    <header>
      <nav id="headerNav" class="space-between">
        <div>
          <a href="">Home</a>
          <a href="../currencyExchange/CurrencyEx.html">Currency exchange</a>
          <a href="../transfers/viewTransfers.php">Transfer</a>
          <a href="../transactions/viewTransactions.php">Summary</a>
          <a href="../account/Account.php">Account</a>
        </div>
        <div>
          <a href="../login/login.html">Login</a>
          <a href="">Sign up</a>
        </div>
      </nav>
    </header>
    <div class="mainDivWrapper singleColumn-Margin">
      <main class="mainWrapper">
        <div class="flex-col small-pad bg-color-dark">
          <?php
          if($transfer) {echo '<h1>Accept or Decline the transfer</h1>';}
          ?>
          <div class="flex-row">
            <form method="post" action="confirmTransfer.php?transferId=<?php echo $_GET['transferId']; ?>">
              <input type="submit" name="submit" value="Claim">
              <input type="submit" name="submit" value="Revoke">
            </form>
          </div>

          <?php

          ?>
          <a href="viewTransfers.php">Go Back</a>
        </div>
      </main>
      <!--
      <nav class="rightColumn">
      </nav>
      <aside class="leftColumn">
      </aside>
      -->
    </div>
    <footer>
    </footer>
  </body>
</html>
