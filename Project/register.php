<?php
  session_start();
  require("connect.php");
  require("botdetect.php");
  $FormCaptcha = new Captcha("FormCaptcha");
  $FormCaptcha-> UserInputID = "CaptchaCode";

  if($_POST && isset($_POST['UserName']) && isset($_POST['Password']) && isset($_POST['Confirm'])&& isset($_POST['email']))
  {
    if (trim(isset($_POST['UserName']) !="")&& trim(isset($_POST['email']) !="") ){

    if(trim($_POST['Password']) == trim($_POST['Confirm']) && strlen(trim($_POST['Password']))>=7)
      {

        if($FormCaptcha->Validate()){

            $user = trim(filter_input(INPUT_POST,"UserName", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $Password = trim(filter_input(INPUT_POST,"Password", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $email = trim(filter_input(INPUT_POST,"email", FILTER_SANITIZE_FULL_SPECIAL_CHARS));
            $Password = password_hash($Password, PASSWORD_BCRYPT);

          try {
              $query= "INSERT INTO users(UserName, DateCreated, password, email) VALUES(:Name, now(),'$Password', :email)";
              $Statment = $db->prepare($query);
              $Statment->bindValue(':Name', $user);
              $Statment->bindValue(':email', $email);
              $Statment->execute();
                //  print_r($Statment->errorInfo());
                // echo $Password;
                 header("Location:login.php");
              }
            catch (Exception $e)
            {
                  print $e->getMessage();
                  echo "error in the insert bro";
            }

          }
          else {
                  echo "<script>alert('Invlaid Capcha')</script>";
          }

      } else if (strlen(trim($_POST['Password'])) <7) {
               echo "<script> alert('Invlaid Password - Passwords must be longer that 7 Characters')</script>";
              }
      }
      else
      {
         echo "<script> alert('Invalid user name  or email ')</script>";
      }
  }

 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <!-- <meta charset="utf-8"> -->
     <title>Mr. Fix-it</title>
     <link rel="stylesheet" type="text/css" href="fix-it.css"/>
     <link type="text/css" rel="Stylesheet" href="<?php echo CaptchaUrls::LayoutStylesheetUrl() ?>" />
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
             <li><a href="index.php?CatId=4">  Out Doors </a></li>
             <li><a href="index.php?CatId=6">  Crafts </a></li>
             <li><a href="index.php?CatId=8">  Work Shop </a></li>
             <li><a href="index.php"> All Projects </a></li>
           </ul>
         </nav>
       </div>
 <!-- section content  -->
 <!-- <p id="text"> So you want be a member? Enter a the form below to get started sharing your DIY ideas with our community. </p> -->
      <div id="form">
         <form method="post">
            <label> Username </label>
            <input type="text" name="UserName">
            <label> Password </label>
            <input type="password" name="Password">
            <label>  confirm Password </label>
            <input type="password" name="Confirm">
            <label> Email Address </label>
            <input type="text" name="email">
            <p> </p>
            <label for="CaptchaCode">Retype the characters from the picture:</label>
            <?php echo $FormCaptcha->Html(); ?>
            <input name="CaptchaCode" id="CaptchaCode" type="text"/>
            <input type="submit">
         </form>
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
