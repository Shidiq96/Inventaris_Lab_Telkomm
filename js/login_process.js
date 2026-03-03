function loadMatkul() {
    var kelasId = document.getElementById("kelas_select").value;
    var matkulSelect = document.getElementById("matkul_select");
    
    matkulSelect.innerHTML = "<option value=''>Loading...</option>";
    matkulSelect.disabled = true;

    if(kelasId){
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "login_process.php?get_matkul&kelas_id=" + kelasId, true);
        xhr.onload = function(){
            if(this.status == 200){
                matkulSelect.innerHTML = this.responseText;
                matkulSelect.disabled = false;
            } else {
                matkulSelect.innerHTML = "<option value=''>Error</option>";
            }
        };
        xhr.send();
    } else {
        matkulSelect.innerHTML = "<option value=''>-- Pilih Kelas Dahulu --</option>";
    }
}