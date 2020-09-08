<?php
session_start();
if(isset($_POST['naziv']) && isset($_POST['vrsta']) && isset($_POST['dubina'])
    && isset($_POST['volumenboce']) && isset($_POST['tlakboce']) && isset($_POST['pocetakurona']) && isset($_POST['idlokaliteta'])){

  include("DBI.php");
  //Dohvati podatke urona
  $naziv = $_POST['naziv'];
  $vrsta = $_POST['vrsta'];
  $dubina = $_POST['dubina'];
  $volumenboce = $_POST['volumenboce'];
  $tlakboce = $_POST['tlakboce'];
  $pocetakurona = $_POST['pocetakurona'];
  $idlokaliteta = $_POST['idlokaliteta'];

  //Podaci osobe koja je kreirala dogadaj
  if(isset($_SESSION['e_mail'])) $e_mail = $_SESSION['e_mail'];
  if(isset($_SESSION['ime'])) $ime = $_SESSION['ime'];
  if(isset($_SESSION['prezime'])) $prezime = $_SESSION['prezime'];

  //Dohvati ID_korisnika
  $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
  $userID = $user_query -> fetch_assoc();
  $ID = $userID['ID_korisnika'];

  //Spremi uron u bazu podataka
  $mysqli->query("INSERT INTO `uroni` (`Naziv`, `Vrsta`, `Dubina`, `VolumenBoce`, `TlakBoceP`, `PocetakUrona`, `ID_lokaliteta`, `ID_korisnika`)
  VALUES ('$naziv', '$vrsta', '$dubina','$volumenboce', '$tlakboce', '$pocetakurona', '$idlokaliteta', '$ID' );");

  //Vrati ID zadnjeg kreiranog urona
  $dive_query = $mysqli->query("SELECT ID_urona FROM uroni WHERE ID_korisnika = $ID ORDER BY ID_urona DESC LIMIT 1");
  $dive_ID_arr = $dive_query -> fetch_assoc();
  $dive_ID = $dive_ID_arr['ID_urona'];

  //Kreiraj potvrdeni zahtjev
  $opis_urona = "Pridruzivanje uronu";
  $accepted = "accepted";
  $mysqli->query("INSERT INTO `zahtjevi` (`Opis`, `ID_podnositelja`, `ID_urona`,`Status_zahtjeva`)
  VALUES ('$opis_urona', '$ID', '$dive_ID', '$accepted');");

  //Vrati ID zadnjeg potvrdenog zahtjeva
  $request_query = $mysqli->query("SELECT ID_zahtjeva,ID_urona,Status_zahtjeva,ID_podnositelja FROM zahtjevi WHERE ID_podnositelja = $ID ORDER BY ID_zahtjeva DESC LIMIT 1");

  //Dodaj odmah osobu u dogadaj
  $mysqli->query("INSERT INTO `korisnik_uron` (`ID_korisnika`,`ID_urona`)
  VALUES ('$ID', '$dive_ID');");

  if($_POST['emailovi'] != ""){
    $mailovi = explode(",", $_POST['emailovi']);
    $opis = "Poziv na uron";
    for($i = 0; $i < sizeof($mailovi); $i++){
      $e_mail = $mailovi[$i];
      //Dohvati ID_korisnika
      $users_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
      if($users_query->num_rows != 0){
      $usersID = $users_query -> fetch_assoc();
      $ID_korisnika = $usersID['ID_korisnika'];
      //Posalji zahtjeve
      $mysqli->query("INSERT INTO `zahtjevi` (`Opis`, `ID_podnositelja`, `ID_urona`)
      VALUES ('$opis', '$ID_korisnika', '$dive_ID');");
      }
    }
  }

?>

<?php if($vrsta == "Javni"){
  $html = "<div class='comment_box'>
    <div style='display:flex;flex-wrap: wrap;align-items: center;justify-content:center;text-align:center;'>
        <p class='comment_text' style='font-size:16px;'>".$naziv."</p>
        <button style='margin:10px 10px 10px 10px;font-size: 12px;' id='moreaboutevent' class='btn submit' onclick='toggleElement(eventdetails".$dive_ID.")'>Detaljnije</button>
    </div>
    <div id='eventdetails".$dive_ID."' style='display:none;text-align:center;margin-bottom: 10px;'>
        <p class='comment_text'>Osnivač događaja: ".$ime. " " . $prezime."</p>
        <p class='comment_text'>Email kontakt: ".$e_mail."</p>
        <p class='comment_text'>Dubina: ".$dubina. " m </p>
        <p class='comment_text'>Volumen boce: ".$volumenboce . " l </p>
        <p class='comment_text'>Tlak boce: ".$tlakboce . " bara </p>
        <p class='comment_text'>Pocetak urona: ".$pocetakurona."</p>
    </div>
  </div>";
  }
$request_arr = array();
while($row = $request_query->fetch_assoc()){
  $request_arr[] = $row;
}
$request_arr = array_map('array_values', $request_arr);
$arr = array('html'=>$html,'idurona'=>$dive_ID,'zahtjev'=>$request_arr);
echo json_encode($arr);

  exit;
}
?>
