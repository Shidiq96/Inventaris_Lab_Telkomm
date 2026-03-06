    function openEditModal(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_username').value = data.username;
        document.getElementById('edit_nama_lengkap').value = data.nama_lengkap;
        document.getElementById('edit_role').value = data.role;
        document.getElementById('edit_pass').value = '';
        document.getElementById('editModal').style.display = "flex";
        setTimeout(function() {
            document.getElementById('edit_username').focus();
        }, 100);
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == document.getElementById('editModal')) { closeEditModal(); }
    }