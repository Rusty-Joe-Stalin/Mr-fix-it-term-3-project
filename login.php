<?php
  session_start();
  require("connect.php");

  $Invalid = false;

  if($_POST && isset($_POST['UserName']) && isset($_POST['Password'])){

    $user = trim(filter_input(INPUT_POST,"UserName", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
     $Password = trim(filter_input(INPUT_POST,"Password", FILTER_SANITIZE_FULL_SPECIAL_CHARS));

      $query= "SELECT * FROM users WHERE UserName = :user";
      $Statment = $db->prepare($query);
      $Statment->bindValue(":user", $user);
      $Statment->execute();
      $rows = $Statment -> fetchAll();

  $hashed = $rows[0]['password'];
  // echo $hashed;
  // echo " - ";
  // echo $Password;
  // echo " - ";
  // echo password_hash($Password,PASSWORD_BCRYPT);

  if(password_verify($Password,$hashed)) ///// FIX !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
      {

          if(count($rows) == 1 && $rows[0]['UserRole'] == 'normal')
          {
            $_SESSION['logginID'] = $rows[0]['userID'];
            $_SESSION['UserName'] = $rows[0]['UserName'];
            $_SESSION['Admin'] = false;
            //  print_r($_SESSION);
             header("Location:index.php");
          }
          else if (count($rows) == 1 && $rows[0]['UserRole'] == 'administrator')
          {
            $_SESSION['logginID'] = $rows[0]['userID'];
            $_SESSION['UserName'] = $rows[0]['UserName'];
            $_SESSION['Admin'] = true;
            //  print_r($_SESSION);
             header("Location:admin.php");
          }
      }
      echo "<script> alert('Invalid username or password')</script>";
        $Invalid = true;
  }

 ?>


 <!DOCTYPE html>
 <html>
   <head>
     <!-- <meta charset="utf-8"> -->
     <title>Log in</title>
     <link rel="stylesheet" type="text/css" href="fix-it.css">
   </head>
   <body>
     <div id="page">
       <div id="log">
         <ul>
           <li><a href="login.php">Log in</a></li>
             <li>|</li>
           <li><a href="register.php">Register</a></li>
         </ul>
       </div>
       <div id="header">
         <h1><a href="index.php">Mr. Fix-it </a></h1>
         <img src="images/logo.jpg" alt="logo" height="100" width="130"/>
         <p>your DIY guide</p>
         <nav>
           <ul>
             <li><a href="index.php?CatId=1">  Kitchen </a></li>
             <li><a href="index.php?CatId=3">  Bathroom </a></li>
             <li><a href="index.php?CatId=4">  Out Doors</a></li>
             <li><a href="index.php?CatId=6">  Crafts</a></li>
             <li><a href="index.php?CatId=8">  Work Shop </a></li>
             <li><a href="index.php"> All Projects </a></li>
           </ul>
         </nav>
       </div>
 <!-- section content  -->
      <div id="form">
         <form method="post">
            <label> Username </label>
            <input type="text" name="UserName">
            <label> Password </label>
            <input type="password" name="Password">

            <input type="submit">
         </form>
           <?php if($Invalid):?>
             <p> Invalid username or password </p>
           <?php endif?>
      </div>

  <div id="space"> </div>
 <!-- footer -->
     <div id="footer">
       <ul>
         <li><a href="index.php?CatId=1">  Kitchen </a></li>
         <li><a href="index.php?CatId=3">  Bathroom </a></li>
         <li><a href="index.php?CatId=4">  Out Doors</a></li>
         <li><a href="index.php?CatId=6">  Crafts</a></li>
         <li><a href="index.php?CatId=8">  Work Shop </a></li>
         <li><a href="index.php"> All Projects </a></li>
       </ul>
   </div>
   </body>
 </html>
