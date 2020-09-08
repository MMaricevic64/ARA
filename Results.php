<?php
session_start();
include("Navbar.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  include("DBI.php");
  if(isset($_POST['savedivechanges'])){
    //Dohvati ID_urona
    $id_urona= $_POST['savedivechanges'];
    $tlak_na_zavrsetku = $_POST['tlakK'];
    $zavrsetak_urona = $_POST['kraj_urona'];

    if(isset($_SESSION['e_mail'])) $e_mail = $_SESSION['e_mail'];
    //Dohvati ID_korisnika
    $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
    $userID = $user_query -> fetch_assoc();
    $ID = $userID['ID_korisnika'];

    //Updataj tablicu korisnickih podataka za uron
    $mysqli->query("UPDATE korisnik_uron SET TlakBoceK = '$tlak_na_zavrsetku',KrajUrona = '$zavrsetak_urona' WHERE ID_korisnika = $ID AND ID_urona = $id_urona");
  }
  else if(isset($_POST['saveecoactionchanges'])){
    //Dohvati ID_eko akcije
    $id_eko_akcije = $_POST['saveecoactionchanges'];
    $zavrsetak_eko_akcije = $_POST['kraj_eko_akcije'];

    if(isset($_SESSION['e_mail'])) $e_mail = $_SESSION['e_mail'];
    //Dohvati ID_korisnika
    $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
    $userID = $user_query -> fetch_assoc();
    $ID = $userID['ID_korisnika'];

    //Updataj tablicu korisnickih podataka za ekoackije
    $mysqli->query("UPDATE korisnik_ekoakcija SET ZavrsetakAkcije = '$zavrsetak_eko_akcije' WHERE ID_korisnika = $ID AND ID_ekoakcije = $id_eko_akcije");
  }

}

?>

