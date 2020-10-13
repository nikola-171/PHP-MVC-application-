function izbrisi_fizicko_lice(id_objave){
    let id = id_objave;
    $.ajax({
        url : '/administrator_izbrisi_fizicko_lice',
        type : 'post',
        data : {id : id},
        success: function(response){
            if(response === 'uspeh'){
               /* $(dugme).closest("tr").remove();*/
               $("#"+id_objave).remove();
               $("#"+id_objave+"-manji").remove();

            }else{
                alert(greska);
                $('#button-'+id_objave).show();
                $('#akcija-'+id_objave).removeClass("spinner-border");

            }
        },
        beforeSend: function() {
            $('#button-'+id_objave).hide();
            $('#akcija-'+id_objave).addClass("spinner-border");
        }
    });
}
