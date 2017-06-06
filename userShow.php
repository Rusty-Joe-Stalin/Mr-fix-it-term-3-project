<?php
 session_start();
 require("connect.php");
 require("imageResize.php");

$UserName= "Register";
$regLink ="register.php";
$loggin ="Log In";
$logLink ="login.php";
$editBox = false;
use \Eventviva\ImageResize;

if($_GET && isset($_GET['out']) && $_GET['out']=='t'){
   session_destroy();
   $regLink ="register.php";
   $logLink ="login.php";
   $UserName= "Register";
   $loggin ="Log In";
   header("Location:index.php");
}

if(isset($_SESSION["UserName"])){
 $UserName = $_SESSION["UserName"];
 $regLink ="userShow.php?User=". $_SESSION['logginID'];
 $loggin="Log Out";
 $logLink="index.php?out=t";
}

$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);

function file_upload_path($original_filename, $upload_subfolder_name = 'images') {
       $current_folder = dirname(__FILE__);
       $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
       return join(DIRECTORY_SEPARATOR, $path_segments);
    }


// if the admin is logged in and navigates to this page.
if(isset($_SESSION["Admin"]) && $_SESSION["Admin"] == true){
  $regLink ="admin.php";
  $editBox = true;
}
else {
  $editBox = false;
}
    $Selected_User = filter_input(INPUT_GET,'User',FILTER_SANITIZE_NUMBER_INT);

    $query= "SELECT * FROM projects JOIN users USING (UserID) JOIN images WHERE users.UserID = :user AND projects.PicID = images.PicID";
    $Statment = $db->prepare($query);
    $Statment->bindValue(":user",$Selected_User);
    $Statment->execute();
    $rows = $Statment -> fetchAll();

    $userQuery = "SELECT *  FROM users  JOIN images WHERE userID = :user AND users.PicID = images.PicID";
    $UserStatment = $db->prepare($userQuery);
    $UserStatment->bindValue(":user",$Selected_User);
    $UserStatment->execute();
    $UserRows = $UserStatment -> fetchAll(0);
  //  print_r($UserRows);

  // delete a user
    if($_POST && isset($_POST['DelUser'])) {
      $deleteUserQuery = "DELETE FROM users WHERE userID =:user";
      $deleteStatment = $db->prepare($deleteUserQuery);
      $deleteStatment->bindValue(":user",$Selected_User);
      $deleteStatment->execute();
      header("Location:admin.php");
    }
// update user password
    if($_POST && isset($_POST['ChangePassword']) && isset($_POST['NewPass'])) {
       $NewHasedPass = trim(filter_input(INPUT_POST,'NewPass',FILTER_SANITIZE_FULL_SPECIAL_CHARS));
       $NewHasedPass= password_hash($NewHasedPass,PASSWORD_BCRYPT);
       $upDatePassQuery = "UPDATE users SET password='$NewHasedPass' WHERE userID = :user";
       $UpdateStatment = $db->prepare($upDatePassQuery);
       $UpdateStatment->bindValue(":user",$Selected_User);
       $UpdateStatment->execute();

       echo "<script> alert('User Password has been changed')</script>";
    }
    // user profile picture upload and image insert.
    if($image_upload_detected)
    {
      $original_filename = $_FILES['image']['name'];
      file_upload_path($original_filename);

        $image_filename       = $_FILES['image']['name'];
        $temporary_image_path = $_FILES['image']['tmp_name'];
        $new_image_path       = file_upload_path($image_filename);

        move_uploaded_file($temporary_image_path, $new_image_path);

// resize the image on upload
        $Resizeimage = new ImageResize($new_image_path);
        $Resizeimage->resizeToHeight(500);
        $Resizeimage->resizeToWidth(300);
        // $new_image_path1 = "Resized".$new_image_path;
        $Resizeimage->save($new_image_path);


        $path = "images/".$_FILES['image']['name'];

  ///image path insert
      $imageQuery = "INSERT INTO images(filePath) VALUES(:FilePath)";
      $ProfilePicStatment = $db->prepare($imageQuery);
      $ProfilePicStatment->bindValue(":FilePath",$path);
      $ProfilePicStatment-> execute();

      $PicIdQuery = "SELECT MAX(PicID) from images";
      $ProfilePicStatment = $db->prepare($PicIdQuery);
      $ProfilePicStatment-> execute();
      $PicID = $ProfilePicStatment -> fetch();

      $imageQuery = "UPDATE users SET PicID = $PicID[0] WHERE userID = $Selected_User";
      $ProfilePicStatment = $db->prepare($imageQuery);
      $ProfilePicStatment->execute();

      header("Location:userShow.php?User=".$Selected_User);
      // print_r($PicID);
      // echo  $PicID[0];
      //  print_r($_FILES);
    }

