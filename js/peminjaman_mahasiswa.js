    const fileInput = document.getElementById('foto_ktm');
    const previewContainer = document.getElementById('previewContainer');
    const previewImg = document.getElementById('previewImg');
    const dropArea = document.getElementById('dropArea');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        handleFile(file);
    });

    function handleFile(file) {
        if (file) {
            // Validasi Ukuran (2MB)
            if (file.size > 2000000) {
                alert("Ukuran file terlalu besar! Maksimal 2MB.");
                fileInput.value = ""; // Reset
                previewContainer.style.display = 'none';
                return;
            }

            // Validasi Tipe
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                alert("Format file salah! Hanya JPG, JPEG, dan PNG.");
                fileInput.value = "";
                previewContainer.style.display = 'none';
                return;
            }

            // Baca dan Tampilkan Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'flex';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    }

function updateInput(checkbox, max_val, index) {
    var input = document.getElementById('input_jumlah_' + index);
    
    if (checkbox.checked) {
        input.disabled = false;
        input.value = 1;
        input.max = max_val;
        input.focus();
    } else {
        input.disabled = true;
        input.value = 0;
    }
}

function validateSubmit() {
    var checkboxes = document.querySelectorAll('input[name="nama_barang[]"]');
    var isAnyChecked = false;
    var totalItems = 0;
    checkboxes.forEach(function(box) {
        if (box.checked) {
            isAnyChecked = true;
            var id = box.id.replace('check_', 'input_jumlah_');
            var inputQty = document.getElementById(id);
            if (inputQty && parseInt(inputQty.value) > 0) {
                totalItems += parseInt(inputQty.value);
            }
        }
    });
    var inputNama = document.querySelector('input[name="nama_mahasiswa"]');
    if (!isAnyChecked) {
        alert("Silakan pilih setidaknya satu barang.");
        return false;
    }
    if (inputNama.value.trim() === "") {
        alert("Nama Mahasiswa wajib diisi!");
        inputNama.focus();
        return false;
    }
    if (totalItems <= 0) {
        alert("Total jumlah pinjam harus lebih dari 0.");
        return false;
    }
    var inputCaptcha = document.querySelector('input[name="captcha"]');
    if (inputCaptcha.value.trim() === "") {
        alert("Kode Captcha wajib diisi!");
        inputCaptcha.focus();
        return false;
    }
    return confirm("Apakah Anda yakin ingin meminjam " + totalItems + " unit barang ini?");
}