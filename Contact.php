<?php
session_start();
    use PHPMailer\PHPMailer\PHPMailer;
    //use PHPMailer\PHPMailer\SMTP;
    //use PHPMailer\PHPMailer\Exception;
    include("Navbar.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  if(isset($_POST["send_mail_butt"])){

      //Prikupljanje podataka iz Contact forme
      $ime = $_POST['name'];
      $ime = strtolower($ime);
      $ime = ucwords($ime);
      $email = $_POST['email'];
      $poruka = $_POST['contact_text'];

      //Importanje potrebnih fajlova
      require("Mailer/PHPMailer.php");
      require("Mailer/SMTP.php");
      //require("Mailer/Exception.php");

      //Slanje poruke na mail
      $mail = new PHPMailer();
      //$mail->SMTPDebug = 2;
      $mail -> isSMTP();
      $mail->SMTPAuth = true;
      $mail->SMTPOptions = array(
        'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
          )
      );
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;
      $mail->isHTML(true);
      $mail->Username = 'arasustav@gmail.com';
      $mail->Password = 'ARAsustav641998';
      $mail->setFrom('arasustav@gmail.com', 'ARA sustav');
      $mail->addReplyTo($email , $ime);
      $mail->addAddress('arasustav@gmail.com');
      $mail->Subject = 'ARA sustav - Poruka korisnika';
      $mail->Body = '<h3>Ime korisnika:</h3>' .$ime . "<h3>Poruka:</h3></br>" .$poruka;
      if($mail->Send()){
        $validation = true;
      }
      else{
        $validation = false;
        //echo $mail-> ErrorInfo;
      }
  }
}

?>

<body id= "kontakt">
  <div class="container" style="padding-top: 60px;padding-bottom: 40px;">
    <div class="row text-center">
        <div class="col-md-6"></div>
        <div class="col-md-6">
          <h1 style ="margin-bottom: 40px;">Kontaktirajte nas!</h1>
          <div class = "container container_form" >
          <form action="Contact.php" method ="post" class = "form_edit" onsubmit="return confirm('U slučaju pogrešno unesenog e-maila, nećemo vam biti u mogućnosti odogovoriti!\nProvjerite još jednom i potvrdite.')">
				        <div class="form-group">
					             <input type="text" name = "name" class="form-control"  placeholder="Ime i prezime" required>
				       </div>
				       <div class="form-group">
					            <input type="email" name = "email" class="form-control"  placeholder="E-mail" required>
				      </div>
				      <div class="form-group">
					           <textarea type="text" name = "contact_text" class="form-control" rows="3" placeholder="Kako vam možemo pomoći?" required></textarea>
				      </div>
				      <button type="submit" name = "send_mail_butt" class="btn submit">Pošalji</button>
              <div id = "validcontactmess" class = "<?php
                if(isset($validation) && $validation == true){
                    echo "alert alert-success";
                  }
                else if(isset($validation) && $validation == false){
                    echo "alert alert-danger";
                  }
                else echo "";?>"
              role ="alert">
              <?php if(isset($validation) && $validation == true){
                  echo "Poruka je uspješno poslana!</br>Odgovoriti ćemo u najkraćem mogućem roku!</br>Vaš ARA tim!";
                }
              else if(isset($validation) && $validation == false){
                  echo "Poruka nije uspjesno poslana!";
                }
              else echo "";?></div>
          </form>
        </div>
      </div>
    </div>

  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="Ara.js"></script>
</body>

</html>
