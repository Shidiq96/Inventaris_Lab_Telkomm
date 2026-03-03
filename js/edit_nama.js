// Tidak ada kode JS murni di edit_nama.php, namun jika ingin handle konfirmasi hapus via JS eksternal:
function showBarcode(kode) {
    document.getElementById('barcodeImage').src =
        'generate_barcode.php?kode=' + encodeURIComponent(kode);
    document.getElementById('barcodeModal').style.display = 'flex';
}

function closeBarcode() {
    document.getElementById('barcodeModal').style.display = 'none';
}

function confirmDelete() {
    return confirm('Hapus unit ini permanen?');
}

// Optional: close modal with ESC key
document.addEventListener('keydown', function(e){
    var modal = document.getElementById('barcodeModal');
    if(modal.style.display==='block' && (e.key==='Escape'||e.key==='Esc')){
        closeBarcode();
    }
});