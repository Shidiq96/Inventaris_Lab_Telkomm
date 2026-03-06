    function openAddModal(hari) {
        document.getElementById('add-hari').value = hari;
        document.getElementById('add-hari-display').value = hari;
        document.getElementById('addModal').style.display = 'block';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    // Modal edit untuk jadwal per grup hari
    function openEditJadwalModal(data) {
        document.getElementById('edit-jadwal-id').value = data.id;
        document.getElementById('edit-jadwal-hari').value = data.hari;
        document.getElementById('edit-jadwal-jam_ke').value = data.jam_ke;
        document.getElementById('edit-jadwal-waktu_id').value = data.waktu_id;
        document.getElementById('edit-jadwal-ruang_id').value = data.ruang_id;
        document.getElementById('edit-jadwal-kelas_id').value = data.kelas_id;
        document.getElementById('edit-jadwal-matkul_id').value = data.matkul_id;
        document.getElementById('edit-jadwal-dosen_id').value = data.dosen_id;
        document.getElementById('modalEditJadwal').style.display = 'block';
    }

    function closeEditJadwalModal() {
        document.getElementById('modalEditJadwal').style.display = 'none';
    }

    // Modal edit ruang
    function editRuang(id, nama) {
        document.getElementById('edit_id_ruang').value = id;
        document.getElementById('edit_nama_ruang').value = nama;
        document.getElementById('modalEditRuang').style.display = 'block';
    }

    // Modal edit waktu
    function editWaktu(id, waktu) {
        document.getElementById('edit_id_waktu').value = id;
        document.getElementById('edit_waktu').value = waktu;
        document.getElementById('modalEditWaktu').style.display = 'block';
    }

    function closeModal(Id) {
        document.getElementById(Id).style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            closeAddModal();
        }
        if (event.target == document.getElementById('modalEditJadwal')) {
            closeEditJadwalModal();
        }
        if (event.target == document.getElementById('modalEditRuang')) {
            closeModal('modalEditRuang');
        }
        if (event.target == document.getElementById('modalEditWaktu')) {
            closeModal('modalEditWaktu');
        }
    }

    function openMiniPopup(data) {
        console.log("CLICKED", data);
        document.getElementById('mini-id').value = data.id;
        document.getElementById('mini-jam_ke').value = data.jam_ke;
        document.getElementById('mini-waktu_id').value = data.waktu_id;
        document.getElementById('mini-ruang_id').value = data.ruang_id;
        document.getElementById('mini-dosen_id').value = data.dosen_id;

        document.getElementById('miniEditPopup').classList.add('show');
    }

    function closeMiniPopup() {
        document.getElementById('miniEditPopup').classList.remove('show');
    }

    window.addEventListener('click', function(e) {
        if (e.target == document.getElementById('miniEditPopup')) {
            closeMiniPopup();
        }
        
    });