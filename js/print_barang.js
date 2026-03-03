// Tidak ada kode JavaScript khusus pada print_barang.php selain window.close() dan window.print(),
// namun untuk konsistensi, kita bisa membuat file JS kecil jika ingin mengelola tombol tutup/print secara eksternal.
function closeOrBack() {
    if(!window.close()) window.history.back();
}
// Fungsi print sudah native: window.print()
