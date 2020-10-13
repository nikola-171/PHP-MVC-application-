function izbrisi_preduzece(id_objave){
    let id = id_objave;
    $.ajax({
        url : '/administrator_izbrisi_preduzece',
        type : 'post',
        data : {id : id},
        success: function(response){
            if(response === 'uspeh'){
                /*$(dugme).closest("tr").remove();*/
                $("#"+id_objave).remove();
                $("#"+id_objave+"-manji").remove();
            }else{
                alert(greska);
                $('#akcija-'+id_objave).removeClass("spinner-border");
                $('#button-'+id_objave).show();
            }
        },
        beforeSend: function() {
            $('#button-'+id_objave).hide();
            $('#akcija-'+id_objave).addClass("spinner-border");

        }
    });
}
