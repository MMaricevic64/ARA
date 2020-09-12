<?php
session_start();
include("Navbar.php");

function get_all_data($type_of_data){ //Funkcija za dohvacanje lokacija
  include("DBI.php");

  if($type_of_data == "lokaliteti"){
    $data_query = $mysqli->query("SELECT * FROM $type_of_data");
  }
  else if($type_of_data == "eko_akcije"){
    $data_query = $mysqli->query("SELECT ID_eko_akcije,Naziv,PocetakAkcije,ID_korisnika,ID_lokaliteta,Ime,Prezime,Email FROM $type_of_data JOIN korisnici USING (ID_korisnika)");
  }
  else if($type_of_data == "zahtjevi"){
    $email = $_SESSION['e_mail'];
    $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$email'");
    $userID = $user_query -> fetch_assoc();
    $ID = $userID['ID_korisnika'];

    $data_query = $mysqli->query("SELECT ID_zahtjeva,ID_eko_akcije,Status_zahtjeva,ID_podnositelja FROM $type_of_data WHERE ID_podnositelja = $ID AND ID_eko_akcije IS NOT NULL");
  }

  $arr = array();

  if($data_query->num_rows != 0){
    while($row = $data_query->fetch_assoc()){
      $arr[] = $row;
    }
  }
  $arr = array_map('array_values', $arr);
  echo json_encode($arr);
}
?>

<body id= "ekoakcije" onload="initMap();">
  <div style="padding: 60px 25px 40px 25px;">
    <div class="row">
      <div class="col-md-6">
        <h1 style ="margin-bottom: 15px;text-align: center;">*Eko akcije*</h1>
        <h4 style ="margin-bottom: 40px;text-align: center;">Mapa lokaliteta</h4>
        <div  id="map" ></div>
      </div>
      <div class="col-md-6" style="text-align:center;">
        <h4>1.) Odaberite jedan od ponuđenih lokaliteta sa mape.</h4>
        <div id="event_form" style="margin-top: 30px; display:none;" class = "container container_form" >
          <h4 style="margin-top: 15px;margin-bottom:15px;">2.) Ispunite obrazac kako bi kreirali eko akciju.</h4>
          <div class="row text-center">
          <div class="col-md-2"></div>
          <div class="col-md-8">
          <div style="margin-top:15px;margin-bottom:15px;" class="form-group">
                 <input type="hidden" id="idlokaliteta" name = "idlokaliteta" class="form-control">
          </div>
          <div style="margin-top:15px;margin-bottom:15px;" class="form-group">
                 <input type="text" id="naziv" name = "naziv" class="form-control"  placeholder="Naziv eko akcije" required>
          </div>

          <div style="margin-top:15px;margin-bottom:15px;" class="form-group">
                 <p>Pocetak eko akcije:</p>
                 <input type="datetime-local" id="pocetak_ekoakcije" name = "pocetak_ekoakcije" required>
          </div>
          <button style="margin-bottom: 15px;" id="createevent" type="submit" name = "createevent" class="btn submit" onclick="add_event()">Spremi</button>
        </div>
        <div class="col-md-2"></div>
        </div>
      </div>
      </div>
    </div>
  </div>

