<?php
session_start();
if(isset($_POST['naziv']) && isset($_POST['pocetakekoakcije']) && isset($_POST['idlokaliteta'])){

  include("DBI.php");
  //Dohvati podatke urona
  $naziv = $_POST['naziv'];
  $pocetakekoakcije = $_POST['pocetakekoakcije'];
  $idlokaliteta = $_POST['idlokaliteta'];

  //Podaci osobe koja je kreirala ekoakciju
  if(isset($_SESSION['e_mail'])) $e_mail = $_SESSION['e_mail'];
  if(isset($_SESSION['ime'])) $ime = $_SESSION['ime'];
  if(isset($_SESSION['prezime'])) $prezime = $_SESSION['prezime'];

  //Dohvati ID_korisnika
  $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
  $userID = $user_query -> fetch_assoc();
  $ID = $userID['ID_korisnika'];

  //Spremi ekoakciju u bazu podataka
  $mysqli->query("INSERT INTO `eko_akcije` (`Naziv`, `PocetakAkcije`, `ID_lokaliteta`, `ID_korisnika`)
  VALUES ('$naziv', '$pocetakekoakcije', '$idlokaliteta', '$ID' );");

  //Vrati ID zadnje kreirane eko akcije
  $ecoaction_query = $mysqli->query("SELECT ID_eko_akcije FROM eko_akcije WHERE ID_korisnika = $ID ORDER BY ID_eko_akcije DESC LIMIT 1");
  $ecoaction_ID_arr = $ecoaction_query -> fetch_assoc();
  $ecoaction_ID = $ecoaction_ID_arr['ID_eko_akcije'];

  //Kreiraj potvrdeni zahtjev
  $opis_eko_akcije = "Pridruzivanje eko akciji";
  $mysqli->query("INSERT INTO `zahtjevi` (`Opis`, `ID_podnositelja`, `ID_eko_akcije`,`Status_zahtjeva`)
  VALUES ('$opis_eko_akcije', '$ID', '$ecoaction_ID','accepted');");

  //Vrati ID zadnjeg potvrdenog zahtjeva
  $request_query = $mysqli->query("SELECT ID_zahtjeva,ID_eko_akcije,Status_zahtjeva,ID_podnositelja FROM zahtjevi WHERE ID_podnositelja = $ID ORDER BY ID_zahtjeva DESC LIMIT 1");

  //Dodaj odmah osobu u dogadaj
  $mysqli->query("INSERT INTO `korisnik_ekoakcija` (`ID_korisnika`,`ID_ekoakcije`)
  VALUES ('$ID', '$ecoaction_ID');");

$html = "<div class='comment_box'>
  <div style='display:flex;flex-wrap: wrap;align-items: center;justify-content:center;text-align:center;'>
      <p class='comment_text' style='font-size:16px;'>".$naziv."</p>
      <button style='margin:10px 10px 10px 10px;font-size: 12px;' id='moreaboutevent' class='btn submit' onclick='toggleElement(eventdetails".$ecoaction_ID.")'>Detaljnije</button>
  </div>
  <div id='eventdetails".$ecoaction_ID."' style='display:none;text-align:center;margin-bottom: 10px;'>
      <p class='comment_text'>Osnivač eko akcije: ". $ime . " " . $prezime."</p>
      <p class='comment_text'>Email kontakt: ".$e_mail."</p>
      <p class='comment_text'>Pocetak eko akcije: ".$pocetakekoakcije."</p>
  </div>
</div>";

$request_arr = array();
while($row = $request_query->fetch_assoc()){
  $request_arr[] = $row;
}
$request_arr = array_map('array_values', $request_arr);
$arr = array('html'=>$html,'idekoakcije'=>$ecoaction_ID,'zahtjev'=>$request_arr);
echo json_encode($arr);

exit;
}
?>
