$(document).ready(function(){
    $("#reset").submit(function(e){
        return false;
    });
    $("#promeni").click(function(){
        let lozinka = $("#lozinka").val();
        let lozinka_ponovo = $("#lozinka_ponovo").val();

        if(lozinka !== lozinka_ponovo){
            $("#load").text(ne_poklapanje);
        }else{
            $.ajax({
                url: '/promenaLozinkePreduzece',
                type: 'post',
                data: {lozinka : lozinka},
                success: function(response){
                    $("#load").html(response);
                },
                beforeSend: function() {
                    $("#load").text("loading....");
                }
            });
        }
    });
});