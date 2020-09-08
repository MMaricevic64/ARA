<?php
session_start();
include("Navbar.php");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  //ADMINOVO PRIHVACANJE/ODBIJANJE KREIRANJA KLUBA
  if(isset($_POST['approve'])){
    $id_kluba = $_POST['approve'];
    $mysqli->query("UPDATE ron_klubovi SET Validacija = 'approved' WHERE ID_kluba = $id_kluba");
  }
  else if(isset($_POST['decline'])){
    $id_kluba = $_POST['decline'];
    //treba uklonit korisnika i maknut klub
    $mysqli->query("UPDATE ron_klubovi SET Validacija = 'declined' WHERE ID_kluba = $id_kluba");
  }
  //VODITELJ KLUBA PRIHVACA/ODBIJA KORISNIKA
  else if(isset($_POST['approvejoin'])){
    $id_kluba = $_POST['approvejoin'];
    $id_korisnika = $_POST['idkorisnikaacc'];
    $id_zahtjeva = $_POST['idzahtjevaacc'];
    //Updataj zahtjeve
    $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'accepted' WHERE ID_zahtjeva = $id_zahtjeva");
    //Dodaj korisnika u klub
    $mysqli->query("UPDATE korisnici SET ID_kluba = $id_kluba WHERE ID_korisnika = $id_korisnika");
  }
  else if(isset($_POST['declinejoin'])){
    $id_kluba = $_POST['declinejoin'];
    $id_korisnika = $_POST['idkorisnikadel'];
    $id_zahtjeva = $_POST['idzahtjevadel'];
    //Updataj zahtjeve
    $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'declined' WHERE ID_zahtjeva = $id_zahtjeva");
  }
  //OSNIVAC URONA PRIHAVACA/ODBIJA KORISNIKA U PRIDRUZIVANJU
  else if(isset($_POST['approvejoindive'])){
    $id_korisnika = $_POST['idkorisnikaaccdive'];
    $id_zahtjeva = $_POST['idzahtjevaaccdive'];
    $id_urona = $_POST['approvejoindive'];
    //Updataj zahtjeve
    $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'accepted' WHERE ID_zahtjeva = $id_zahtjeva");
    //Dodaj osobu u dogadaj
    $mysqli->query("INSERT INTO `korisnik_uron` (`ID_korisnika`, `ID_urona`)
    VALUES ('$id_korisnika', '$id_urona');");
  }
  else if(isset($_POST['declinejoindive'])){
    $id_zahtjeva = $_POST['idzahtjevadeldive'];
    //Updataj zahtjeve
    $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'declined' WHERE ID_zahtjeva = $id_zahtjeva");
  }
  //OSNIVAC EKO AKCIJA PRIHVACA/ODBIJA KORISNIKA U PRIDRUZIVANJU
  else if(isset($_POST['approvejoineco'])){
    $id_korisnika = $_POST['idkorisnikaacceco'];
    $id_zahtjeva = $_POST['idzahtjevaacceco'];
    $id_eko_akcije = $_POST['approvejoineco'];
    //Updataj zahtjeve
      $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'accepted' WHERE ID_zahtjeva = $id_zahtjeva");
    //Dodaj osobu u događaj
    $mysqli->query("INSERT INTO `korisnik_ekoakcija` (`ID_korisnika`, `ID_ekoakcije`)
    VALUES ('$id_korisnika', '$id_eko_akcije');");
  }
  else if(isset($_POST['declinejoineco'])){
    $id_zahtjeva = $_POST['idzahtjevadeleco'];
    //Updataj zahtjeve
    $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'declined' WHERE ID_zahtjeva = $id_zahtjeva");
  }
  //ODGOVOR NA POZIV PO PITANJU PRIVATNOG URONA
  else if(isset($_POST['approvejoinprivdive'])){
    $id_korisnika = $_POST['idkorisnikaaccpriv'];
    $id_urona = $_POST['approvejoinprivdive'];
    $id_zahtjeva = $_POST['idzahtjevaaccpriv'];
    //Updataj zahtjeve
    $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'accepted' WHERE ID_zahtjeva = $id_zahtjeva");
    //Dodaj osobu u dogadaj
    $mysqli->query("INSERT INTO `korisnik_uron` (`ID_korisnika`, `ID_urona`)
    VALUES ('$id_korisnika', '$id_urona');");
  }
  else if(isset($_POST['declinejoinprivdive'])){
    $id_zahtjeva = $_POST['declinejoinprivdive'];
    //Updataj zahtjeve
    $mysqli->query("UPDATE zahtjevi SET Status_zahtjeva = 'declined' WHERE ID_zahtjeva = $id_zahtjeva");
  }
}
?>

<body id= "postavke">
  <div class="container" style="padding-top: 80px;padding-bottom: 40px;">
    <div style="margin-bottom: 20px;" class="row text-center">
      <div class="col-md-10">
      <?php
        //Pronadi ID korisnika
        $email = $_SESSION['e_mail'];
        $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$email'");
        $userID = $user_query -> fetch_assoc();
        $ID = $userID['ID_korisnika'];

        $voditelj_kluba_query = $mysqli->query("SELECT ID_kluba,Ime FROM ron_klubovi WHERE ID_voditelja = $ID");
        $uroni_query = $mysqli->query("SELECT ID_urona,Naziv,Dubina FROM uroni WHERE ID_korisnika = $ID AND Vrsta = 'Javni'");
        $uroniprivatni_query = $mysqli->query("SELECT ID_urona,ID_zahtjeva FROM zahtjevi WHERE ID_podnositelja = $ID AND Status_zahtjeva = 'obrada' AND Opis = 'Poziv na uron'");
        $ecoactions_query = $mysqli->query("SELECT ID_eko_akcije,Naziv FROM eko_akcije WHERE ID_korisnika = $ID");
        $obavijestidive_query = $mysqli->query("SELECT uroni.Naziv AS Uron,lokaliteti.Naziv,Dubina,VolumenBoce,TlakBoceP,PocetakUrona FROM uroni JOIN korisnik_uron USING (ID_urona)JOIN lokaliteti USING (ID_lokaliteta) WHERE korisnik_uron.ID_korisnika = $ID");
        $obavijestieco_query = $mysqli->query("SELECT eko_akcije.Naziv AS EkoAkcija,lokaliteti.Naziv,PocetakAkcije FROM eko_akcije JOIN korisnik_ekoakcija ON eko_akcije.ID_eko_akcije = korisnik_ekoakcija.ID_ekoakcije JOIN lokaliteti USING (ID_lokaliteta) WHERE korisnik_ekoakcija.ID_korisnika = $ID");

        if(isset($_SESSION['admin'])){
        echo "<table class='table table-dark'>";
        echo "<thead>
                <tr class='table-info'>
                <th scope='col' colspan='2'>Podnešeni zahtjevi :</th>
                <th scope='col' ></th>
                </tr>
             </thead>";
       echo "<tbody>";
      $result = $mysqli->query("SELECT * FROM ron_klubovi WHERE Validacija = 'unknown'");
      while($row = $result -> fetch_assoc()){
        //Dohvati broj clanova svakog kluba
        $id_kluba = $row["ID_kluba"];
        $id_voditelja = $row["ID_voditelja"];
        $osnivac_query = $mysqli->query("SELECT Ime,Prezime FROM korisnici WHERE ID_korisnika = '$id_voditelja'");
        $osnivac = $osnivac_query->fetch_assoc();
          echo "<tr>";
          echo  "<td>";
          echo  "Korisnik " .$osnivac['Ime']. " " .$osnivac['Prezime']. " želi osnovati klub " .$row['Ime']. " .";
          echo  "</td>";
          echo "<td style='vertical-align: middle;'>";
          echo "<form action = 'ProfileSettings.php' method= 'POST'>
                <button type='submit' class='btn btngreen' name='approve' value='".$id_kluba."'>Odobri zahtjev</button>
                </form>";
          echo  "</td>";
          echo "<td style='vertical-align: middle;'>";
          echo "<form style='margin-left: 15px;' action = 'ProfileSettings.php' method= 'POST'>
                <button type='submit' class='btn btnred' name='decline' value='".$id_kluba."'>Odbij zahtjev</button>
                </form>";
          echo "</td>";
          echo "</tr>";
        }
      echo "</tbody>";
      echo "</table>";
      }

      if($voditelj_kluba_query->num_rows != 0){ //Korisnik je voditelj kluba
        $klub_arr = $voditelj_kluba_query->fetch_assoc();
        $idkluba = $klub_arr['ID_kluba'];
        $imekluba = $klub_arr['Ime'];

        //Pronadi sve zahtjeve za taj klub
        $zahtjevi_za_klub_query = $mysqli->query("SELECT ID_zahtjeva,Opis,Ime,Prezime,ID_korisnika FROM zahtjevi JOIN korisnici ON zahtjevi.ID_podnositelja = korisnici.ID_korisnika WHERE zahtjevi.ID_kluba = $idkluba AND zahtjevi.Status_zahtjeva = 'obrada'");
        if($zahtjevi_za_klub_query->num_rows != 0){
          echo "<table class='table table-dark'>";
          echo "<thead>
                  <tr class='table-info'>
                  <th scope='col' colspan='2'>Podnešeni zahtjevi za klub ". $imekluba . "</th>
                  <th scope='col' ></th>
                  </tr>
               </thead>";
         echo "<tbody>";
          while($row = $zahtjevi_za_klub_query->fetch_assoc()){
            echo "<tr>";
            echo  "<td>";
            echo  "Korisnik " .$row['Ime']. " " .$row['Prezime']. " želi se priključiti klubu " .$imekluba. " .";
            echo  "</td>";
            echo "<td style='vertical-align: middle;'>";
            echo "<form action = 'ProfileSettings.php' method= 'POST'>
                  <input type='hidden'  name = 'idkorisnikaacc' class='form-control' value='".$row['ID_korisnika']."'>
                  <input type='hidden'  name = 'idzahtjevaacc' class='form-control' value='".$row['ID_zahtjeva']."'>
                  <button type='submit' class='btn btngreen' name='approvejoin' value='".$idkluba."'>Odobri zahtjev</button>
                  </form>";
            echo  "</td>";
            echo "<td style='vertical-align: middle;'>";
            echo "<form style='margin-left: 15px;' action = 'ProfileSettings.php' method= 'POST'>
                  <input type='hidden'  name = 'idkorisnikadel' class='form-control' value='".$row['ID_korisnika']."'>
                  <input type='hidden'  name = 'idzahtjevadel' class='form-control' value='".$row['ID_zahtjeva']."'>
                  <button type='submit' class='btn btnred' name='declinejoin' value='".$idkluba."'>Odbij zahtjev</button>
                  </form>";
            echo "</td>";
            echo "</tr>";
          }
          echo "</tbody>";
          echo "</table>";
        }
      }

      if($uroni_query->num_rows != 0){ //Korisnik je kreator urona
          while($row = $uroni_query->fetch_assoc()){
            $id_urona = $row['ID_urona'];
            $zahtjevi_za_uron_query = $mysqli->query("SELECT ID_zahtjeva,Ime,Prezime,Licenca,korisnici.ID_korisnika FROM zahtjevi
              JOIN uroni ON zahtjevi.ID_urona = uroni.ID_urona JOIN korisnici ON zahtjevi.ID_podnositelja = korisnici.ID_korisnika
              WHERE zahtjevi.ID_urona = $id_urona AND zahtjevi.Status_zahtjeva = 'obrada'");
              if($zahtjevi_za_uron_query->num_rows != 0){
                $flag = true;
                if($flag == true){
                echo "<table class='table table-dark'>";
                echo "<thead>
                        <tr class='table-info'>
                        <th scope='col' colspan='2'>Podnešeni zahtjevi za događaje </th>
                        <th scope='col' ></th>
                        </tr>
                     </thead>";
                     $flag == false;
                   }
               echo "<tbody>";
              while($row1 = $zahtjevi_za_uron_query->fetch_assoc()){
            echo "<tr>";
            echo  "<td>";
            echo  "Korisnik " .$row1['Ime']. " " .$row1['Prezime']. " želi se priključiti uronu " .$row['Naziv']. " .";
                  if($row1['Licenca'] == 'R1' && $row['Dubina'] > 20){
            echo  "<p style='color:red;'>*Upozorenje! Osoba nema dovoljno iskustva. Licenca R1*</p>";
                  }
                  else if($row1['Licenca'] == 'R2' && $row['Dubina'] > 30){
            echo  "<p style='color:red;'>*Upozorenje! Osoba nema dovoljno iskustva. Licenca R2*</p>";
                  }
            echo  "</td>";
            echo "<td style='vertical-align: middle;'>";
            echo "<form action = 'ProfileSettings.php' method= 'POST'>
                  <input type='hidden'  name = 'idkorisnikaaccdive' class='form-control' value='".$row1['ID_korisnika']."'>
                  <input type='hidden'  name = 'idzahtjevaaccdive' class='form-control' value='".$row1['ID_zahtjeva']."'>
                  <button type='submit' class='btn btngreen' name='approvejoindive' value='".$id_urona."'>Odobri zahtjev</button>
                  </form>";
            echo  "</td>";
            echo "<td style='vertical-align: middle;'>";
            echo "<form style='margin-left: 15px;' action = 'ProfileSettings.php' method= 'POST'>
                  <input type='hidden'  name = 'idkorisnikadeldive' class='form-control' value='".$row1['ID_korisnika']."'>
                  <input type='hidden'  name = 'idzahtjevadeldive' class='form-control' value='".$row1['ID_zahtjeva']."'>
                  <button type='submit' class='btn btnred' name='declinejoindive' value='".$id_urona."'>Odbij zahtjev</button>
                  </form>";
            echo "</td>";
            echo "</tr>";
          }
          }
          }
          echo "</tbody>";
          echo "</table>";
          }
          //Korisnik je kreator eko akcija
          if($ecoactions_query->num_rows != 0){
          while($row = $ecoactions_query->fetch_assoc()){
            $id_eko_akcije = $row['ID_eko_akcije'];
            $zahtjevi_za_ekoakciju_query = $mysqli->query("SELECT ID_zahtjeva,Ime,Prezime,korisnici.ID_korisnika FROM zahtjevi
              JOIN eko_akcije ON zahtjevi.ID_eko_akcije = eko_akcije.ID_eko_akcije JOIN korisnici ON zahtjevi.ID_podnositelja = korisnici.ID_korisnika
              WHERE zahtjevi.ID_eko_akcije = $id_eko_akcije AND zahtjevi.Status_zahtjeva = 'obrada'");
              if($zahtjevi_za_ekoakciju_query->num_rows != 0){
                $flag1 = true;
                if($flag1 == true){
                echo "<table class='table table-dark'>";
                echo "<thead>
                        <tr class='table-info'>
                        <th scope='col' colspan='2'>Podnešeni zahtjevi za događaje </th>
                        <th scope='col' ></th>
                        </tr>
                     </thead>";
                     $flag1 == false;
                   }
              while($row1 = $zahtjevi_za_ekoakciju_query->fetch_assoc()){
            echo "<tr>";
            echo  "<td>";
            echo  "Korisnik " .$row1['Ime']. " " .$row1['Prezime']. " želi se priključiti eko akciji " .$row['Naziv']. " .";
            echo  "</td>";
            echo "<td style='vertical-align: middle;'>";
            echo "<form action = 'ProfileSettings.php' method= 'POST'>
                  <input type='hidden'  name = 'idkorisnikaacceco' class='form-control' value='".$row1['ID_korisnika']."'>
                  <input type='hidden'  name = 'idzahtjevaacceco' class='form-control' value='".$row1['ID_zahtjeva']."'>
                  <button type='submit' class='btn btngreen' name='approvejoineco' value='".$id_eko_akcije."'>Odobri zahtjev</button>
                  </form>";
            echo  "</td>";
            echo "<td style='vertical-align: middle;'>";
            echo "<form style='margin-left: 15px;' action = 'ProfileSettings.php' method= 'POST'>
                  <input type='hidden'  name = 'idzahtjevadeleco' class='form-control' value='".$row1['ID_zahtjeva']."'>
                  <button type='submit' class='btn btnred' name='declinejoineco' value='".$id_eko_akcije."'>Odbij zahtjev</button>
                  </form>";
            echo "</td>";
            echo "</tr>";
          }
          }
          }
          echo "</tbody>";
          echo "</table>";
          }

          //Korisnik je pozvan na privatne urone
          if($uroniprivatni_query->num_rows != 0){
            echo "<table class='table table-dark'>";
            echo "<thead>
                    <tr class='table-info'>
                    <th scope='col' colspan='2'>Podnešeni zahtjevi za događaje </th>
                    <th scope='col' ></th>
                    </tr>
                 </thead>";
           echo "<tbody>";
            while($row = $uroniprivatni_query->fetch_assoc()){
              $id_urona = $row['ID_urona'];
              $uron_query = $mysqli->query("SELECT Naziv,Ime,Prezime FROM uroni JOIN korisnici USING (ID_korisnika) WHERE ID_urona = $id_urona");
              $uron_arr = $uron_query->fetch_assoc();
              echo "<tr>";
              echo  "<td>";
              echo  "Korisnik " .$uron_arr['Ime']. " " .$uron_arr['Prezime']. " poziva vas da se prikljucite uronu " .$uron_arr['Naziv']. " .";
              echo  "</td>";
              echo "<td style='vertical-align: middle;'>";
              echo "<form action = 'ProfileSettings.php' method= 'POST'>
                    <input type='hidden'  name = 'idkorisnikaaccpriv' class='form-control' value='".$ID."'>
                    <input type='hidden'  name = 'idzahtjevaaccpriv' class='form-control' value='".$row['ID_zahtjeva']."'>
                    <button type='submit' class='btn btngreen' name='approvejoinprivdive' value='".$row['ID_urona']."'>Prihvati</button>
                    </form>";
              echo  "</td>";
              echo "<td style='vertical-align: middle;'>";
              echo "<form style='margin-left: 15px;' action = 'ProfileSettings.php' method= 'POST'>
                    <button type='submit' class='btn btnred' name='declinejoinprivdive' value='".$row['ID_zahtjeva']."'>Odbij</button>
                    </form>";
              echo "</td>";
              echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
          }

          //Ispis urona koji su trenutno u tijeku tog korisnika
          if($obavijestidive_query->num_rows != 0){
            echo "<h4 style='color:white;text-align:left;margin-left:30px;'>Obavijesti urona:</h4>";
            echo "<table class='table table-dark'>";
            echo "<thead>
                    <tr class='table-info'>
                    <th scope='col'>Naziv urona</th>
                    <th scope='col' >Lokalitet</th>
                    <th scope='col' >Dubina (m)</th>
                    <th scope='col' >Volumen boce (l)</th>
                    <th scope='col' >Tlak boce (bar)</th>
                    <th scope='col' >Pocetak urona</th>
                    </tr>
                 </thead>";
           echo "<tbody>";
           while($row = $obavijestidive_query->fetch_assoc()){
             $time = strtotime($row['PocetakUrona']);
             $time_now = time();
             if($time > $time_now){
             echo "<tr>";
             echo  "<td>";
             echo  $row['Uron'];
             echo  "</td>";
             echo "<td style='vertical-align: middle;'>";
             echo  $row['Naziv'];
             echo  "</td>";
             echo "<td style='vertical-align: middle;'>";
             echo $row['Dubina'];
             echo "</td>";
             echo "<td style='vertical-align: middle;'>";
             echo $row['VolumenBoce'];
             echo "</td>";
             echo "<td style='vertical-align: middle;'>";
             echo $row['TlakBoceP'];
             echo "</td>";
             echo "<td style='vertical-align: middle;'>";
             echo $row['PocetakUrona'];
             echo "</td>";
             echo "</tr>";
           }
          }
          echo "</tbody>";
          echo "</table>";
          }

          //Ispis ekoackija koji su trenutno u tijeku tog korisnika
          if($obavijestieco_query->num_rows != 0){
            echo "<h4 style='color:white;text-align:left;margin-left:30px;'>Obavijesti eko akcija:</h4>";
            echo "<table class='table table-dark'>";
            echo "<thead>
                    <tr class='table-info'>
                    <th scope='col'>Naziv ekoakcije</th>
                    <th scope='col' >Lokalitet</th>
                    <th scope='col' >Pocetak ekoakcije</th>
                    </tr>
                 </thead>";
           echo "<tbody>";
           while($row = $obavijestieco_query->fetch_assoc()){
             $time = strtotime($row['PocetakAkcije']);
             $time_now = time();
             if($time > $time_now){
             echo "<tr>";
             echo  "<td>";
             echo  $row['EkoAkcija'];
             echo  "</td>";
             echo "<td style='vertical-align: middle;'>";
             echo  $row['Naziv'];
             echo  "</td>";
             echo "<td style='vertical-align: middle;'>";
             echo $row['PocetakAkcije'];
             echo "</td>";
             echo "</tr>";
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
