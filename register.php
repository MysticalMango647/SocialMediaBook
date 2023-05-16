<!-- http://127.0.0.1/SYSC4504_Labs/yunas_magsi_A03/register.php -->

<?php
// Start the session
session_start();


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
             <li><a href="login.php">Login</a></li>
             <li><a href="index.php">Home</a></li>
             <li><a href="profile.php">Profile</a></li>
             <li><a class="active" href="register.php">Register</a></li>
         </ul>
     </div>
   </nav>
   <main>

      <?php
         function BeginMagic(){
            include("connection.php");

            $conn = new mysqli($server_name, $username, $password, $database_name);
            if($conn->connect_error){
               echo"cant connect to server";
               die("Error: Couldn't Connect. " . $conn->connect_error);
            }

            
            $sql = "INSERT INTO USERS_INFO VALUES (?, ?, ?, ?, ?);";
            $statement = $conn->prepare($sql);
            $statement->bind_param('issss', $_POST["student_ID"],$_POST["student_email"],$_POST["first_name"],$_POST["last_name"],$_POST["DOB"]);
            $statement->execute();
            $result = $statement->get_result();
            

            $fetechedStudentID = mysqli_insert_id($conn);

            //echo "<br>Session val below ---------------------";
            $_SESSION["fetechedStudentID"] = $fetechedStudentID;
            //echo $_SESSION["fetechedStudentID"];

            $EmptyString = "NULL";
            $EmptyNumbers = 00000;

            //echo "New record has id: " . mysqli_insert_id($conn);
            //echo "<br>";
            //echo "Local id from var is : " . $fetechedStudentID;
            

            $sql = "INSERT INTO users_program VALUES (?, ?);";
            $statement = $conn->prepare($sql);
            $statement->bind_param('is', $fetechedStudentID , $_POST["Program"]);
            $statement->execute();
            $result = $statement->get_result();
            

            $sql = "INSERT INTO users_avatar VALUES (?, ?);";
            $statement = $conn->prepare($sql);
            $statement->bind_param('is', $fetechedStudentID, $EmptyNumbers);
            $statement->execute();
            $result = $statement->get_result();

            //echo "<br>";
            //echo "NULL set for user avatar";

            $sql = "INSERT INTO users_address VALUES (?, ?, ?, ?, ?, ?);";
            $statement = $conn->prepare($sql);
            $statement->bind_param('isssss', $fetechedStudentID,$EmptyNumbers,$EmptyString,$EmptyString,$EmptyString,$EmptyString);
            $statement->execute();
            $result = $statement->get_result();

            
            $passEncrypted = password_hash($_POST["password"], PASSWORD_BCRYPT);
            echo "The regular password is: " . $_POST["password"] . " , Hashed password: " . $passEncrypted;

            $sql = "INSERT INTO users_passwords VALUES (?,?);";
            $statement = $conn->prepare($sql);
            $statement->bind_param('is', $fetechedStudentID,$passEncrypted);
            $statement->execute();
            $result = $statement->get_result();
            //echo "<br>";
            //echo "NULL set for user users_address";

            $defaultPermssions = 1;

            $sql = "INSERT INTO users_permissions (student_ID, account_type) VALUES (?, ?)";
            $statement = $conn->prepare($sql);
            $statement->bind_param('ii',$fetechedStudentID, $defaultPermssions);
            $statement->execute();
            $result = $statement->get_result();
                  
            $conn -> close();
         }

         function GetPreLoadedData(){
            include("connection.php");

            $conn = new mysqli($server_name, $username, $password, $database_name);
            if($conn->connect_error){
               echo"cant connect to server";
               die("Error: Couldn't Connect. " . $conn->connect_error);
            }
            $student_ID=$_SESSION["fetechedStudentID"];

            $sql = "SELECT * FROM USERS_INFO WHERE student_Id=$student_ID";
            $statement = $conn->query($sql);
            
            while($row = $statement->fetch_assoc()) {
               //echo "<br>student_ID: " . $row["student_ID"]. " - student_email: " . $row["student_email"]. " - first_name: " . $row["first_name"]. " - last_name: " . $row["last_name"]." - DOB: " . $row["DOB"]."<br>";
               $student_email=$row["student_email"];
               $first_name=$row["first_name"];
               $last_name=$row["last_name"];
               $DOB=$row["DOB"];
            }

            $sql = "SELECT * FROM `users_program` WHERE student_Id=$student_ID";
            $statement = $conn->query($sql);

            while($row = $statement->fetch_assoc()) {
               //echo "<br>Program: " . $row["Program"]."<br>";
               $Program=$row["Program"];
            }
            $listofVal = [$student_email, $first_name, $last_name,$DOB,$Program];
            
            return($listofVal);
         }

         function CheckEmailExist(){
            include("connection.php");

            $conn = new mysqli($server_name, $username, $password, $database_name);
            if($conn->connect_error){
               echo"cant connect to server";
               die("Error: Couldn't Connect. " . $conn->connect_error);
            }
            if (isset($_POST['student_email'])){
            $student_email = $_POST["student_email"];

            $studentEmailExist = null;
            $sql = "SELECT EXISTS(SELECT * FROM USERS_INFO WHERE student_email=?);";
            
            $statement = $conn->prepare($sql);
            $statement->bind_param('s', $_POST['student_email']);
            $statement->execute();
            $statement->bind_result($studentEmailExist);
            $statement->fetch();
            echo $studentEmailExist . ": 0 -> Email does not exist, 1 -> Email Exist<br>";
            }
            if ($studentEmailExist >=1){
               #echo "Email already exist in DB, try new email";
               return true;
            }
            else{
               #echo "New email detected, continuing to registratoring";
               return false;
            }

         }

         //main

         
         $tempDisplayErrorMessage=false;

         if ($_SERVER["REQUEST_METHOD"]== "POST"){
            if (isset($_POST["submit-register"])) {
               $EmailStatus = CheckEmailExist();
               if($EmailStatus == false){
                  $tempDisplayErrorMessage=false;
                BeginMagic();
                $_SESSION['register']=true;
                header("Location: profile.php");
               }else{
                  echo "you silly goose, you already have an email registered.";
                  $tempDisplayErrorMessage = true;
                  //header("Location: login.php");
               }
                
            }
         }
         

         /*
         if (isset($_SESSION['register'])==true){
            $getVal = GetPreLoadedData();
            $student_email=$getVal[0]; 
            $first_name=$getVal[1];  
            $last_name=$getVal[2]; 
            $DOB=$getVal[3];
            $Program=$getVal[4]; 
         }*/
         
      ?>
      
      <section>
         <h2>Register a new profile</h2>
         <form method="POST" action="">
            <!-- profile.php-->
            <fieldset>
               <legend>&emsp;Personal information</legend>
               
                  <table>
                  <tr>
                     <td>
                        <label>&emsp;First Name: </label>
                        <input type="text" name="first_name" value=<?php if (isset($first_name)){echo $first_name;} else{echo"";};?>>
                     </td>
                     <td>
                        <label>Last Name: </label>
                        <input type="text" name="last_name" value=<?php if (isset($last_name)){echo $last_name;} else{echo"";};?>>
                     </td>
                     <td>
                        <label>DOB: </label>
                        <input type="date" name="DOB" value=<?php if (isset($DOB)){echo $DOB;} else{echo "YYYY-MM-DD";};?>>
                        <!--value="yyyy-mm-dd"-->
                     </td>
                  </tr>
                  </table>
               
            </fieldset>
            <fieldset>
               <legend>&emsp;Profile Information</legend>
               <p>
                  <label>Email address: </label>
                  <input type="email" name="student_email" value=<?php if (isset($student_email)){echo $student_email;} else{echo"";};?>>
               </p>
               <p>
                  <label>Program</label>
                  <select name="Program">
                  <option <?php if (isset($Program) && $Program=="Choose Program"){echo "selected";} else{echo"";};?>>Choose Program</option>
                     <option <?php if (isset($Program) && $Program=="Computer Systems Engineering"){echo "selected";} else{echo"";};?>>Computer Systems Engineering</option>
                     <option <?php if (isset($Program) && $Program=="Software Engineering"){echo "selected";} else{echo"";};?>>Software Engineering</option>
                     <option <?php if (isset($Program) && $Program=="Communications Engineering"){echo "selected";} else{echo"";};?>>Communications Engineering</option>
                     <option <?php if (isset($Program) && $Program=="Biomedical and Electrical"){echo "selected";} else{echo"";};?>>Biomedical and Electrical</option>
                     <option <?php if (isset($Program) && $Program=="Electrical Engineering"){echo "selected";} else{echo"";};?>>Electrical Engineering</option>
                     <option <?php if (isset($Program) && $Program=="Special"){echo "selected";}  else{echo"";};?>>Special</option>
                     </select>
               </p>
            </fieldset>
            <fieldset>
               <script type='text/JavaScript'>
                  var check = function() {
                     console.log("hello, from check function")
                     if (document.getElementById('password').value != document.getElementById('confirm_password').value) {
                        document.getElementById('invalidPass').innerHTML = 'Password Dont Match';
                        console.log("Password Dont Match");
                     } 
                     else{
                        document.getElementById('invalidPass').innerHTML = '';
                        console.log("Password Do Be Match");
                     }
                     //Derived from: http://jsfiddle.net/aelor/F6sEv/324/
                   }
               </script>

               

               <legend>&emsp;Give Me Passwords, Pls &#128056;</legend>
               <p>
                  <label>Password: </label>
                  <input type="password" name="password" id="password" onkeyup='check();' >
               </p>
               <p>
                  <label>Confirm Password: </label>
                  <input type="password" name="confirm_password" id="confirm_password" onkeyup='check();' >
               </p>
               
               <p id="invalidPass" style="color:red;">  </p>

               <?php 
                  //im done with javascript gonna do php
                   if ($tempDisplayErrorMessage==true){
                     echo '<p id="invalidEmail" style="color:red;"> Email Already Exist, Please Login or Use Different Email </p>';
                   }
                   else{
                     echo '<p id="invalidEmail" style="color:red;"> </p>';
                   }
               ?>
                  <table>
                  <tr>
                     <td>
                        <input type="submit", name="submit-register">
                     </td>
                     <td>
                        <input type="reset", name="reset">
                     </td>
                  </tr>
               </table>
            </fieldset>
         </form>
      </section>

      
      
   </main>
</body>
</html>