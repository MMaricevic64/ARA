<?php
session_start();
if(isset($_POST['user_comm'])){
  include("DBI.php");
  $locality_id = $_POST['locality_id'];
  $comment = $_POST['user_comm'];
  $e_mail = $_SESSION['e_mail'];

  //Dohvati ID_korisnika
  $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
  $userID = $user_query -> fetch_assoc();
  $ID = $userID['ID_korisnika'];

  //Spremi komentar u bazu
  $mysqli->query("INSERT INTO `komentari` (`Komentar`, `ID_lokaliteta`, `ID_korisnika`)
  VALUES ('$comment', '$locality_id', '$ID');");

?>

  <div class="comment_box">
    <p class="comment_text" style="padding-bottom:0px;font-size:16px;">
        <?php echo $_SESSION['ime']. " " . $_SESSION['prezime']; ?>
    </p>
    <p class="comment_text"><?php echo $comment;  ?></p>
    </div>

<?php
  exit;
}
?>
