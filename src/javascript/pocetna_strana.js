/*učitavanje novih objava korišćenjem ajax poziva*/

$(document).ready(function(){
    $("#reset").click(function(){
        window.location.href = "/logovanje";
    });
  });

var params = "";

function ajaxRequest(){
    try {
        var request = new XMLHttpRequest();
    }
    catch(e1) {
        try {
            request = new ActiveXObject("Msxm12.XMLHTTP");
        }
        catch(e2) {
            try {
                request = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e3) {
                request = false;
            }   
        }
    }
    return request;
}

$("#ucitavanje").click(function(){
        window.params = "id="+window.id_zadnjeg_komentara;
        
        window.params = window.params + "&datum="+window.datum + "&datumParam="+window.datumParam;
        window.params = window.params + "&naziv="+window.naziv + "&naslov="+window.naslov;
        window.params = window.params + "&sadrzi=" + window.sadrzi;
        
        var azuriran_id = 0;
        let request = ajaxRequest();
        if(request !== false){
           request.open("POST", "/ucitajObjave", true);
           request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
           request.setRequestHeader("Content-length", params.length);
           request.setRequestHeader("Connection", "close");

           request.onreadystatechange = function() {
                if(this.readyState == 4 && this.status == 200)
                {
                    var niz = JSON.parse(this.responseText);
                    var izlaz3 = "";
                        
                    var novi = 0;
                    if(niz.length == 0){
                        alert(nema);
                    }
                    for(var i = 0; i < niz.length; i++){
                        novi = niz[i].id;
                        izlaz3 += `
                       <div class="komentar">
                            <hr>
                            <h5> ${niz[i].naziv + " " + niz[i].datum}</h5>`;

                        if(auth == "company"){
                            if(korisnik == niz[i].preduzece){
                                izlaz3 += `
                                <form action="/izbrisiObjavu" method="post">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="${niz[i].id}">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Obrišite objavu</button>
                                        </div>
                                    </form>
                                `;
                            }
                        }
                        izlaz3 += `
                            <p>${niz[i].tekst}</p>
                        `;

                        if(niz[i].putanja_slike != null){
                            if(niz[i].orijentacija == "s"){
                                izlaz3 += `
                                <div class="container">
                                        <img src="/../${niz[i].putanja_slike}" class="sira rounded" height="100" onclick="document.getElementById('{{item.id}}').click();">
                                    </div>
                                `;
                            }else{
                                izlaz3 += `
                                <div class="container">
                                        <img src="/../${niz[i].putanja_slike}" class="rounded" style="max-height:95vh;" onclick="document.getElementById('{{item.id}}').click();">
                                    </div>
                                `;
                            }
                        }
                        izlaz3 += `<h3> ${niz[i].tekst}</h3>
                            <hr>
                            </div>`;
                        
                    }
                    window.id_zadnjeg_komentara = novi;

                    
                    $("#rez").append(izlaz3);
                    
                }
            }
           request.send(params);


        }else{
            alert('Došlo je do greške prilikom učitavanja objava.');
        }
});





$(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
$(function(){
    $('#filter').on('click',function(){
    $('#pretraga').toggle();
    });
});
//Get the button:
mybutton = document.getElementById("myBtn");


window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 40 || document.documentElement.scrollTop > 40) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
        }
    }

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}
function ubaci_sliku(){
    document.getElementById('getFile').click();
}

$("#forma_sadrzaj").submit(function(e){
    return test();
});
function test(){
    let naslov = $("#naslov").val();
    let sadrzaj = $("#sadrzaj").val();
    let datum = $("#datum").val();


    if(naslov === '' || sadrzaj === '' || datum == ''){
        alert(prazna);
        return false;
    }else{        
        return true;
    }    
}

