
<?php
session_start();
include("navbar.php");
if($_SERVER['REQUEST_METHOD'] == 'POST'){

  include("DBI.php");  //informacije o bazi podataka


  if(isset($_POST["register_butt"])){   //Submitana je forma Registracije
    unset($_SESSION['lozinka_err']);
    unset($_SESSION['email_err_pri']);
    unset($_SESSION['prikazi_login']);
    unset($_SESSION['email_err']);

    //Prikupljanje podataka iz Registracijske forme
    $ime = $mysqli->escape_string($_POST['ime']);
    $prezime = $mysqli->escape_string($_POST['prezime']);
    $licenca = $mysqli->escape_string($_POST['licenca']);
    $e_mail = $mysqli->escape_string($_POST['email_reg']);
    $lozinka = $mysqli->escape_string(md5($_POST['lozinka_reg'])); //kriptiranje lozinke md5
    $profile_img_name = $mysqli->escape_string(time(). ' ' .$_FILES['profile_img']['name']);  //dohvacanje imena slike i dodavanje vremena ispred -> da mogu bit dvije site slike
    //Uređivanje imena i prezimena prije unosenja u bazu podataka
    $ime = strtolower($ime);
    $ime = ucfirst($ime);
    $prezime = strtolower($prezime);
    $prezime = ucfirst($prezime);
    //SQL upit prema bazi podataka i pregled postoji li već korisnik sa unesenim Emailom
    $provjera = $mysqli->query("SELECT * FROM korisnici WHERE Email = '$e_mail'");
    if($provjera->num_rows > 0){
    	$_SESSION['email_err'] = "Korisnik sa unesenim email-om već postoji!";
    }
    else{
    //SQL upit ubacivanja u bazu podataka
    $mysqli->query("INSERT INTO `korisnici` (`Ime`, `Prezime`, `Licenca`, `Email`, `Lozinka`, `Slika`)
    VALUES ('$ime', '$prezime', '$licenca', '$e_mail', '$lozinka','$profile_img_name');");

    //dodavanje slike u Folder
    $target = 'profile_images/' . $profile_img_name;
    move_uploaded_file($_FILES['profile_img']['tmp_name'], $target);

    //Spremanje varijabli u Seasion dok se korisnik ne Logouta
    $_SESSION['ime'] = $ime;
    $_SESSION['prezime'] = $prezime;
    header("location: ARA_naslovna.php");
    }
  }

  if(isset($_POST["login_butt"])){  //Submitana je forma Prijave
    //Dohvacanje podataka iz forme za Prijavu
    unset($_SESSION['lozinka_err']);
    unset($_SESSION['email_err_pri']);
    unset($_SESSION['prikazi_login']);
    unset($_SESSION['email_err']);

    $e_mail = $mysqli->escape_string($_POST['email_pri']);
    $lozinka = $mysqli->escape_string(md5($_POST['lozinka_pri'])); //kriptiranje lozinke md5
    //SQL upit u bazu podataka da se pronađe korisnik sa unesenim e-mailom
    $result = $mysqli->query("SELECT * FROM korisnici WHERE Email = '$e_mail'");

    if($result->num_rows == 0){
      $_SESSION['email_err_pri'] = "Ne postoji korisnik sa tim e-mailom!";
      $_SESSION['prikazi_login'] = "da";
      header("location: registerform.php");
    }
    else{

      $user = $result->fetch_assoc();
      if($lozinka  == $user['Lozinka']){
        $_SESSION['ime'] = $ime;
        $_SESSION['prezime'] = $prezime;
        header("location: ARA_naslovna.php");
      }
      else{
        $_SESSION['lozinka_err'] = "Unesena je kriva lozinka!";
        $_SESSION['prikazi_login'] = "da";
        header("location: registerform.php");
      }
    }
  }

}
?>

<body id= "registracija">
    <div class="container" style="padding-top: 40px;padding-bottom: 40px;">
      <h1>Dobrodošli u ARA sustav!</h1>
      <div class="row text-center" style="padding-top: 40px">
        <div class="col-md-3"></div>
        <div class="col-md-6">
          <div id="container_form" class="container">
            <div class="row text-center" style = "padding-bottom: 20px;">
                <div class="col-md-6" style="padding: 0px 0px">
                  <button id="reg_butt" class="btn signbutt">Registriraj se</button>
                </div>
                <div class="col-md-6" style="padding: 0px 0px">
                  <button id="log_butt" class="btn signbutt">Prijavi se</button>
                </div>
            </div>
            <div class="row text-center">

              <div class="signup-form">
              <form id="reg_form" action="registerform.php" method ="post" onsubmit="return validacija_forme()" enctype="multipart/form-data">
                <div class="form-group">
                    <img src = "profile_images/placeholderimg.png" id = "profileDisplay" onclick="promjeni_sliku();">
                    <input type="file" name = "profile_img" id = "profile_img" style = "display: none;" onchange="prikazi_sliku(this)">
                </div>
                <label> Odaberite sliku profila </label>
                <div class="form-group">
                  <input id="ime" type="text" name ="ime" class="form-control" placeholder="Ime" required>
                  <div id ="ime_err" class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                  <input id="prezime" type="text" name="prezime" class="form-control" placeholder="Prezime" required>
                  <div id ="prezime_err" class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                  <select id ="licenca" name="licenca" class="form-control">
                      <option disabled hidden selected value="none">Odaberite vašu licencu ronjenja</option>
                      <option value="R1">R1</option>
                      <option value="R2">R2</option>
                      <option value="R3">R3</option>
                  </select>
                  <div id ="lic_err" class="invalid-feedback"></div>
                </div>
                <div class="form-group">
                  <input type="email" name="email_reg" class="form-control <?php if(isset($_SESSION['email_err'])){echo "is-invalid";}?>" placeholder="E-mail" required>
                  <div id ="email_err" class="invalid-feedback"><?php if(isset($_SESSION['email_err'])){echo $_SESSION['email_err'];} ?></div>
                </div>
                <div class="form-group">
                  <input type="password" name="lozinka_reg" class="form-control" placeholder="Lozinka" required>
                </div>
                <button type="submit" class="btn submit" name="register_butt">Registracija</button>
              </form>
              </div>

              <div class="login-form">
                <form id="log_form" action="registerform.php" method ="post" onsubmit="promjeni();" enctype="multipart/form-data">
                  <div class="form-group">
                    <input type="email" name="email_pri" class="form-control <?php if(isset($_SESSION['email_err_pri'])){echo "is-invalid";}?>" placeholder="E-mail" required>
                    <div  class="invalid-feedback"><?php if(isset($_SESSION['email_err_pri'])){echo $_SESSION['email_err_pri'];} ?></div>
                  </div>
                  <div class="form-group">
                    <input type="password" name="lozinka_pri" class="form-control <?php if(isset($_SESSION['lozinka_err'])){echo "is-invalid";}?>" placeholder="Lozinka" required>
                    <div  class="invalid-feedback"><?php if(isset($_SESSION['lozinka_err'])){echo $_SESSION['lozinka_err'];} ?></div>
                  </div>
                  <div class="form-group">
                    <input id="inputHiddenId" type="hidden"  class="form-control" value="<?php if(isset($_SESSION['prikazi_login'])){echo "login";}else{echo"register";} ?>">
                  </div>
                  <button type="submit" class="btn submit" name="login_butt">Prijava</button>
                </form>
              </div>

            </div>
          </div>
        </div>
        <div class="col-md-3">
        </div>
      </div>
    </div>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="Ara.js"></script>
</body>

</html>