<body id= "rezultati">
  <div class="container" style="padding-top: 80px;padding-bottom: 40px;">
    <div style="margin-bottom: 20px;" class="row text-center">
      <div class="col-md-10">
        <?php
          include("DBI.php");
          //Pronadi ID korisnika
          $email = $_SESSION['e_mail'];
          $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$email'");
          $userID = $user_query -> fetch_assoc();
          $ID = $userID['ID_korisnika'];

          //Pronadi njegove urone
          $dive_query = $mysqli->query("SELECT * FROM korisnik_uron  WHERE ID_korisnika = $ID");
          if($dive_query->num_rows != 0){
            echo "<h3 style='text-align:left !important;'>Vaši rezultati urona : </h3>";
            echo "<table class='table table-dark'>";
            echo "<thead>
                    <tr class='table-info'>
                    <th scope='col' style='vertical-align: middle;'>Naziv urona</th>
                    <th scope='col' style='vertical-align: middle;'>Lokalitet</th>
                    <th scope='col' style='vertical-align: middle;'>Dubina (m)</th>
                    <th scope='col' style='vertical-align: middle;'>Volumen boce na početku (l)</th>
                    <th scope='col' style='vertical-align: middle;'>Tlak boce na pocetku (bar)</th>
                    <th scope='col' style='vertical-align: middle;'>Pocetak urona</th>
                    <th scope='col' style='vertical-align: middle;'>Tlak boce na kraju (bar)</th>
                    <th scope='col' style='vertical-align: middle;'>Zavrsetak urona</th>
                    <th scope='col' style='vertical-align: middle;'></th>
                    </tr>
                 </thead>";
           echo "<tbody>";
            while($row = $dive_query -> fetch_assoc()){
              $id_urona = $row['ID_urona'];
              $dive_query_info = $mysqli->query("SELECT uroni.Naziv AS Uron,Dubina,VolumenBoce,TlakBoceP,PocetakUrona,lokaliteti.Naziv FROM uroni JOIN lokaliteti USING (ID_lokaliteta) WHERE ID_urona = $id_urona");
              if($dive_query_info->num_rows != 0){
                    $dive_arr = $dive_query_info->fetch_assoc();
                    $time = strtotime($dive_arr['PocetakUrona']);
                    $time_now = time();
                    if($time > $time_now){
                    echo "<tr>";
                    echo  "<td style='vertical-align: middle;'>";
                    echo  $dive_arr['Uron'];
                    echo  "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    echo  $dive_arr['Naziv'];
                    echo  "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    echo  $dive_arr['Dubina'];
                    echo  "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    echo $dive_arr['VolumenBoce'];
                    echo "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    echo $dive_arr['TlakBoceP'];
                    echo "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    echo $dive_arr['PocetakUrona'];
                    echo "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    if($row['TlakBoceK'] != NULL){
                    echo $row['TlakBoceK'];
                    }
                    else{
                      echo "<form action='Results.php' method ='post'>";
                      echo "<input type='number' id='tlakK' name = 'tlakK'  placeholder='Tlak boce na kraju' required>";
                    }
                    echo "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    if($row['KrajUrona'] != NULL){
                      echo $row['KrajUrona'];
                    }
                    else{
                      echo "<input type='datetime-local' id='kraj_urona' name = 'kraj_urona' required>";
                    }
                    echo "</td>";
                    echo "<td style='vertical-align: middle;'>";
                    if($row['KrajUrona'] != NULL){
                      echo "";
                    }
                    else{
                      echo "<button style='margin-bottom: 15px;'' id='savechanges".$id_urona."' type='submit' name = 'savedivechanges' value='".$id_urona."'class='btn submit'>Spremi</button>";
                      echo "</form>";
                    }
                    echo "</td>";
                    echo "</tr>";
                  }
              }
            }
            echo "</tbody>";
            echo "</table>";
          }
                //Pronadi njegove eko_akcije
                $ecoaction_query = $mysqli->query("SELECT * FROM korisnik_ekoakcija  WHERE ID_korisnika = $ID");

                if($ecoaction_query->num_rows != 0){
                  echo "<h3 style='text-align:left !important;'>Vaši rezultati eko akcija : </h3>";
                  echo "<table class='table table-dark'>";
                  echo "<thead>
                          <tr class='table-info'>
                          <th scope='col' style='vertical-align: middle;'>Naziv eko akcije</th>
                          <th scope='col' style='vertical-align: middle;'>Lokalitet</th>
                          <th scope='col' style='vertical-align: middle;'>Pocetak eko akcije</th>
                          <th scope='col' style='vertical-align: middle;'>Zavrsetak eko akcije</th>
                          <th scope='col' style='vertical-align: middle;'></th>
                          </tr>
                       </thead>";
                 echo "<tbody>";
                  while($row = $ecoaction_query -> fetch_assoc()){
                    $id_ekoakcije = $row['ID_ekoakcije'];
                    $ecoaction_query_info = $mysqli->query("SELECT eko_akcije.Naziv AS EkoAkcija,PocetakAkcije,lokaliteti.Naziv FROM eko_akcije JOIN lokaliteti USING (ID_lokaliteta) WHERE ID_eko_akcije = $id_ekoakcije");
                    if($ecoaction_query_info->num_rows != 0){
                          $ecoaction_arr = $ecoaction_query_info->fetch_assoc();
                          $time = strtotime($ecoaction_arr['PocetakAkcije']);
                          $time_now = time();
                          if($time > $time_now){
                          echo "<tr>";
                          echo  "<td style='vertical-align: middle;'>";
                          echo  $ecoaction_arr['EkoAkcija'];
                          echo  "</td>";
                          echo "<td style='vertical-align: middle;'>";
                          echo  $ecoaction_arr['Naziv'];
                          echo  "</td>";
                          echo "<td style='vertical-align: middle;'>";
                          echo $ecoaction_arr['PocetakAkcije'];
                          echo "</td>";
                          echo "<td style='vertical-align: middle;'>";
                          if($row['ZavrsetakAkcije'] != NULL){
                          echo $row['ZavrsetakAkcije'];
                          }
                          else{
                            echo "<form action='Results.php' method ='post'>";
                            echo "<input type='datetime-local' id='kraj_eko_akcije' name = 'kraj_eko_akcije' required>";
                          }
                          echo "</td>";
                          echo "<td style='vertical-align: middle;'>";
                          if($row['ZavrsetakAkcije'] != NULL){
                            echo "";
                          }
                          else{
                            echo "<button style='margin-bottom: 15px;'' id='savechanges".$id_ekoakcije."' type='submit' name = 'saveecoactionchanges' value='".$id_ekoakcije."'class='btn submit'>Spremi</button>";
                            echo "</form>";
                          }
                          echo "</td>";
                          echo "</tr>";
                        }
                    }
                  }
                  echo "</tbody>";
                  echo "</table>";
                }
          ?>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="Ara.js"></script>
</body>

</html>
