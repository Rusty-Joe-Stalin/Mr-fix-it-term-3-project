<?php
 session_start();
 require("connect.php");

 $UserName= "Register";
 $regLink ="register.php";
 $loggin ="Log In";
 $logLink ="login.php";

// log out statment
if($_GET && isset($_GET['out']) && $_GET['out']=='t'){
    $regLink ="register.php";
    $logLink ="login.php";
    $UserName= "Register";
    $loggin ="Log In";
    header("Location:index.php");
    session_destroy();
}
// change the log in link and register link when a usrename is set.
if(isset($_SESSION["UserName"]))
{
  $UserName = $_SESSION["UserName"];
  $regLink ="userShow.php?User=".$_SESSION['logginID'];
  $loggin="Log Out";
  $logLink="index.php?out=t";
}
// adimin session
if(isset($_SESSION["Admin"]) && $_SESSION["Admin"] == true){
  $regLink ="admin.php";
}
// catagory search
if( $_GET && isset($_GET['CatId']))
{
  $CatID = filter_input(INPUT_GET ,'CatId',FILTER_SANITIZE_NUMBER_INT);
  $query= "SELECT * FROM projects JOIN users USING (UseriD) JOIN images WHERE catagorID = :ID AND projects.PicID =  images.PicID";
  $Statment = $db->prepare($query);
  $Statment->bindValue(":ID", $CatID);
  $Statment->execute();
  $rows = $Statment -> fetchAll();
}
else
{
    $query= "SELECT * FROM projects JOIN users USING(useriD)  JOIN images WHERE projects.PicID = images.PicID";
    $Statment = $db->prepare($query);
    $Statment->execute();
    $rows = $Statment -> fetchAll();
}

// search query when a search perameter ind entered.
$SearchPost= null;
 if ($_POST && isset($_POST['search']))
 {
   $search = filter_input(INPUT_POST , 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $catId= filter_input(INPUT_POST , 'catagory' ,FILTER_SANITIZE_NUMBER_INT);

   if( $catId==0 ){
   $searchQ = "SELECT * FROM projects JOIN users USING(useriD)  JOIN images WHERE projects.PicID = images.PicID AND ProjectTitle LIKE '%{$search}%'";
   }
   else{
        $searchQ = "SELECT * FROM projects JOIN users USING(useriD)  JOIN images
        WHERE projects.PicID = images.PicID AND ProjectTitle LIKE '%{$search}%' AND catagorID = $catId";
   }
   $searchStatment = $db->prepare($searchQ);
   $searchStatment->execute();
   $SearchPost= $searchStatment->fetchAll();
  //  print_r($searchStatment->errorInfo());
 }
 // print_r($SearchPost);
?>

<!DOCTYPE html>
<html>
  <head>
    <!-- <meta charset="utf-8"> -->
    <title>Mr. Fix-it</title>
    <link rel="stylesheet" type="text/css" href="fix-it.css">
    <!-- <link rel="stylesheet" type="text/css" href="fix-it.css"> -->
  </head>
  <body>
    <div id="page">
      <div id="log">
        <ul>
          <li><a href="<?=$logLink?>"><?=$loggin?></a></li>
            <li>|</li>
          <li><a href="<?=$regLink?>"><?=$UserName?></a></li>
          <li></li>
          <li>
        <form method="post">
            <label>Search</label>
            <input type="text" name="search"/>
            <input type="submit" value="submit"/>
            <label>Search catagory</label>
            <select name="catagory">
                <option value="0" >All Projects</option>
                <option value="1">Kitchen</option>
                <option value="3">Garden</option>
                <option value="4">Bathroom</option>
                <option value="6">Crafts</option>
                <option value="8">WorkShop</option>
            </select>
        </form>
      </li>
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
       <?php if (isset($_POST['search']) && $SearchPost != null ):?>

<!-- display all of the projects searched for  -->
        <?php foreach ($SearchPost as $key ):?>
          <div class="project">
           <h3><a href="show.php?ProID=<?=$key['ProjectID'] ?>"> <?= $key['ProjectTitle']?></a></h3>
           <p> project was add on <?=$key['DateUploaded']?></p>
           <p> created by <?=$key['UserName']?></p>
           <img src="<?=$key['filePath']?>" alt="Test Pic"  height="200" width="200"/>
         </div>
         <?php endforeach ?>
 <!-- dispaly all projects avalible -->
       <?php elseif (isset($_POST['search']) == false ): ?>
              <?php foreach ($rows as $key ):?>
                <div class="project">
                <h3><a href="show.php?ProID=<?=$key['ProjectID'] ?>"> <?= $key['ProjectTitle']?></a></h3>
                <p> project was add on <?=$key['DateUploaded']?></p>
                <p> created by <?=$key['UserName']?></p>
                <img src="<?=$key['filePath']?>" alt="Test Pic"  height="200" width="200"/>
              </div>
            <?php endforeach ?>
          <!-- if no results are avalable   -->
       <?php else: ?>
          <p> No results </p>
      <?php endif ?>
      <div id="clear">
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
  </div>
  </body>
</html>
