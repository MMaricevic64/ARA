<!DOCTYPE html>
<html>
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
      <meta http-equiv="X-UA-Compatible" content="IE-edge">
      <title>ARA sustav</title>

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        <link media="all" rel="stylesheet" type="text/css" href="ara.css">
    </head>

  <header>
    <nav class="navbar navbar-expand-xl fixed-top">
        <div class="container">
              <a class="navbar-brand">
                  <img src="AllPictures/logo.jpg" href="Naslovna.php">
              </a>
              <button id="hamburger" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">
                    <span class="fas fa-bars" style = "color: white;"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarResponsive">
                  <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                      <a class="nav-link" href="Homepage.php"><i class="fas fa-info-circle"></i>
                        Što je ARA?
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="Locality.php"><i class="fas fa-map-marker-alt"></i>
                        Lokaliteti
                      </a>
                    </li>

                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="far fa-list-alt"></i>
                        Akcije
                      </a>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="Dive.php"><i class="fas fa-water"></i>
                          Uroni
                            </a>
                        </li>
                         <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item" href="EcoActions.php"><i class="fas fa-leaf"></i>
                          EKO akcije
                            </a>
                        </li>
                      </ul>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="Clubs.php"><i class="fas fa-users"></i>
                        Klubovi
                      </a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" href="Contact.php"><i class="fas fa-address-book"></i>
                        Kontakt
                      </a>
                    </li>

                    <?php
                      if(isset($_SESSION['e_mail'])){
                        ?>
                          <li class="nav-item dropdown avatar">
                            <a class="nav-link" data-toggle="dropdown">
                               <img src="profile_images/<?php include 'DBI.php';
                               $e_mail = $_SESSION['e_mail'];
                               $result = $mysqli->query("SELECT * FROM korisnici WHERE Email = '$e_mail'");
                               $user = $result->fetch_assoc();echo $user['Slika'];?>"
                                class = "profile-pic" alt="Slika profila">
                            </a>
                              <ul class="dropdown-menu">
                                <li>
                                <p class="dropdown-item"><?php echo $_SESSION['ime'];echo ' ';echo $_SESSION['prezime'];?></p>
                                </li>
                                <li><a class="dropdown-item" href="ProfileSettings.php"><i class="fas fa-user-edit"></i>
                                      Postavke profila
                                    </a>
                                </li>
                                 <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="Results.php"><i class="fas fa-chart-bar"></i>
                                      Moji rezultati
                                  </a>
                                </li>
                                 <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="Logout.php"><i class="fas fa-sign-out-alt"></i>
                                  Odjava
                                  </a>
                                </li>
                              </ul>
                          </li>
                          <?php
                      } else {
                      ?>
                        <li class="nav-item">
                          <a class="nav-link" href="Register.php"><i class="fas fa-user-alt"></i>
                            Prijava
                          </a>
                        </li>
                        <?php
                      }
                      ?>

                  </ul>
              </div>
        </div>
    </nav>
  </header>
