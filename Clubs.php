<?php
session_start();
include("Navbar.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  include("DBI.php");  //informacije o bazi podataka

  if(isset($_POST["clubleave"])){   //Izlaz iz kluba
    $klub = $_POST['clubleave'];
    $e_mail = $_SESSION['e_mail'];

    $izadi_query = $mysqli->query("UPDATE korisnici SET ID_kluba = NULL WHERE Email = '$e_mail'");

    $br_clanova_query = $mysqli->query("SELECT COUNT(*) AS broj_clanova FROM korisnici WHERE ID_kluba = $klub");
    $br_clanova = $br_clanova_query->fetch_assoc();
    if($br_clanova['broj_clanova'] == 0){
        $izbrisi_voditelja = $mysqli->query("UPDATE ron_klubovi SET ID_Voditelja = NULL WHERE ID_kluba = $klub");
    }
  }

  if(isset($_POST["clubreqdel"])){   //Izlaz iz kluba
    $zahtjev = $_POST['clubreqdel'];
    $cancel_query = $mysqli->query("DELETE FROM zahtjevi WHERE ID_zahtjeva = $zahtjev");
  }

  if(isset($_POST["clubidjoin"])){   //Pridruzivanje klubu
    $klub = $_POST['clubidjoin'];
    $e_mail = $_SESSION['e_mail'];

    //Ako je osoba jedina u timu ona postaje administrator tima
    $br_clanova_query = $mysqli->query("SELECT COUNT(*) AS broj_clanova FROM korisnici WHERE ID_kluba = $klub");
    $br_clanova = $br_clanova_query->fetch_assoc();
    if($br_clanova['broj_clanova'] == 0){
      //Dohvati ID logiranog korisnika i postavi ga kao voditelja tima
      $id_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
      $id_polje = $id_query->fetch_assoc();
      $id = $id_polje['ID_korisnika'];
      $dodijeli_voditelja = $mysqli->query("UPDATE ron_klubovi SET ID_voditelja = $id WHERE ID_kluba = $klub");
      $pridruzi_klubu = $mysqli->query("UPDATE korisnici SET ID_kluba = $klub WHERE ID_korisnika = $id");
    }
    else{
      $opis = "Pridruzivanje klubu";

      $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
      $userID = $user_query -> fetch_assoc();
      $ID = $userID['ID_korisnika'];

      //Posalji zahtjev za clanstvo
      $mysqli->query("INSERT INTO `zahtjevi` (`Opis`, `ID_podnositelja`, `ID_kluba`)
      VALUES ('$opis', '$ID', '$klub');");
    }
    //header("location: Clubs.php");
  }

  if(isset($_POST["create_club_butt"])){
    $ime_kluba = $_POST['clubname'];
    $e_mail = $_SESSION['e_mail'];

    //Kreiraj ime slike kluba koje ce se upisati u bazu
    if (is_uploaded_file($_FILES['clubimg']['tmp_name'])) {
      $club_img_name = $mysqli->escape_string(time(). ' ' .$_FILES['clubimg']['name']);  //dohvacanje imena slike i dodavanje vremena ispred -> da mogu bit dvije site slike
    }
    else{
      $club_img_name = $mysqli->escape_string('placeholderclubimg.png');
    }

    //Dohvacanje ID_korisnika, kako bi ga se postavilo za voditelja i pridruzilo klubu
    $id_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
    $id_polje = $id_query->fetch_assoc();
    $id = $id_polje['ID_korisnika'];

    //Dodavanje kluba u bazu podataka
    $mysqli->query("INSERT INTO `ron_klubovi` (`Ime`, `Slika`,`ID_voditelja`)
    VALUES ('$ime_kluba', '$club_img_name','$id');");

    //Dohvati ID dodanog kluba
    $novi_tim_query = $mysqli->query("SELECT ID_kluba FROM ron_klubovi WHERE ID_voditelja = '$id'");
    $novi_tim_polje = $novi_tim_query -> fetch_assoc();
    $novi_tim_id = $novi_tim_polje['ID_kluba'];
    echo $novi_tim_id;

    //Dodaj osobu u tim
    $mysqli->query("UPDATE korisnici SET ID_kluba = $novi_tim_id  WHERE Email = '$e_mail'");

    //dodavanje slike u Folder
    $target = 'Club_images/' . $club_img_name;
    move_uploaded_file($_FILES['clubimg']['tmp_name'], $target);

    $create_status = true;
  }
}
?>

<body id= "ron_klubovi">
  <div class="container" style="padding-top: 80px;padding-bottom: 40px;">
    <div style="margin-bottom: 20px;" class="row text-center">
      <div class="col-md-1"></div>
      <div class="col-md-5">
        <h1 style="margin-bottom: 10px;">Ronilački klubovi<h1>
          <?php if(!isset($_SESSION['e_mail'])){ ?>
              <h4><a style="color: white;" href="Register.php"><u>Prijavite se</u></a> kako bi ste se pridružili timu</h4>
          <?php }else{ ?>
        <h3 style ="margin-bottom: 30px;">Stvori svoj tim ili postani dio tima!</h3>
      <?php } ?>
      </div>
      <div class="col-md-5">
        <?php
        if(isset($_SESSION['e_mail'])){

          $e_mail = $_SESSION['e_mail'];
          $korisnik_dio_kluba_query = $mysqli->query("SELECT ID_kluba FROM korisnici WHERE Email = '$e_mail'");
          $korisnik_dio_kluba = $korisnik_dio_kluba_query -> fetch_assoc();
          if($korisnik_dio_kluba['ID_kluba'] == NULL){
        ?>
        <div id="newclub_form" class = "container container_form" >
        <form action="Clubs.php" method ="post" class = "form_edit" enctype="multipart/form-data">
              <div class="form-group">
                     <input type="text" name = "clubname" class="form-control"  placeholder="Unesite ime kluba" required>
             </div>
             <div class="form-group">
                 <img src = "Club_images/placeholderclubimg.png" id = "clubpicdisplay" class="club-pic" onclick="promjeni_sliku_kluba();">
                 <input type="file" name = "clubimg" id = "club_img" style = "display: none;" onchange="prikazi_sliku_kluba(this)">
             </div>
             <label> Odaberite sliku kluba </label>
            <button type="submit" name = "create_club_butt" class="btn submit">Kreiraj klub</button>
            <div id = "validcontactmess" class = "<?php
              if(isset($create_status) && $create_status == true){
                  echo "alert alert-primary";
                }
              else echo "";?>"
            role ="alert">
            <?php if(isset($create_status) && $create_status == true){
                echo "Zahtjev za osnivanjem kluba je uspjesno poslan.</br>Odgovor na zahtjev slijedi u sto kracem vremenu!</br><b>Do odgovora na zahtjev nećete moći pristupati drugim klubovima!</b></br>Vaš ARA tim!";
              }
            else echo "";?></div>
        </form>
      </div>
    <?php }
      } ?>
      </div>
      <div class="col-md-1"></div>
    </div>
    <div class="row text-center">
      <div class="col-md-1"></div>
      <div class="col-md-10">
        <?php
          include("DBI.php");  //informacije o bazi podataka
          echo "<table class='table table-dark'>";
          if(!isset($_GET['clubid'])){
            echo "<thead>
                    <tr class='table-info'>
                    <th scope='col'></th>
                    <th scope='col'>Ime kluba</th>
                    <th scope='col'>Broj clanova</th>
                    </tr>
                 </thead>";
           echo "<tbody>";
          $result = $mysqli->query("SELECT * FROM ron_klubovi WHERE Validacija = 'approved'");
          while($row = $result -> fetch_assoc()){
            //Dohvati broj clanova svakog kluba
            $id_kluba = $row["ID_kluba"];
            $br_clanova_query = $mysqli->query("SELECT COUNT(*) AS broj_clanova FROM korisnici WHERE ID_kluba = $id_kluba");
            $br_clanova = $br_clanova_query->fetch_assoc();
              echo "<tr>
                <td>
                  <a href= 'Clubs.php?clubid=" . $row["ID_kluba"] . "'>
                    <img src='Club_images/" . $row["Slika"] . "' class = 'club-pic' alt='Slika profila'>
                    </a>
                </td>";
              echo "<td style='vertical-align: middle;'>
                      <a style='color:white;' href= 'Clubs.php?clubid=" . $row["ID_kluba"] . "'>
                      " . $row["Ime"] . "
                      </a>
                    </td>";
              echo "<td style='vertical-align: middle;'>
                      ".$br_clanova['broj_clanova']."
                    </td>";
              echo "</tr>";
            }
          echo "</tbody>";
          }
          else{
            $klub = $_GET['clubid']; //dohvacanje ID kluba
            $ime_kluba_query = $mysqli->query("SELECT Ime FROM ron_klubovi WHERE ID_kluba = $klub"); //dohvacanje imena kluba na temelju ID
            $ime_kluba = $ime_kluba_query->fetch_assoc();

            if(isset($_SESSION['e_mail'])){ //ako je netko logiran, dohvati njegov klub
                $e_mail = $_SESSION['e_mail'];
                $osoba_query = $mysqli->query("SELECT ID_kluba FROM korisnici WHERE Email = '$e_mail'"); //dohvati ID_kluba trenutno logirane osobe
                $klub_osobe = $osoba_query->fetch_assoc();
            }

            $clanovi_query =  $mysqli->query("SELECT Ime,Prezime,Slika FROM korisnici WHERE ID_kluba = $klub"); //dohvati clanove
            if(isset($_SESSION['e_mail'])){
              //Pronadi ID osobe
              $email = $_SESSION['e_mail'];
              $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$email'");
              $userID = $user_query -> fetch_assoc();
              $ID = $userID['ID_korisnika'];

              $zahtjevzaklubom_query = $mysqli->query("SELECT ID_zahtjeva FROM zahtjevi WHERE ID_kluba = $klub AND ID_podnositelja = $ID AND Status_zahtjeva = 'obrada'");
            }
            //Zaglavlje tablice clanova
            echo "<thead>
                    <tr class='table-info'>
                    <th style='vertical-align: middle;' scope='col'>
                    <div style='display:flex;flex-wrap: wrap;align-items: center;justify-content:center;'>" . $ime_kluba["Ime"];

            if(isset($_SESSION['e_mail'])){
                if($klub_osobe['ID_kluba'] == $klub){
                  echo "<form style='margin-left: 15px;' action = 'Clubs.php' method= 'POST'>
                        <button type='submit' class='btn btnred' name='clubleave' value='".$klub."'>Napusti klub</button>
                        </form>";
                }
                else if($klub_osobe['ID_kluba'] == NULL && $zahtjevzaklubom_query->num_rows == 0){
                  echo "<form action = 'Clubs.php' method= 'POST'>
                      <button type='submit' class='btn btngreen' name='clubidjoin' value='".$klub."'>Pridruzi se</button>
                      </form>";
                }
                else if($klub_osobe['ID_kluba'] == NULL && $zahtjevzaklubom_query->num_rows > 0){
                  $zahtjevzaklubom_arr = $zahtjevzaklubom_query -> fetch_assoc();
                  $id_zahtjeva = $zahtjevzaklubom_arr['ID_zahtjeva'];
                  echo "<form action = 'Clubs.php' method= 'POST'>
                      <button type='submit' class='btn btnoker' name='clubreqdel' value='".$id_zahtjeva."'>Ponisti zahtjev</button>
                      </form>";
                }
            }
            echo "</div></th>
                  <th style='vertical-align: middle;' scope='col' colspan='2'>Popis clanova</th>
                  </tr>
                 </thead>";

            //Ispis clanova kluba
             echo "<tbody>";
            while($row = $clanovi_query -> fetch_assoc()){
              echo "<tr>";
              echo"<td>
                    <img src='profile_images/" . $row["Slika"] . "' class = 'profile-pic' alt='Slika profila'>
                  </td>";
              echo "<td style='vertical-align: middle;'>" . $row["Ime"] . "</td>";
              echo "<td style='vertical-align: middle;'>" . $row["Prezime"] . "</td>";
              echo "</tr>";
            }
            echo "</tbody>";
          }
          echo "</table>";
        ?>
      </div>
      <div class="col-md-1"></div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="Ara.js"></script>
</body>

</html>
