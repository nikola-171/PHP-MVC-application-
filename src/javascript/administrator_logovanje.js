$(document).ready(function(){
    /*imaći ajax poziv da detektuje tekući jezik */

    $("#forma_sadrzaj").submit(function(e){
        return false;
    });

    $("#prosledi").click(function(){
        $("#load").html("");
        let administrator_ime = $("#administrator_ime").val();
        let administrator_lozinka = $("#administrator_lozinka").val();

        if(administrator_ime === "" || administrator_lozinka === ""){
            alert(prazna_polja);
        }else{
            $.ajax({
                url: '/verifikacija_administratora',
                type: 'post',
                data: {administrator_ime : administrator_ime, administrator_lozinka : administrator_lozinka},
                success: function(response){
                    $("#load").removeClass("spinner-border");
                    if(response == "moze"){
                        $.ajax({
                            url: '/postavi_sesiju_administratora',
                            type: 'post',
                            data: {administrator_ime : administrator_ime},
                            success: function(response){
                                window.location.href = "/pocetna_strana_administratora";
                            },
                            beforeSend: function() {
                                $("#load").addClass("spinner-border");

                            }
                        });
                    }else{
                        $("#load").html(response);
                    }
                },
                beforeSend: function() {
                    $("#load").addClass("spinner-border");
                }
            });
        }
    });
});