<?php
session_start();
require('connect.php');
/// session checking and working varables decalration.
$NotvaildCom = '';
$UserName= "Register";
$regLink ="register.php";
$loggin ="Log In";
$logLink ="login.php";
$Selected_Post = trim(filter_input(INPUT_GET,"ProID", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
// log out user script.
if($_GET && isset($_GET['out']) && $_GET['out']=='t'){

   $regLink ="register.php";
   $logLink ="login.php";
   $UserName= "Register";
   $loggin ="Log In";
    session_destroy();
   header("Location:show.php?ProID=$Selected_Post");
}
// if the user is set then change the log in and registry links
if(isset($_SESSION["UserName"])){
 $UserName = $_SESSION["UserName"];
 $regLink ="userShow.php?User=".$_SESSION['logginID'];
 $loggin="Log Out";
 $logLink="index.php?out=t";
}
// if the user is an admin.
if(isset($_SESSION["Admin"]) &&  $_SESSION["Admin"] == true){
  $regLink ="admin.php";
}
try {
  // print_r($_SESSION["logginID"]);
        if($Selected_Post==false)
        {
          header("Location: index.php");
        }
          // insert statment and logic for comments.
        if($_POST && isset($_POST['newComment']))
        {
          $PostedComment = trim(filter_input(INPUT_POST,"newComment", FILTER_SANITIZE_FULL_SPECIAL_CHARS));

            if(strlen($PostedComment) > 0 )
            {
              $userID = $_SESSION['logginID'];
              $query= "INSERT INTO comments(UserID,ProjectID,comments,DateCreated) VALUES(:UserID, $Selected_Post , :newPost ,NOW())";
              $Statment = $db->prepare($query);
              $Statment->bindValue(':UserID', $userID);
              $Statment->bindValue(':newPost', $PostedComment);
              $Statment->execute();
            }
            else{
                $NotvaildCom = "show";
            }
        }
// changed the query.
    $query = "SELECT * FROM projects
              JOIN users USING (UseriD)
              JOIN images
              WHERE ProjectID= :id
              AND projects.PicID = images.PicID";
    $Statment = $db -> prepare($query);
    $Statment->bindValue(":id",$Selected_Post);
    $Statment-> execute();
    $rows = $Statment->fetchAll();

    $CommentQ = "SELECT * FROM comments JOIN users USING (userID) WHERE comments.ProjectID= :id ";
    $CommentStatement = $db -> prepare($CommentQ);
    $CommentStatement->bindValue(":id",$Selected_Post);
    $CommentStatement-> execute();
    $ComROW = $CommentStatement->fetchAll();

    $pageQuery = "SELECT * FROM page JOIN images Using (PicID) WHERE ProjectID = :ProId";
    $steps = $db-> prepare($pageQuery);
    $steps ->bindValue(":ProId",$Selected_Post);
    $steps ->execute();
    $Pages = $steps -> fetchAll();
    // print_r($steps->errorInfo());

    // delete comment scripts the redirect users back to the page.
    $delete = filter_input(INPUT_GET,'del');
    $selectedComment = filter_input(INPUT_GET,'ID');
    if(isset($_GET['del'])&& $delete = 'del')
    {
      $delquery= "DELETE FROM comments WHERE comments.userID = :userID AND CommentID = :comID";
      $Statment = $db->prepare($delquery);
      $Statment -> bindValue(':userID', $_SESSION['logginID']);
      $Statment -> bindValue(':comID' , $selectedComment);
      $Statment->execute();
      header("Location:show.php?ProID=$Selected_Post");
    }

}
 catch (Exception $e) {
  print $e->getMessage();
}
$i=0;
 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <!-- <meta charset="utf-8"> -->
     <title><?= $rows[0]['ProjectTitle']?></title>
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
      <div id="ShowDescription">
         <?php foreach ($rows as $key ):?>
           <h2><?= $key['ProjectTitle']?></h2>
           <p> project was add on <?=$key['DateUploaded']?></p>
           <p>by <?=$key['UserName']?></p>
           <img src="<?=$key['filePath']?>" alt="Test Pic"  height="400" width="400"/>
          <h3>Introduction</h3>
           <p><?=$key['ProjectContent']?></p>
         <?php endforeach ?>
      </div>

      <div id="steps">
        <?php foreach ($Pages as $key):$i++; ?>
          <h3> Step <?=$i?> </h3>
          <img src=<?=$key['filePath']?> alt="step Picture" height="300" width="300"/>
          <p><?=$key['StepDescription']?></p>
        <?php endforeach ?>
        </div>

 <!--comment section  -->

       <?php foreach ($ComROW as $key ):?>
         <div id="comments">
           <h3><?= $key['UserName']?></h3>
           <h4>posted on <?=$key['DateCreated']?></h4>

           <?php if($key['UserID'] == isset($_SESSION['logginID'])):?>
             <a href="show.php?ProID=<?=$Selected_Post?>&del=true&ID=<?=$key['CommentID']?>">delete</a>
           <?php else:?>

           <?php endif?>

           <p><?=$key['Comments']?></p>
         </div>
       <?php endforeach ?>

       <div id="addComment">
        <form method="post">
         <?php if (isset($_SESSION['logginID'])): ?>
           <label for="coomment">give feed back </label>
           <input type=text name="newComment"/>
           <input type="submit" value="submit"/>
         <?php endif?>

           <?php if($NotvaildCom =='show'):?>
            <p>please enter a valid comment</p>
           <?php endif ?>
         </form>
       </div>

 <!-- footer -->
     <div id="space"> </div>
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
