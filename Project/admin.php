<?php
 session_start();
 require("connect.php");
// print_r($_SESSION["UserName"]);
 $UserName= "Register";
 $regLink ="register.php";
 $loggin ="Log In";
 $logLink ="login.php";
if($_GET && isset($_GET['out']) && $_GET['out']=='t'){
    session_destroy();
    $UserName = "Register";
    $regLink ="register.php";
    // print_r($_SESSION);
}
if(isset($_SESSION["UserName"])){
  $UserName = $_SESSION["UserName"];
  $regLink ="userShow.php";
  $loggin="Log Out";
  $logLink="index.php?out=t";
}


    $query= "SELECT * FROM projects JOIN users USING(useriD)  JOIN images WHERE images.PicID = projects.PicID ";
    $Statment = $db->prepare($query);
    $Statment->execute();
    $rows = $Statment -> fetchAll();
    // print_r($Statment->errorInfo());

    $userquery= "SELECT * FROM users WHERE UserRole NOT LIKE 'administrator'";
    $UserStatment = $db->prepare($userquery);
    $UserStatment->execute();
    $Users = $UserStatment -> fetchAll();
// print_r($UserStatment->errorInfo());


//  delete the selected project .
if( $_GET && isset($_GET['delete'])){

  // echo"<script> confirm('Are you Sure?')</script>";
  $SelectedDelete = filter_input(INPUT_GET ,'delete',FILTER_SANITIZE_NUMBER_INT);

  $delquery= "DELETE FROM projects WHERE  ProjectID = :ProjectID;
              DELETE FROM images WHERE ProjectID = :ProjectID";
  $Statment = $db->prepare($delquery);
  $Statment -> bindValue(':ProjectID' , $SelectedDelete);
  $Statment->execute();

// find the images associated
  $delPicquery= "SELECT filePath FROM projects JOIN images WHERE images.PicID = projects.PicID AND ProjectID = :ProjectID ";
  $Statment = $db->prepare($delPicquery);
  $Statment -> bindValue(':ProjectID' , $SelectedDelete);
  $Statment->execute();
  $DElrows = $Statment -> fetchAll();

//delete each image associated with the selected project.
  foreach ($DElrows as $key ) {
      unlink($key['filePath']);
  }

  // print_r($Statment->errorInfo());
   header("Location:admin.php");
}


 // print_r ($rows);
?>

<!DOCTYPE html>
<html>
  <head>
    <!-- <meta charset="utf-8"> -->
    <title>Mr. Fix-it Admin Page</title>
    <link rel="stylesheet" type="text/css" href="fix-it.css">
    <!-- <link rel="stylesheet" type="text/css" href="fix-it.css"> -->
  </head>
  <body>
    <div id="page">
      <div id="log">
        <ul>
          <li><a href="<?=$logLink?>"><?=$loggin?></a></li>
            <li>|</li>
          <li><a href="<?=$regLink?>?User=<?=$_SESSION['logginID']?>"><?=$UserName?></a></li>
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
            <li><a href="index.php?CatId=4">  Out Doors </a></li>
            <li><a href="index.php?CatId=6">  Crafts </a></li>
            <li><a href="index.php?CatId=8">  Work Shop </a></li>
            <li><a href="index.php"> All Projects </a></li>
          </ul>
        </nav>
      </div>

<!-- section content  -->
     <div id="content">
        <h1> Current Projects User created Projects</h1>
        <?php foreach ($rows as $key ):?>
          <h3><a href="show.php?ProID=<?=$key['ProjectID'] ?>"> <?= $key['ProjectTitle']?></a></h3>
          <p> project was add on <?=$key['DateCreated']?></p>
          <p> created by <?=$key['UserName']?></p>
          <img src="<?=$key['filePath']?>" alt="Test Pic" height="200" width="200"/>
          <a href="admin.php?delete=<?=$key['ProjectID']?>"> Delete </a>
        <?php endforeach ?>

        <h1> All users in avalable</h1>
  <!-- display all users to for edits. -->
        <?php  foreach ($Users as $Userkey): ?>
         <h2><?= $Userkey["UserName"] ?></h2>
         <p><?=$Userkey["DateCreated"]?></p>
         <a href="userShow.php?User=<?=$Userkey['userID']?>"> Edit <?= $Userkey["UserName"] ?></a>
        <?php endforeach?>
     </div>

    <div id="space"> </div>
<!-- footer -->
    <div id="footer">
      <ul>
        <li><a href="index.php?CatId=1">  Kitchen </a></li>
        <li><a href="index.php?CatId=3">  Bathroom </a></li>
        <li><a href="index.php?CatId=4">  Out Doors </a></li>
        <li><a href="index.php?CatId=6">  Crafts </a></li>
        <li><a href="index.php?CatId=8">  Work Shop </a></li>
        <li><a href="index.php"> All Projects </a></li>
      </ul>
    </div>
  </div>
  </body>
</html>
