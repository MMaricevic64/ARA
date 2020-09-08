<?php
session_start();
include("Navbar.php");

if(!empty($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST'){
  if(isset($_POST["savelocation"])){
    include("DBI.php");

    $lat = $_POST["lat"];
    $lng = $_POST["lng"];
    $naziv = $_POST["naziv"];
    $opis = $_POST["opis"];
    $mysqli->query("INSERT INTO `lokaliteti` (`Naziv`, `Opis`, `Lat`, `Lng`)
    VALUES ('$naziv', '$opis', '$lat', '$lng');");
  }

}

function get_all_data($type_of_data){ //Funkcija za dohvacanje lokacija i komentara
  include("DBI.php");
  //dohvati sve lokacije iz baze
  if($type_of_data == "lokaliteti"){
    $data_query = $mysqli->query("SELECT * FROM $type_of_data");
  }
  else if($type_of_data == "komentari"){
    //Uzmi komentare od novijim prema starijima
    $data_query = $mysqli->query("SELECT Ime,Prezime,Komentar,ID_lokaliteta FROM $type_of_data JOIN korisnici USING (ID_korisnika) ORDER BY ID_komentara DESC");
  }
  $arr = array();

  while($row = $data_query->fetch_assoc()){
    $arr[] = $row;
  }

    $arr = array_map('array_values', $arr);
    echo json_encode($arr);
}

?>
<body id= "lokaliteti" onload="initMap();">
  <div style="padding: 60px 25px 40px 25px;">
    <h1 style ="margin-bottom: 40px;text-align: center;">Pregled lokaliteta</h1>
      <div id="map" ></div>
  </div>

<script>
//Dohvacanje lokacija i spremanje u 2D polje
var locations = <?php get_all_data("lokaliteti") ?>;
var comments = <?php get_all_data("komentari") ?>;

      function post_comment(location_ID){
        var comment = document.getElementById("comment").value;
        if(comment){
          $.ajax
          ({
              type: 'post',
              url: 'Add_Comment.php',
              data: {
                  user_comm: comment,
                  locality_id: location_ID
              },
              success: function(response)
              {
                document.getElementById("allcomments").innerHTML = response + document.getElementById("allcomments").innerHTML;
                //Stvori novi element komentara polja i dodaj ga na pocetak
                var newcomment = ["<?php if(isset($_SESSION['ime'])) echo $_SESSION['ime']; ?>","<?php if(isset($_SESSION['prezime'])) echo $_SESSION['prezime']?>",comment,location_ID];
                comments.unshift(newcomment);
                document.getElementById("comment").value="";

              }
          });
        }
        return false;
      }

      function list_comments(all_comments,location_ID){
        var all_comments_div = document.getElementById('allcomments');
        for(var j=0; j < all_comments.length; j++){
            if(all_comments[j][3] == location_ID){ //Pronadi komentare za tu lokaciju po ID i ispisi ih
                var element = "<div class='comment_box'>"+
                                  "<p class='comment_text' style='padding-bottom:0px;font-size:16px;'>"+
                                    all_comments[j][0] + " " + all_comments[j][1] +
                                  "</p>"+
                                  "<p class='comment_text'>"+all_comments[j][2]+"</p>"+
                               "</div>";
                all_comments_div.innerHTML += element;
            }
        }
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
                    '<h6 style="color:black;">Komentari:</h6>'+
                    '<?php if(isset($_SESSION['e_mail'])){ ?>'+
                      '<div style="display:flex;align-items: center;margin-bottom: 15px;" class="row text-center">'+
                        '<div class="col-sm-9">'+
                            '<textarea type="text" id = "comment" style="font-size: 14px;"class="form-control" rows="2"  placeholder="Vaš komentar..." required></textarea>'+
                        '</div>'+
                        '<div id="savecommentdiv" class="col-sm-3">'+
                            '<button style="width: 100%;" id="savecomment" type="submit" name = "savecomment" class="btn submit" onclick="post_comment('+locations[i][0]+')">></button>'+
                        '</div>'+
                      '</div>'+
                    '<?php } ?>'+
                    '<div id="allcomments">'+

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
              //U slucaju da je zadnje postavljen marker, cija forma nije ispunjena tj. nije dodan njegov opis treba ga maknuti sa mape i iz polja
              if(markers[markers.length-1].id == "unknown"){
                markers[markers.length-1].setMap(null);
                markers.pop();
              }
              infowindow.setContent(marker.html);
              infowindow.setOptions({maxWidth:300});
              infowindow.open(map,marker);
              list_comments(comments,marker.id);
            }
          }) (marker, i));

        }

        //Dodavanje novog lokaliteta
        google.maps.event.addListener(map, 'click', function(e) {
            var lat = e.latLng.lat(); // lat of clicked point
            var lng = e.latLng.lng(); // lng of clicked point
            //Ako se klikne na mapu, a prije je postavljen marker cija forma nije ispunjena treba ga ukloniti
            if(markers.length > 0){
            if(markers[markers.length-1].id == "unknown"){
              markers[markers.length-1].setMap(null);
              markers.pop();
            }
            }
            //Zatvaranje infowindow-a
            infowindow.close();
            //Kreiranje novog markera i dodavanje u polje
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(lat,lng),
                map: map,
                animation: google.maps.Animation.DROP,
                html:
                    '<div class = "container" >' +
                      '<form action="Locality.php" method ="POST" class = "form_edit" enctype="multipart/form-data">' +
                            '<div class="form-group">'+
					                         '<input type="text" name = "naziv" class="form-control"  placeholder="Naziv lokaliteta" required>'+
				                     '</div>' +
                            '<div class="form-group">'+
  					                       '<textarea type="text" name = "opis" class="form-control" rows="3" placeholder="Zasto bas ova lokacija?" required></textarea>'+
  				                   '</div>' +
                             '<div class="form-group">'+
   					                       '<input type="hidden" name = "lat" class="form-control" value="'+lat+'">'+
   				                   '</div>' +
                             '<div class="form-group">'+
   					                       '<input type="hidden" name = "lng" class="form-control" value="'+lng+'">'+
   				                   '</div>' +
                            '<button id="savelocation" type="submit" name = "savelocation" class="btn submit">Spremi lokaciju</button>'+
                     '</div>',
                id: 'unknown'
            });
            markers.push(marker);
            //Otvaranje infowindow-a u kojem se nalazi forma za dodavanje markera u bazu podataka
            infowindow.setContent(marker.html);
            infowindow.setOptions({maxWidth:500});
            infowindow.open(map,marker);
        });
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
