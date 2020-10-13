$(document).ready(function(){
    /*imaći ajax poziv da detektuje tekući jezik */

    $("#forma_sadrzaj").submit(function(e){
        return false;
    });

    $("#prosledi").click(function(){
        $("#load").html("");
        let ime = $("#ime").val();
        let prezime = $("#prezime").val();
        let email = $("#email").val();
        let administrator_ime = $("#administrator_ime").val();
        let administrator_lozinka = $("#administrator_lozinka").val();
        let potvrda_lozinke = $("#potvrda_lozinke").val();


        if(administrator_ime === "" || administrator_lozinka === "" || ime === "" || prezime === "" || email === ""){
            alert(prazna_polja);
        }else{
            if(administrator_lozinka !== potvrda_lozinke){
                alert(ne_poklapanje);
            }else{
                $.ajax({
                    url: '/registracija_administratora',
                    type: 'post',
                    data: {administrator_ime : administrator_ime, administrator_lozinka : administrator_lozinka,
                           ime : ime, prezime : prezime, email : email},
                    success: function(response){
                        $("#load_container").removeClass("spinner-border");
                        $("#load").html(response);
                    },
                    beforeSend: function() {
                        $("#load_container").addClass("spinner-border");
                    }
                });
            }
        }
    });
});