<?php
session_start();
include("Navbar.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){

  include("DBI.php");  //informacije o bazi podataka

  if(isset($_POST["clubleave"])){   //Izlaz iz kluba
    $e_mail = $_SESSION['e_mail'];
    $izadi_query = $mysqli->query("UPDATE korisnici SET ID_kluba = NULL WHERE Email = '$e_mail'");
    //header("location: Clubs.php");
  }

  if(isset($_POST["clubidjoin"])){   //Pridruzivanje klubu
    $klub = $_POST['clubidjoin'];
    $e_mail = $_SESSION['e_mail'];
    $pridruzi_se_query = $mysqli->query("UPDATE korisnici SET ID_kluba = $klub WHERE Email = '$e_mail'");
    //header("location: Clubs.php");
  }
}
?>

<body id= "ron_klubovi">
  <div class="container" style="padding-top: 60px;padding-bottom: 40px;">
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
                    </tr>
                 </thead>";
           echo "<tbody>";
          $result = $mysqli->query("SELECT * FROM ron_klubovi");
          while($row = $result -> fetch_assoc()){
              echo "<tr>
                <td>
                  <a href= 'Clubs.php?clubid=" . $row["ID_kluba"] . "'>
                    <img src='Club_images/" . $row["Slika"] . "' class = 'profile-pic' alt='Slika profila'>
                    </a>
                </td>";
              echo "<td style='vertical-align: middle;'>
                      <a style='color:white;' href= 'Clubs.php?clubid=" . $row["ID_kluba"] . "'>
                      " . $row["Ime"] . "
                      </a>
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

            //Zaglavlje tablice clanova
            echo "<thead>
                    <tr class='table-info'>
                    <th scope='col'>" . $ime_kluba["Ime"];
            if(isset($_SESSION['e_mail'])){            //$klub_osobe['ID_kluba'] == NULL
                if($klub_osobe['ID_kluba'] == $klub){
                  echo "<form action = 'Clubs.php' method= 'POST'>
                        <button type='submit' class='btn btnleaveclub' name='clubleave'>Napusti klub</button>
                        </form>";
                }
                else if($klub_osobe['ID_kluba'] == NULL){
                  echo "<form action = 'Clubs.php' method= 'POST'>
                      <button type='submit' class='btn btnjoinclub' name='clubidjoin' value='".$klub."'>Pridruzi se</button>
                      </form>";
                }
            }
            echo "</th>
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
