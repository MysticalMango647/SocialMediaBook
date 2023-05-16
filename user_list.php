<!-- http://127.0.0.1/SYSC4504_Labs/yunas_magsi_A03/user_list.php -->

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
            <li><a href="index.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
            <?php
             if(isset($_SESSION["FetechedAccountType"]) && $_SESSION["FetechedAccountType"]==0){
               echo '<li><a class="active" href="user_list.php">User List</a></li>';}?>
            <li><a href="logout.php">Log out</a></li>
         </ul>
     </div>
   </nav>

   

   <main>
      <section>
            <fieldset>
               <legend>&emsp;User List</legend>
               <?php

                if(isset($_SESSION["FetechedAccountType"]) && $_SESSION["FetechedAccountType"]==0){
               echo '
                <div class = "GameTableBox"> <table> <caption>Admin Stuff Here</caption>
                <thead>
                <tr>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Student Email</th>
                    <th>Program</th>
                </tr>
                </thead>
                <tbody>';
                
                  
                  //Printing the user's info
                  try{
                     // Server's Information
                     include "connection.php";

                     $conn = new mysqli($server_name, $username, $password, $database_name);

                     $sql = "SELECT * FROM users_info INNER JOIN users_program ON users_info.student_ID = users_program.student_ID;";
                     $statement = $conn->prepare($sql);
                     $statement->execute();
                     $results = $statement->get_result();

                     while(($row = $results->fetch_assoc())){
                        echo 
                           '<tr>
                                 <td>'.$row["student_ID"].'</td>
                                 <td>'.$row["first_name"].'</td>
                                 <td>'.$row["last_name"].'</td>
                                 <td>'.$row["student_email"].'</td>
                                 <td>'.$row["Program"].'</td>
                           </tr>'
                        ;
                     }

                     $conn -> close();
                  }catch (mysqli_sql_exception $e){
                     $error = $e->getMessage();
                     echo $error;
                  }
              echo' </tbody> </table></div>';
            }else{
                echo'<p style="color:red;"><a href="index.php">Permission Denied, Click Here to Return</a></p>';
            }
         ?>
                
            </fieldset>
      </section>
   </main>
</body>
</html>