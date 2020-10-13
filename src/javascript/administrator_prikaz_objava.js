function izbrisi_objavu(id_objave){
    let id = id_objave;
    $.ajax({
        url : '/administrator_izbrisi_objavu',
        type : 'post',
        data : {id : id},
        success: function(response){
            if(response === 'uspeh'){
                $("#"+id_objave).remove();
                $("#"+id_objave+"-manji").remove();
            }else{
                $('#akcija-'+id_objave).removeClass("spinner-border");
                $('#button-'+id_objave).show();
                $('#button-show-'+id_objave).show();
                alert(greska);
            }
        },
        beforeSend: function() {
            $('#button-'+id_objave).hide();
            $('#button-show-'+id_objave).hide();
            $('#akcija-'+id_objave).addClass("spinner-border");
        }
    });
}