//delete profile picture
    if( $_GET && isset($_GET['DelPic']))
    {
      $findPicID = "SELECT PicID FROM users WHERE userId = :user";
      $Statment1 = $db->prepare($findPicID);
      $Statment1->bindValue(":user",$Selected_User);
      $Statment1->execute();
      $ProfilePicId = $Statment1->fetch()[0];
      print_r($Statment1->errorInfo());

      $SEL = "SELECT filePath FROM images WHERE PicID = :Pic";
      $Statment2 = $db->prepare($SEL);
      $Statment2->bindValue(":Pic",$ProfilePicId);
      $Statment2->execute();
      $filepath = $Statment2->fetch()[0];
      print_r($Statment2->errorInfo());

      if($filepath !='images/default.jpg'){

      unlink($filepath);
      $ProfielPicDel = "UPDATE images SET filePath='images/default.jpg' WHERE PicID = :Pic";
      $Statment3 = $db->prepare($ProfielPicDel);
      $Statment3->bindValue(":Pic",$ProfilePicId);
      $Statment3->execute();
      print_r($Statment3->errorInfo());

      }else {
      echo "<script> alert('you cannot delete this picture')</script>";
      }
        header("Location:userShow.php?User=".$Selected_User);
    }

?>

<!DOCTYPE html>
<html>
  <head>
    <!-- <meta charset="utf-8"> -->
    <title><?= $UserName ?> Profile </title>
    <link rel="stylesheet" type="text/css" href="fix-it.css">
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

     <div id="content">
      <div id="project">
        <img src="<?=$UserRows[0]['filePath']?>" alt="profile pic" height="100" width="100"/>
        <h3><?=$UserRows[0]['UserName']?></h3>
        <p>you have been a member since <?=$UserRows[0]['DateCreated']?></p>
        <a href="userShow.php?User=<?=$_SESSION['logginID']?>&DelPic=T"> Remove Profile pic</a>
        </div>

        <div id="clear"></div>
              <h3> Add a profile picture</h3>
              <form method="post" enctype="multipart/form-data">
                    <label for="image">Image Filename:</label>
                    <input type="file" name="image" id="image">
                    <input type="submit" name="submit" value="Upload Image">
              </form>
              <?php if($editBox):?>
                <form method="post">
                  <input type="submit" name="DelUser" value="Delete User"/>
                  <input type="submit" name="ChangePassword" value="Reset Password"/>
                  <label > Re-enter Password </label>
                  <input type="text" name="NewPass"/>
                </form>
              <?php endif?>

        <h3><strong>Your Projects</strong></h3>

        <?php if(count($rows) !=0 ):
                foreach ($rows as $key ):?>
          <div class="project">
              <h3><a href="show.php?ProID=<?=$key['ProjectID']?>"> <?= $key['ProjectTitle']?></a></h3>
              <p> project was add on <?=$key['DateCreated']?></p>
              <img src="<?=$key['filePath']?>" alt="Test Pic"  height="200" width="200"/>
          </div>
        <?php   endforeach;
              endif ?>
          <div id="clear"></div>
          <p></p>
        <a href="createNewProject.php" id="newProject">create a new Project </a>

     </div>

<!--comment section   -->
    <!-- <div id="comments">

    </div> -->
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
