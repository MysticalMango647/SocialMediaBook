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
   <title>SYSCBOOK - Main</title>
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
             <li><a class="active" href="index.php">Home</a></li>
             <li><a href="profile.php">Profile</a></li>
             <?php
             if(isset($_SESSION["FetechedAccountType"]) && $_SESSION["FetechedAccountType"]==0){
               echo '<li><a href="user_list.php">User List</a></li>';}?>
             <li><a href="logout.php">Log out</a></li>
         </ul>
     </div>
   </nav>


   <main>
   <?php
      include("connection.php");

      $conn = new mysqli($server_name, $username, $password, $database_name);
      if($conn->connect_error){
         //echo"cant connect to server";
         die("Error: Couldn't Connect. " . $conn->connect_error);
      }
      
      
      function BeginMagic($conn, $student_ID){
         //$student_ID=$_SESSION["fetechedStudentID"];
         //echo 'studentId from session is: '.$student_ID;
         //sending data to db for user post submission
         $sql = 'INSERT INTO users_posts VALUES (?, ?, ?, ?);';
         $statement = $conn->prepare($sql);
         $statement->bind_param('iiss',$_POST['post_ID'],$student_ID,$_POST['new_post'],$_POST['$post_date']);
         $statement->execute();
         //echo "<br>begin magic done: Post logged successfully";
      }

      function GetLastFewPost($conn, $student_ID){
         //echo '<br>In retriving last 5 post';
         $sql = "SELECT new_post FROM `users_posts` ORDER BY post_date DESC;";
         $results = $conn->query($sql);
         $counter = 0;
         $postMessageList = [];
         $postCounter = 0;
         while ($row = $results->fetch_assoc()) {
               //echo "<br>Post Message: " . $row['new_post'];
               $postMessageList[$postCounter] = $row['new_post'];
               $postCounter = $postCounter + 1;
               $counter = $counter + 1;
        }
        return([$postMessageList, $postCounter]);
      }

      //main
      $student_ID=$_SESSION["fetechedStudentID"];
      if ($_SERVER["REQUEST_METHOD"]== "POST"){
         if (isset($_POST["submit-index"]) && isset($_SESSION["fetechedStudentID"])){
             BeginMagic($conn, $student_ID);
         }
      }
      
      if (isset($_SESSION["fetechedStudentID"])){

         try{
         $student_ID=$_SESSION["fetechedStudentID"];
         $PreviousPostData = GetLastFewPost($conn, $student_ID);
         //testing list
         //echo "<br>".$PreviousPostData[0][0] . " list of data";
         //echo "<br> number of post: " . $PreviousPostData[1];  
         $displayPostNumber = $PreviousPostData[1];
         $displayPostMessage = $PreviousPostData[0];
         if ($displayPostNumber > 10){
            $displayPostNumber = 10;
         }} catch (Exception $e){
            //echo "<br> No data extist for user";
            $displayPostNumber = 0;
         }
      }else{
         $displayPostNumber = 0;
      }

      $conn -> close();
   ?>
      <section>
         <form method="POST" action="">
            <fieldset>
               <legend>&emsp;New Post</legend>
               <div class="QuestionBox">
                  
                  <p>
                     <textarea name="new_post" rows="4" cols="50"> What's on your mind? (max 500 char) </textarea>
                  </p>
                  <table>
                  <tr>
                     <td> 
                        <input type="submit", name="submit-index">
                     </td>
                     <td> 
                        <input type="reset", name="reset"> 
                     </td>
                  </tr>
                  </table>
                  </div>
            </fieldset>
         </form>
      </section>
      <section>
         <?php
         for($i=0; $i < $displayPostNumber; $i++){
         echo '<details open="">';
         echo '<summary>Post' . $i+1 .'</summary>';
         echo '<p>'. $displayPostMessage[$i]. '</p>';
         echo '</details>' ;        
         }
         ?>
         <!--
         <details open="">
            <summary>Post 1</summary>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam rhoncus, ipsum non efficitur sagittis, elit nisl sodales leo, in porta turpis arcu vitae purus. Nullam nec odio non eros maximus accumsan. Donec enim dolor, auctor ut ligula ac, lobortis auctor nulla. In et dignissim erat, nec congue lectus. Aenean ac sodales nisi. Curabitur vel blandit dui. Donec iaculis pretium fringilla. Aenean sed eros enim. Donec viverra, tellus et sollicitudin mattis, sem tortor accumsan dui, quis elementum velit mi vel tellus. Fusce vitae rutrum dui, non sodales ligula. Duis porttitor facilisis justo, vel lacinia mauris accumsan ac. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nulla enim risus, varius laoreet finibus et, eleifend sed leo.</p>
         </details>
         <br>
         <details open="">
            <summary>Post 2</summary>
            <p>Etiam et odio nisi. Duis fermentum, ipsum id posuere dictum, massa purus fringilla ex, at elementum nibh nunc laoreet turpis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla id quam sit amet sem ornare eleifend. Mauris finibus dapibus augue, vel pellentesque erat pulvinar in. Phasellus ultrices lorem nunc, eu venenatis lorem sodales non. Quisque at rutrum orci. Suspendisse lobortis vestibulum lectus pharetra posuere. Aenean lacinia dui id lorem rhoncus posuere. Aliquam vitae felis in orci blandit finibus. Fusce id purus sit amet orci finibus lacinia eget ut metus. Integer sodales, orci eget elementum fermentum, neque diam rutrum risus, quis eleifend erat lectus eu nibh. Maecenas vel purus id velit sagittis dictum in ut tellus.</p>
         </details>
         <br>
         <details open="">
            <summary>Post 3</summary>
            <p>Etiam blandit, purus eu bibendum blandit, arcu massa pellentesque risus, non gravida magna augue et elit. Ut sollicitudin quis arcu vitae fermentum. Proin egestas viverra faucibus. Fusce ut velit neque. Aliquam in mi accumsan, porta turpis vel, sollicitudin nunc. Pellentesque tincidunt nisl at ligula interdum, ut rhoncus magna maximus. In maximus luctus lobortis. Phasellus ipsum ante, tincidunt sit amet fringilla at, suscipit eget tortor. Cras magna arcu, sollicitudin non nisl sit amet, varius sodales odio. In sed dictum arcu, sed interdum felis. Maecenas efficitur massa ut augue molestie, ac porttitor sapien accumsan. Vivamus at convallis urna. Nullam vitae libero dolor. Morbi dignissim rhoncus justo, id pretium enim venenatis ac. Duis sit amet quam lacus. Nam tempor a eros ut dapibus.</p>
         </details>
         <br>
         -->
      </section>

   </main>
</body>
</html>