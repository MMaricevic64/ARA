<?php
 session_start();
 include("Navbar.php");
?>

<body id= "naslovna">

  <section id = "uvod">
  <div id="slideshow" class="carousel slide carousel-fade" data-ride="carousel">
      <!-- Indicator dots -->

      <ol class="carousel-indicators">
          <li data-target="#slideshow" data-slide-to="0" class="active"></li>
          <li data-target="#slideshow" data-slide-to="1"></li>
          <li data-target="#slideshow" data-slide-to="2"></li>
      </ol>

      <!-- Wraper for slides -->

        <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
                <img class="d-block w-100" src="AllPictures/Slideshow1.jpg"/>
                <div class="carousel-caption first-cap">
                    <h1>Ronjenje je Vaša strast?</h1>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="AllPictures/Slideshow2.jpg"/>
                <div class="carousel-caption sec-cap">
                  <h1>More je Vaš drugi dom?</h1>
                </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="AllPictures/Slideshow3.jpg"/>
                <div class="carousel-caption third-cap">
                  <h1>Ljubimac ste morskih dubina?</h1>
                </div>
            </div>
        </div>

      <!-- Controls - next and prev buttons-->

        <a class="carousel-control-prev" href="#slideshow" role="button" data-slide="prev">
           <span class="carousel-control-prev-icon" aria-hidden="true"></span>
           <span class="sr-only">Previous</span>
        </a>

        <a class="carousel-control-next" href="#slideshow" role="button" data-slide="next">
           <span class="carousel-control-next-icon" aria-hidden="true"></span>
           <span class="sr-only">Next</span>
        </a>

    </div>
  </section>
  <section id = "services">
    <div class="container">
			<h1>Što sve pruža ARA sustav?</h1>
			<div class="row services">
				<div class="col-md-4 text-center" onclick="location.href='Dive.php';">
					<div class="circle-icon">
						<img class = "imgicon" src = "AllPictures/ServicesDiveIcon.png">
					</div>
					<h3>Planiranje urona</h3>
					<p>Odaberite jedan od ponuđenih lokaliteta, kreirajte događaj, pozovite ekipu i avantura može početi!</p>
				</div>
				<div class="col-md-4 text-center" onclick="location.href='Register.php';">
					<div class="circle-icon">
					<img class = "imgicon" src = "AllPictures/ServicesNewUserIcon.png">
					</div>
					<h3>Vlastiti profil</h3>
					<p>Kreirajte svoj profil kako bi iskoristili sve mogućnosti našeg sustava i bili dio ARA obitelji!</p>
				</div>
				<div class="col-md-4 text-center" onclick="location.href='EcoActions.php';">
					<div class="circle-icon">
						<img class = "imgicon" src = "AllPictures/ServicesEcoActionIcon.png">
					</div>
					<h3>EKO akcije</h3>
					<p>Očuvanje čistoće mora i okoliša naša je zadaća. Budi i ti dio EKO akcija i pruži pomoć svojoj prirodi! </p>
				</div>
				<div class="col-md-4 text-center" onclick="location.href='Clubs.php';">
					<div class="circle-icon">
						<img class = "imgicon" src = "AllPictures/ServicesClubIcon.png">
					</div>
					<h3>Ronilački timovi</h3>
					<p>Još uvijek nemaš svoj tim? Pridruži se timu, upoznaj nove ljude i iskusi čari timskog ronjenja.</p>
				</div>
				<div class="col-md-4 text-center" onclick="location.href='Contact.php';">
					<div class="circle-icon">
							<img class = "imgicon" src = "AllPictures/ServicesContactIcon.png">
					</div>
					<h3>Informacije</h3>
					<p>Svoje ideje, mišljenja, komentare i kritike u vezi našeg sustava slobodno proslijedite. Mi smo tu za Vas!</p>
				</div>
				<div class="col-md-4 text-center" onclick="location.href='<?php if(isset($_SESSION['e_mail'])){
                      echo "Results.php";}
                  else{
                      echo "Register.php";
                  }
          ?>';">
					<div class="circle-icon">
							<img class = "imgicon" src = "AllPictures/ServicesResultIcon.png">
					</div>
					<h3>Statistika ronjenja</h3>
					<p>Kreiranjem profila u mogućnosti ste pratiti sve Vaše dosadašnje događaje i rezultate te pratiti Vaš napredak!</p>
				</div>
			</div>

		</div>
  </section>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <script src="Ara.js"></script>
</body>

</html>
