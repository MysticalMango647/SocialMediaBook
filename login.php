<!-- http://127.0.0.1/SYSC4504_Labs/yunas_magsi_A03/login.php -->

<?php
// Start the session
session_start();
?>


<?php
      function startConnection(){
         include("connection.php");
         $conn = new mysqli($server_name, $username, $password, $database_name);
         if($conn->connect_error){
            echo"cant connect to server";
            die("Error: Couldn't Connect. " . $conn->connect_error);
         }
         return($conn);
      }
      function closeConnection($conn){
         $conn -> close();
      }

      function validateSignIn($studentID, $password, $conn){
         
         $DBpassword = null;
         try{
            $sql = "SELECT password From users_passwords Where student_ID = ?";
            $statement = $conn->prepare($sql);
            $statement->bind_param('s', $studentID);
            $statement->execute();
            $statement->bind_result($DBpassword);
            $statement->fetch();
            } catch(mysqli_sql_exception $e){
               $error = $e->getMessage();
               echo $error;
            }
   
            //echo "<br>" . $DBpassword . "fetched Password";

            if (password_verify($password, $DBpassword)){
               $_SESSION["fetechedStudentID"] = $studentID;
               return true;
            }else{
               return false;
            }   
      }

      function validateEmail($email, $conn){
         $studentEmailExist = null;
         try{
         $sql = "SELECT student_ID From USERS_INFO Where student_email = ?";
         $statement = $conn->prepare($sql);
         $statement->bind_param('s', $email);
         $statement->execute();
         $statement->bind_result($studentEmailExist);
         $statement->fetch();
         } catch(mysqli_sql_exception $e){
            $error = $e->getMessage();
            echo $error; 
         }

         //echo "<br>" . $studentEmailExist . "fetched student ID";
         return $studentEmailExist;

      }

      //main
      $tempDisplayErrorMessage = false;
      if ($_SERVER["REQUEST_METHOD"]== "POST"){
         
         if (isset($_POST["submit-login"])){
            $conn = startConnection();
            $email = $_POST['student_email'];
            $password = $_POST['password'];
            $studentIDfromEmail = validateEmail($email, $conn);
            if ($studentIDfromEmail != null){
               $validSignOn = validateSignIn($studentIDfromEmail, $password, $conn);
               //echo"<br> in 1st if statement";
               if ($validSignOn == true){
                  $sql = "SELECT account_type From users_permissions Where student_ID = ?;";
                  $statement = $conn->prepare($sql);
                  $statement->bind_param('i',$studentIDfromEmail);
                  $statement->execute();
                  $result = $statement->get_result();
                  $row = $result->fetch_assoc();
                  $_SESSION["FetechedAccountType"] = $row["account_type"];
                  //echo"<br> in 2nd if statement";
                  $tempDisplayErrorMessage = false;
                  header("Location: index.php");
               }else{
                  //echo"<br> in 2nd else statement";
                  $tempDisplayErrorMessage = true;
               }
               
            }
            else{
               //echo"<br> in 1st else statement";
               $tempDisplayErrorMessage = true;
            }
            closeConnection($conn);
       }}
   ?>

<!DOCTYPE html>
<html lang="en">
<head >
   <meta charset="utf-8">
   <title>Register on SYSCBOOK</title>
   <link rel="stylesheet" href="assets/css/reset.css" />
   <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
   <header>
      <h1>SYSCBOOK</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <nav>
      <div id="navBarLeft">
         <ul>
            <li><a class="active" href="login.php">Login</a></li>
            <li><a href="index.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="register.php">Register</a></li>
         </ul>
     </div>
   </nav>

   

   <main>
      <section>
         <form method="post" action="">
            <fieldset>
               <legend>&emsp;Login Info</legend>
                  <table>
                  <tr>
                     <td>
                        <label>Email: </label>
                        <input type="text" name="student_email">
                     </td>
                  </tr>   
                  </tr>             
                     <td>
                        <label>Password: </label>
                        <input type="password" name="password">
                     </td>
                  </tr>
                  </table>
                  <?php 
                   if ($tempDisplayErrorMessage==true){
                     echo '<p style="color:red;"> Incorrect Email or Password, Please try again </p>';
                   }
                   else{
                     echo '<p style="color:red;"> </p>';
                   }
                  ?>
                  <br>
                  <p style="color:red;"><a href="register.php">click here to register for new account here</a></p>
                  <table>
                  <tr>
                     <td>
                        <input type="submit", name="submit-login">
                     </td>
                     <td>
                        <input type="reset">
                     </td>
                  </tr>
               </table>
               
            </fieldset>
         </form>
      </section>
   </main>
</body>
</html>