<script>
    //Dohvacanje lokacija i spremanje u 2D polje
    var locations = <?php get_all_data("lokaliteti") ?>;
    var events = <?php get_all_data("eko_akcije") ?>;
    <?php if(isset($_SESSION['e_mail'])){ ?>
    var claim_status = <?php get_all_data("zahtjevi")?>;
    <?php } ?>

    function join_cancel_event(ID_ekoakcije){
      var button_value = document.getElementById("ecoevent"+ID_ekoakcije).value;
      if(button_value){
        $.ajax
        ({
            type: 'post',
            url: 'Join_Cancle_EcoAction_Event.php',
            dataType: 'JSON',
            data: {
                status: button_value,
                idekoakcije: ID_ekoakcije
            },
            success: function(response)
            {
              //Dohvati ID_korisnika
              <?php if(isset($_SESSION['e_mail'])){
              $_mail = $_SESSION['e_mail'];
              $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
              $userID = $user_query -> fetch_assoc();
              $ID = $userID['ID_korisnika']; ?>

              if(response.akcija == "Zahtjev_dodan"){
                idzahtjeva = response.idzahtjeva;
                //Stvori novi element zahtjeva +dodaj ga na pocetak
                var newrequest = [idzahtjeva,ID_ekoakcije,"obrada",<?php echo $ID;?>];
                claim_status.unshift(newrequest);
                //Promjeni gumb
                document.getElementById("ecoevent"+ID_ekoakcije).classList.add('btnred');
                document.getElementById("ecoevent"+ID_ekoakcije).classList.remove('btngreen');
                document.getElementById("ecoevent"+ID_ekoakcije).innerHTML = "Ponisti zahtjev";
                document.getElementById("ecoevent"+ID_ekoakcije).value = "cancel";
              }
              else{
                //Ukloni obrisani zahtjev iz polja zahtjeva za odredeni uron
                var index;
                if(claim_status.length > 0){
                for(var i = 0; i < claim_status.length; i++){
                  if(claim_status[i][1] == ID_ekoakcije && claim_status[i][3] == <?php echo $ID; ?>){
                    index = i;
                  }
                }
                claim_status.splice(index,i);
                }
                    //Promjeni gumb
                  document.getElementById("ecoevent"+ID_ekoakcije).classList.add('btngreen');
                  document.getElementById("ecoevent"+ID_ekoakcije).classList.remove('btnred');
                  document.getElementById("ecoevent"+ID_ekoakcije).innerHTML = "Pridruzi se";
                  document.getElementById("ecoevent"+ID_ekoakcije).value = "join";
              }
              <?php } ?>
             }
        });
      }
      return false;
    }

    function add_event(){
      var naziv_eko_akcije = document.getElementById("naziv").value;
      var pocetak_ekoakcije = document.getElementById("pocetak_ekoakcije").value;
      var id_lokaliteta = document.getElementById("idlokaliteta").value;

      if(naziv_eko_akcije && pocetak_ekoakcije){
        $.ajax
        ({
            type: 'post',
            url: 'Add_EcoAction_Event.php',
            dataType: 'JSON',
            data: {
                naziv: naziv_eko_akcije,
                pocetakekoakcije: pocetak_ekoakcije,
                idlokaliteta: id_lokaliteta
            },
            success: function(response)
            {
              //Dohvati HTML i dodaj ga u sve urone
              var html = response.html;
              document.getElementById("allevents").innerHTML = html + document.getElementById("allevents").innerHTML;
              //Dohvati ID korisnika
              <?php if(isset($_SESSION['e_mail'])){
              $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
              $userID = $user_query -> fetch_assoc();
              $ID = $userID['ID_korisnika'];?>
              //Dohvati ID eko akcije i zahtjev kako bi se dodali u polje dosadasnjih
              var idekoakcije = response.idekoakcije;
              var zahtjev = response.zahtjev;
              //Kreiraj novi uron i dodaj ga u postojece
              var newevent = [idekoakcije,naziv_eko_akcije,pocetak_ekoakcije,"<?php echo $ID; ?>",id_lokaliteta,
                              "<?php if(isset($_SESSION['ime'])) echo $_SESSION['ime']; ?>",
                              "<?php if(isset($_SESSION['prezime'])) echo $_SESSION['prezime']; ?>",
                              "<?php if(isset($_SESSION['e_mail'])) echo $_SESSION['e_mail']; ?>"];
              events.unshift(newevent);
              //Kreiraj novi zahtjev i dodaj ga u postojece
              var newrequest = [zahtjev[0][0],zahtjev[0][1],zahtjev[0][2],zahtjev[0][3]];
              claim_status.unshift(newrequest);
              <?php } ?>
            }
        });
      }
      reset_form();
      return false;
    }

    function list_events(all_events,location_ID){
      var all_events_div = document.getElementById('allevents');
      var button;
      var vrijeme;
      for(var i=0; i < all_events.length; i++){
          if(all_events[i][4] == location_ID){ //Pronadi ekoakcije za tu lokaciju po ID i ispisi ih
              vrijeme_ekoakcije = new Date(all_events[i][2]);
              vrijeme_sada = new Date();
              if(vrijeme_ekoakcije > vrijeme_sada){ //Ispisi samo ekoakcije koje nisu zastarjele
              <?php if(isset($_SESSION['e_mail'])){ ?>
                var zahtjev = ["","",""];

              for(var j=0; j < claim_status.length; j++){
                if(claim_status[j][1] == all_events[i][0]){
                  zahtjev = claim_status[j];
                }
              }
              <?php
              $e_mail = $_SESSION['e_mail'];

              //Dohvati ID_korisnika koji je logiran
              $user_query = $mysqli->query("SELECT ID_korisnika FROM korisnici WHERE Email = '$e_mail'");
              $userID = $user_query -> fetch_assoc();
              $ID = $userID['ID_korisnika'];
              ?>

              if(zahtjev[2] == "" && all_events[i][3] != <?php echo $ID; ?>){
                button = "<button type='submit' style='margin: auto;font-size: 12px;' id='ecoevent"+all_events[i][0]+"' class='btn btngreen' value='join' onclick='join_cancel_event("+all_events[i][0]+")'>Pridruzi se</button>";
              }
              else if(zahtjev[2] == 'obrada'){
                button = "<button type='submit' style='margin: auto;font-size: 12px;' id='ecoevent"+all_events[i][0]+"' class='btn btnred' value='cancel' onclick='join_cancel_event("+all_events[i][0]+")'>Poništi zahtjev</button>";
              }
              else if(zahtjev[2]  == 'accepted'){
                button = "<p style='font-size:15px;'>Pridruzen</p>";
              }
              else{
                button = "";
              }
              <?php } ?>

              var element = "<div class='comment_box'>"+
                              "<div style='display:flex;flex-wrap: wrap;align-items: center;justify-content:center;text-align:center;'>"+
                                "<p class='comment_text' style='font-size:16px;'>"+
                                  all_events[i][1] +
                                "</p>"+
                                "<button style='margin:10px 10px 10px 10px;font-size: 12px;' id='moreaboutevent' class='btn submit' onclick='toggleElement(eventdetails"+all_events[i][0]+")'>Detaljnije</button>"+
                                "</div>"+
                                "<div id='eventdetails"+all_events[i][0]+"' style='display:none;text-align:center;margin-bottom: 10px;'>"+
                                "<p class='comment_text'>Osnivač eko akcije: "+ all_events[i][5] + " " + all_events[i][6] + "</p>"+
                                "<p class='comment_text'>Email kontakt: "+ all_events[i][7] + "</p>"+
                                "<p class='comment_text'>Pocetak eko akcije: "+ all_events[i][2] + "</p>"+
                                "<div id='eventbutt"+all_events[i][0]+"'>"+
                                "<?php if(isset($_SESSION['e_mail'])){?>"+button+"<?php } ?>" +
                                "</div>"+
                                "</div>"+
                             "</div>";
              all_events_div.innerHTML += element;
            }
          }
        }
    }

    //Resetiranje forme za dodavanje dogadaja
    function reset_form(){
      document.getElementById("naziv").value = "";
      document.getElementById("pocetak_ekoakcije").value = "";
      document.getElementById("idlokaliteta").value = "";
    }

    //Otvori formu za kreiranje dogadaja i spremi u formu ID_lokaliteta
    function open_event_form(location_ID){
      reset_form();
      document.getElementById("idlokaliteta").value = location_ID;
      $("#event_form").show();
    }


      function showElement(element){
        $(element).show();
      }

      function hideElement(element){
          $(element).hide();
      }

      function toggleElement(element){
        $(element).toggle();
      }

      //Ucitavanje mape lokaliteta
      function initMap() {
          //Definiranje markera i polja markera u kojeg ce se spremiti svi markeri
          var marker;
          var markers = new Array();
          //Kreiranje infowindow-a koji se otvara na klik markera ili kreiranje novog markera
          var infowindow = new google.maps.InfoWindow();
          //Kreiranje mape sa centrom
          var zadar = {lat: 44.1194, lng: 15.2314};
          var map = new google.maps.Map(document.getElementById('map'), {zoom: 7, center: zadar});
          // Kreiranje markera, dodavanje u polje markera
          for(var i = 0; i < locations.length; i++){
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][3],locations[i][4]),
                map: map,
                html: '<div id="infowindow">' +
                      '<h4 style="color:black;text-align:center;">'+locations[i][1]+'</h4>'+
                      '<h6 style="color:black;">Opis lokacije:</h6>'+
                      '<p class="descriptiontext">'+locations[i][2]+'</p>'+
                      '<hr color = "black">'+
                      '<?php if(isset($_SESSION['e_mail'])){ ?>'+
                        '<div style="margin-bottom: 15px;" class="row text-center">'+
                          '<div class="col-sm-12">'+
                              '<button style="width: 100%;" id="eventform" type="submit" name = "eventform" class="btn openform" onclick="open_event_form('+locations[i][0]+')">Kreiraj eko akciju</button>'+
                          '</div>'+
                        '</div>'+
                      '<?php } ?>'+
                      '<h6 style="color:black;">Popis eko akcija:</h6>'+
                      '<div id="allevents">'+

                      '</div>'+
                      '</div>',
                id: locations[i][0]
            });
            markers.push(marker);
            //Otvori i zatvori da se ucita html tag u infowindow -> Da nema ovoga nebi se prepoznao allcomments div
            infowindow.setContent(marker.html);
            infowindow.setOptions({maxWidth:300});
            infowindow.open(map,marker);
            infowindow.close();
            //Postavljanje infowindow-a na svaki marker
            google.maps.event.addListener(marker, 'click', (function(marker, i){
              return function(){
                infowindow.setContent(marker.html);
                infowindow.setOptions({maxWidth:300});
                infowindow.open(map,marker);
                $("#event_form").hide();
                list_events(events,marker.id);
              }
            }) (marker, i));

          }

          //Dodavanje novog lokaliteta
          <?php if(isset($_SESSION['e_mail'])){ ?>
          google.maps.event.addListener(map, 'click', function(e) {
              //Zatvaranje infowindow-a
              infowindow.close();
              $("#event_form").hide();
          });
          <?php } ?>
        }
</script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsCJTV2_xmCNEGY1RPywZQsOl1Q807c34"></script>
  <script>
      //Ovo sprijecava da se forma subbmita na refresh stranice
      if ( window.history.replaceState ) {
          window.history.replaceState( null, null, window.location.href );
      }
  </script>
  <script src="Ara.js"></script>
</body>

</html>
