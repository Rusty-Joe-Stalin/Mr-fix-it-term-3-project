<?php
session_start();
require("connect.php");

$UserName= "Register";
$regLink ="register.php";
$loggin ="Log In";
$logLink ="login.php";
$editBox = false;

$Selected_User = filter_input(INPUT_GET,'User',FILTER_SANITIZE_NUMBER_INT);
$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

// file upload function
function file_upload_path($original_filename, $upload_subfolder_name = 'images') {
       $current_folder = dirname(__FILE__);
       $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
       return join(DIRECTORY_SEPARATOR, $path_segments);
    }

// log out statment
if($_GET && isset($_GET['out']) && $_GET['out']=='t'){
  session_destroy();
  $regLink ="register.php";
  $logLink ="login.php";
  $UserName= "Register";
  $loggin ="Log In";
  header("Location:index.php");
}

if(isset($_SESSION["UserName"]))
{
$UserName = $_SESSION["UserName"];
$regLink ="userShow.php?User=".$_SESSION['logginID'];
$loggin="Log Out";
$logLink="index.php?out=t";
}

if($_POST && isset($_POST['numOfSteps']))
{
  if($image_upload_detected && isset($_POST['numOfSteps']))
  {
      $original_filename = $_FILES['image']['name'];
      file_upload_path($original_filename);
      $image_filename       = $_FILES['image']['name'];
      $temporary_image_path = $_FILES['image']['tmp_name'];
      $new_image_path       = file_upload_path($image_filename);
      move_uploaded_file($temporary_image_path, $new_image_path);
      $path = "images/".$_FILES['image']['name'];

///image path insert
    $imageQuery = "INSERT INTO images(filePath) VALUES(:FilePath)";
    $ProfilePicStatment = $db->prepare($imageQuery);
    $ProfilePicStatment->bindValue(":FilePath",$path);
    $ProfilePicStatment-> execute();
    $picID = $db->lastInsertId();
  }

  $numOfSteps = filter_input(INPUT_POST,'numOfSteps');
  $catID = filter_input(INPUT_POST , 'catagory');
  $Title = filter_input(INPUT_POST, 'title' , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $description = filter_input(INPUT_POST, 'description' , FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $userID = (int)$_SESSION['logginID'];

  $ProjectContentQuery = "INSERT INTO projects(catagorID,userID,PicID,ProjectTitle,ProjectContent, DateCreated)
                                VALUES ( $catID , $userID, $picID ,:title ,:description , now())";
  $Statment = $db->prepare($ProjectContentQuery);
  $Statment-> bindValue(':title',$Title);
  $Statment-> bindValue(':description', $description);
  $Statment -> execute();
  $ProID = $db-> lastInsertId();

}

if( $_POST && isset($_POST['stepDescription_1'])){

  $i = 1 ;
  $picID = 0;
   foreach ($_POST as $key ) {
     $Loopimage_upload_detected = isset($_FILES['image_'.$i]) && ($_FILES['image_'.$i]['error'] === 0);

     if($Loopimage_upload_detected)
     {
         $original_filename = $_FILES['image_'.$i]['name'];
         file_upload_path($original_filename);
         $image_filename       = $_FILES['image_'.$i]['name'];
         $temporary_image_path = $_FILES['image_'.$i]['tmp_name'];
         $new_image_path       = file_upload_path($image_filename);
         move_uploaded_file($temporary_image_path, $new_image_path);
         $path = "images/".$_FILES['image_'.$i]['name'];

       $imageQuery = "INSERT INTO images(filePath) VALUES(:FilePath)";
       $ProfilePicStatment = $db->prepare($imageQuery);
       $ProfilePicStatment->bindValue(":FilePath",$path);
       $ProfilePicStatment-> execute();
       $picID = $db->lastInsertId();
     }
     $Query = "SELECT MAX(ProjectID) FROM projects";
     $Statment = $db->prepare($Query);
     $Statment-> execute();
     $ID = $Statment->fetch()[0];

     $content = filter_input(INPUT_POST,'stepDescription_'.$i,FILTER_SANITIZE_FULL_SPECIAL_CHARS);

     $StepQuery = "INSERT INTO page(projectID,PicID,StepDescription)VALUES($ID, :PicID ,:Description)";
     $Statment = $db->prepare($StepQuery);
     $Statment-> bindValue(':PicID',$picID);
     $Statment-> bindValue(':Description', $content);
     $Statment -> execute();

  header("Location:show.php?ProID=".$ProID);
     $i++;
   }

}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>New Pro</title>
    <link rel="stylesheet" type="text/css" href="fix-it.css"/>
  </head>
  <body>
    <div id="page">
      <div id="log">
        <ul>
          <li><a href="<?=$logLink?>"><?=$loggin?></a></li>
            <li>|</li>
          <li><a href="<?=$regLink?>"><?=$UserName?></a></li>
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
     <div id="form">

       <?php if(isset($_POST['numOfSteps']) == false):?>
        <form method="post" enctype="multipart/form-data">
          <label for="title"> Project Title</label>
          <input type="text" name="title">

          <label for="description"> enter your project description</label>
          <textarea name="description" rows="10" cols="70"> </textarea>

          <label for="numberOfSteps">How many steps will you add for your Project?</label>
          <input type="number" name="numOfSteps">

          <label for="catagory">Select a catagory</label>
          <select name="catagory">
              <option value="1">Kitchen</option>
              <option value="3">Bathroom</option>
              <option value="4">Out Doors</option>
              <option value="6">Crafts</option>
              <option value="8">WorkShop</option>
          </select>

          <label> Title Page Image </label>
          <input type="file" name="image" id="image">
          <input type="submit"  name="submit" value="Next"/>
        </form>
        <?php endif ?>

        <?php if ($_POST && isset($_POST['numOfSteps'])):?>
            <form method="post" enctype="multipart/form-data">
              <?php  for ($i=1; $i <= $numOfSteps ; $i++):?>
                      <p> step # <?=$i?></p>
                      <label for="stepDescription_<?=$i?>"> description</label>
                      <textarea name="stepDescription_<?=$i?>" rows="10" cols="40"> </textarea>
                      <input type="file" name="image_<?=$i?>" id="image">
        <?php  endfor ?>
          <input type="submit" value="save"/>
              </form>
        <?php endif?>
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
