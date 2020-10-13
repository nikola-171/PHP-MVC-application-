
$(document).ready(function(){
    $("#forma").submit(function(e){
        return false;
    });
    $("#prosledi").click(function(){
        $("#load").html("");

        let korisnicko_ime = $("#korisnicko_ime").val();
        $.ajax({
            url: '/resetPreduzece',
            type: 'post',
            data: {korisnicko_ime : korisnicko_ime},
            success: function(response){
                $("#load").removeClass("spinner-border");
                $("#load").html(response);
            },
            beforeSend: function() {
                $("#load").addClass("spinner-border");
                //$("#load").text("loading....");
            }
        });
    });
});