<?php
// Start the session
session_start();

if (!isset($_SESSION["fetechedStudentID"])){
   header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head >
   <meta charset="utf-8">
   <title>Update SYSCBOOK profile</title>
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
             <li><a href="index.php">Home</a></li>
             <li><a class="active" href="profile.php">Profile</a></li>
             <?php
             if(isset($_SESSION["FetechedAccountType"]) && $_SESSION["FetechedAccountType"]==0){
               echo '<li><a href="user_list.php">User List</a></li>';}?>
             <li><a href="logout.php">Log out</a></li>
         </ul>
     </div>
   </nav>

   <main>
      <section>
         <h2>Update Profile information</h2>

         <?php

         function startConnection(){
            include("connection.php");
            
            $conn = new mysqli($server_name, $username, $password, $database_name);
            if($conn->connect_error){
               echo"cant connect to server";
               die("Error: Couldn't Connect. " . $conn->connect_error);
            }
            //echo "<br>connected to db";
            return($conn);
         }

         function closeConnection($conn){
            //echo "<br>clossing connection to db";
            $conn -> close();
         }

         function GetPreLoadedData($conn, $student_ID){
            #$student_ID=$_SESSION["fetechedStudentID"];

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

         function BeginMagic($conn){
            
            if (isset($_SESSION["fetechedStudentID"])){
               $UseOrCreateStudentID = $_SESSION["fetechedStudentID"];
               $infoIdUpdating = true;
               //echo "<br>In beginMagic function where session stuID is: " . $_SESSION["fetechedStudentID"];              
            }else{
               $UseOrCreateStudentID = 0;
               $infoIdUpdating = false;
               //echo"<br>new userID is being created: " . $UseOrCreateStudentID;
               //$UseOrCreateStudentID = $_POST["student_ID"];
            }
            //echo "<br>Using student_ID: " . $UseOrCreateStudentID;
            
            //echo "<br>doing magic, importing all data to db";
            if (isset($_SESSION["fetechedStudentID"])){
               //update user_address to db
               $sql = "UPDATE USERS_ADDRESS set street_number=?, street_name=?, city=?, provence=?, postal_code=?  Where student_ID= $UseOrCreateStudentID;";
               $statement = $conn->prepare($sql);
               $statement->bind_param('issss',$_POST["street_number"],$_POST["street_name"],$_POST["city"],$_POST["provence"],$_POST["postal_code"]);
               $statement->execute();

               //update user_avatar to db
               $sql = "UPDATE users_avatar set avatar=?  Where student_ID= $UseOrCreateStudentID;";
               $statement = $conn->prepare($sql);
               $statement->bind_param('i',$_POST["avatar"]);
               $statement->execute();
               
               //update users_info to db
               $sql = "UPDATE users_info set student_email=?, first_name=?, last_name=?, DOB=?  Where student_ID= $UseOrCreateStudentID;";
               $statement = $conn->prepare($sql);
               $statement->bind_param('ssss',$_POST["student_email"],$_POST["first_name"],$_POST["last_name"],$_POST["DOB"]);
               $statement->execute();

               //update users_program to db
               $sql = "UPDATE users_program set Program=? Where student_ID= $UseOrCreateStudentID;";
               $statement = $conn->prepare($sql);
               $statement->bind_param('s',$_POST["Program"]);
               $statement->execute();
               //variables to know data has been pushed, and select once someone refreshes to set values
               $allDataSet = true;
            }
            /*
            else{
               echo "error: No session student_ID avaliable.";
               $allDataSet = false;
            }*/
            return($allDataSet);
            
         }

         function UpdateValueOnRefresh($conn, $student_ID){
            //echo "<br>begin updating values on refresh";
            //$student_ID=$_SESSION["fetechedStudentID"];

            $sql = "SELECT * FROM users_avatar WHERE student_Id=$student_ID";
            $statement = $conn->query($sql);
            
            while($row = $statement->fetch_assoc()) {
               $avatar=$row["avatar"];
            }

            $sql = "SELECT * FROM `users_address` WHERE student_Id=$student_ID";
            $statement = $conn->query($sql);

            while($row = $statement->fetch_assoc()) {
               $street_number	= $row["street_number"];
               $street_name	= $row["street_name"];
               $city       	= $row["city"];
               $provence	   = $row["provence"];
               $postal_code	= $row["postal_code"];
            }

            $listofVal2 = [$avatar, $street_number, $street_name, $city, $provence, $postal_code];
            
            //echo "<br>finished with values on refresh";
            return($listofVal2);
         }

        

         // main
         $conn = startConnection();
         
      

         if (isset($_SESSION["fetechedStudentID"])){
            $getVal = GetPreLoadedData($conn, $_SESSION["fetechedStudentID"]);
            $student_email=$getVal[0]; 
            $first_name=$getVal[1];  
            $last_name=$getVal[2]; 

            $DOB=$getVal[3];
            $Program=$getVal[4]; 
            //echo $Program;
         }else{
            echo "<br>no session data avaliabile, please register";
         }

         if ($_SERVER["REQUEST_METHOD"]== "POST"){
            if (isset($_POST["submit-profile"]) ){
               $allDataUpdated=BeginMagic($conn);
               //echo "<br> data uploaded: ".$allDataUpdated ;
            }
         }
         
         if (isset($allDataUpdated)==true){
            $getVal = GetPreLoadedData($conn, $_SESSION["fetechedStudentID"]);
            $student_email=$getVal[0]; 
            $first_name=$getVal[1];  
            $last_name=$getVal[2]; 
            $DOB=$getVal[3];
            $Program=$getVal[4]; 

            $getOtherVal = UpdateValueOnRefresh($conn, $_SESSION["fetechedStudentID"]);
            
            $avatar=$getOtherVal[0]; 
            $street_number=$getOtherVal[1];  
            $street_name=$getOtherVal[2]; 
            $city=$getOtherVal[3];
            $provence=$getOtherVal[4]; 
            $postal_code=$getOtherVal[5];
            $_SESSION['profile']=true;
         }
         if (isset($_SESSION['profile'])==true){
            $getVal = GetPreLoadedData($conn, $_SESSION["fetechedStudentID"]);
            $student_email=$getVal[0]; 
            $first_name=$getVal[1];  
            $last_name=$getVal[2]; 
            $DOB=$getVal[3];
            $Program=$getVal[4]; 

            $getOtherVal = UpdateValueOnRefresh($conn, $_SESSION["fetechedStudentID"]);
            
            $avatar=$getOtherVal[0]; 
            $street_number=$getOtherVal[1];  
            $street_name=$getOtherVal[2]; 
            $city=$getOtherVal[3];
            $provence=$getOtherVal[4]; 
            $postal_code=$getOtherVal[5];

         }

         //if ($allDataUpdated == true){
         //   echo "all data updated";
         //}

         //Ending Connection
         closeConnection($conn);
      ?>

         <form method="POST" action="">
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
                        <input type="text" name="last_name"  value=<?php if (isset($last_name)){echo $last_name;} else{echo"";};?>>
                     </td>
                     <td>
                        <label>DOB: </label>
                        <input type="date" name="DOB"  value=<?php if (isset($DOB)){echo $DOB;} else{echo "YYYY-MM-DD";};?>>
                     </td>
                     
                  </tr> 
               </table>  
               
               </fieldset>
               <fieldset>
               <legend>&emsp;Address</legend>
                  <table>
                  <tr>
                     <td>
                        <label>&emsp;Street Number: </label>
                        <input type="text" name="street_number" value=<?php if (isset($street_number)){echo $street_number;} else{echo"";};?>>
                     </td>
                     <td>
                        <label>Street Name: </label>
                        <input type="text" name="street_name" value=<?php if (isset($street_name)){echo $street_name;} else{echo"";};?>>
                     </td>
                  </tr>
                  
                  
                  <tr>
                     <td>
                        <label>&emsp;City: </label>
                        <input type="text" name="city" value=<?php if (isset($city)){echo $city;} else{echo"";};?>>
                     </td>
                     <td>
                        <label>Province: </label>
                        <input type="text" name="provence" value=<?php if (isset($provence)){echo $provence;} else{echo"";};?>>
                     </td>
                     <td>
                        <label>Postal Code: </label>
                        <input type="text" name="postal_code" value=<?php if (isset($postal_code)){echo $postal_code;} else{echo"";};?>>
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
               
               <br>
                  <label>&emsp;Choose your Avatar</label>
                  <table>
                     <tr>
                     <!-- Refrenced https://www.w3schools.com/php/php_form_complete.asp -->
                     <td><input type="radio" name="avatar" value="1" <?php if (isset($avatar) && $avatar=="1"){echo "checked";} else{echo"";};?>><img src="images/img_avatar1.png" alt="av1"></td>
                     <td><input type="radio" name="avatar" value="2" <?php if (isset($avatar) && $avatar=="2"){echo "checked";} else{echo"";};?>><img src="images/img_avatar2.png" alt="av2"></td>
                     <td><input type="radio" name="avatar" value="3" <?php if (isset($avatar) && $avatar=="3"){echo "checked";} else{echo"";};?>><img src="images/img_avatar3.png" alt="av3"></td>
                     <td><input type="radio" name="avatar" value="4" <?php if (isset($avatar) && $avatar=="4"){echo "checked";} else{echo"";};?>><img src="images/img_avatar4.png" alt="av4"></td>
                     <td><input type="radio" name="avatar" value="5" <?php if (isset($avatar) && $avatar=="5"){echo "checked";} else{echo"";};?>><img src="images/img_avatar5.png" alt="av5"></td>
                  
                  </tr>
                  </table>
                 
                  <table>
                  <tr>
                     <td>
                        <input type="submit", name="submit-profile">
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