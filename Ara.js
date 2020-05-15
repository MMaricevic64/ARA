$(document).ready(function(){

  // sakrivanje login forme kod loada
  if($("#inputHiddenId").val() == "register") {
    $(".signup-form").show();
    $(".login-form").hide();
    $("#log_butt").addClass("signbutt_pasive");
    $("#log_butt").removeClass("signbutt_active");
    $("#reg_butt").addClass("signbutt_active");
    $("#reg_butt").removeClass("signbutt_pasive");
  }
  //sakrivanje register forme kod loada
  if($("#inputHiddenId").val() == "login"){
    $(".login-form").show();
    $(".signup-form").hide();
    $("#reg_butt").addClass("signbutt_pasive");
    $("#reg_butt").removeClass("signbutt_active");
    $("#log_butt").addClass("signbutt_active");
    $("#log_butt").removeClass("signbutt_pasive");
  }
});

$("#reg_butt").click(function(){        //ako se pritisne signin -> prikazuje se Register forma -> mijenja se boja donjeg bordera i boja fonta
  $(".signup-form").show();
  $(".login-form").hide();
  $("#log_butt").addClass("signbutt_pasive");
  $("#log_butt").removeClass("signbutt_active");
  $("#reg_butt").addClass("signbutt_active");
  $("#reg_butt").removeClass("signbutt_pasive");

  //resetiranje inputa Login forme,micanje teksta u errorima, i klase koja oboja input u crveni obrub ako se klikne na gumb
  $("#log_form").trigger("reset");
  $("#log_form input").removeClass("is-invalid");
  $(".invalid-feedback").empty();
  $('#licenca select option:first').prop('selected',true); //selektira prvu opcija koja je Odaberi licencu ronjenja
});

$("#log_butt").click(function(){      //ako se pritisne signup -> prikazuje se Login forma -> mijenja se boja donjeg bordera i boja fonta
  $(".login-form").show();
  $(".signup-form").hide();
  $("#reg_butt").addClass("signbutt_pasive");
  $("#reg_butt").removeClass("signbutt_active");
  $("#log_butt").addClass("signbutt_active");
  $("#log_butt").removeClass("signbutt_pasive");

  //resetiranje inputa Registracijske forme,micanje teksta u errorima, i klase koja oboja input u crveni obrub ako se klikne na gumb
  $("#reg_form").trigger("reset");
  $("#reg_form input").removeClass("is-invalid");
  $(".invalid-feedback").empty();
  $("#licenca").removeClass("is-invalid");
  $('#licenca select option:first').prop('selected',true); //selektira prvu opcija koja je Odaberi licencu ronjenja
});

$('#hamburger').click(function(){   //mijenjanje ikonice od Menija
   $(this).find('span').toggleClass(' fa-times');
});

function validacija_forme(){
  var error = 0;  //kontrolna varijabla za gresku

  var ime = document.getElementById("ime");
  var licenca = document.getElementById("licenca"); //vrijednost select-a
  var prezime = document.getElementById("prezime");

  if(!allletters(ime)){           //provjera imena
    ime.value = "";
    ime.classList.add("is-invalid");
    document.getElementById("ime_err").innerHTML = "Ime mora sadržavati samo slova!";
     error = 1;
  }
  else{
    document.getElementById("ime_err").innerHTML = "";
    ime.classList.remove("is-invalid");
  }

  if(!allletters(prezime)){           //provjera prezimena
    prezime.value = "";
    prezime.classList.add("is-invalid");
    document.getElementById("prezime_err").innerHTML = "Prezime mora sadržavati samo slova!";
     error = 1;
  }
  else{
    document.getElementById("prezime_err").innerHTML = "";
    prezime.classList.remove("is-invalid");
  }

  if(licenca.value == "none"){          //provjera jel odabrana licenca
    licenca.classList.add("is-invalid");
    document.getElementById("lic_err").innerHTML = "Niste odabrali vašu ronilačku licencu!";
    error = 1;
  }
  else{
    document.getElementById("lic_err").innerHTML = "";
    licenca.classList.remove("is-invalid");
  }

  if(error == 1){
    return false;
  }
}


function allletters(element){   //funkcija za provjeru jesu li samo slova sadržana u inputu!
    var justletters = /^[A-Za-z\s-]+$/;
    var isItOk = justletters.test(element.value);
    if(!isItOk){
      return false;
    }
    else{
      return true;
    }
}

function promjeni_sliku(){        //funkcije za promjenu slike prilikom izbora za registraciju
  document.getElementById("profile_img").click();
}

function prikazi_sliku(element){
  if(element.files[0]){
    var reader = new FileReader();

    reader.onload = function(element){
      document.getElementById("profileDisplay").setAttribute('src',element.target.result);
    }

    reader.readAsDataURL(element.files[0]);
  }
}
