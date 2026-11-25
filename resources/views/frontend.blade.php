<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Kegiatan PKL (Versi API)</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>



<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold">Jurnal PKL ITB — REST API + JS Fetch</span>
    </div>
</nav>

<div class="container">
    <!-- Kotak Notifikasi Dinamis -->
    <div id="alert-box" class="alert alert-success d-none" role="alert"></div>

    <div class="row">
        <!-- BAGIAN KIRI: FORM TAMBAH / EDIT -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white" id="form-title">
                    Tambah Kegiatan Baru
                </div>
                <div class="card-body">
                    <form id="form-kegiatan">
                        <input type="hidden" id="kegiatan_id">

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" id="tanggal" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul Kegiatan</label>
                            <input type="text" id="judul_kegiatan" class="form-control" placeholder="Contoh: Membuat REST API" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea id="deskripsi" rows="3" class="form-control" placeholder="Detail pekerjaan..." required></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100" id="btn-simpan">Simpan</button>
                            <button type="button" class="btn btn-secondary d-none" id="btn-batal" onclick="resetForm()">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- BAGIAN KANAN: TABEL DATA DARI API -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">
                    Daftar Kegiatan (Live dari <code>/api/kegiatan</code>)
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Judul & Deskripsi</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-body">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Memuat data dari API...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const API_URL = '/api/kegiatan';

    // 1. READ: Ambil data dari API saat halaman dibuka
    async function loadKegiatan() {
        const res = await fetch(API_URL, {
            headers: { 'Accept': 'application/json' }
        });
        const result = await res.json();
        const tbody = document.getElementById('tabel-body');
        tbody.innerHTML = '';

        if (result.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Belum ada data kegiatan.</td></tr>';
            return;
        }

        result.data.forEach((item, index) => {
            tbody.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.tanggal}</td>
                    <td>
                        <strong>${item.judul_kegiatan}</strong><br>
                        <small class="text-muted">${item.deskripsi}</small>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editKegiatan(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="hapusKegiatan(${item.id})">Hapus</button>
                    </td>
                </tr>
            `;
        });
    }

    // 2. CREATE & UPDATE: Kirim data form ke API (POST / PUT)
    document.getElementById('form-kegiatan').addEventListener('submit', async function(e) {
        e.preventDefault(); // Mencegah browser reload halaman!

        const id = document.getElementById('kegiatan_id').value;
        const payload = {
            tanggal: document.getElementById('tanggal').value,
            judul_kegiatan: document.getElementById('judul_kegiatan').value,
            deskripsi: document.getElementById('deskripsi').value
        };

        const url = id ? `${API_URL}/${id}` : API_URL;
        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await res.json();
        if (res.ok) {
            tampilNotifikasi(result.message);
            resetForm();
            loadKegiatan(); // Muat ulang isi tabel secara instan
        } else {
            alert('Gagal menyimpan: ' + (result.message || 'Periksa kembali isian form'));
        }
    });

    // 3. Ambil 1 data untuk masuk ke form Edit
    async function editKegiatan(id) {
        const res = await fetch(`${API_URL}/${id}`, {
            headers: { 'Accept': 'application/json' }
        });
        const result = await res.json();
        const data = result.data;

        document.getElementById('kegiatan_id').value = data.id;
        document.getElementById('tanggal').value = data.tanggal;
        document.getElementById('judul_kegiatan').value = data.judul_kegiatan;
        document.getElementById('deskripsi').value = data.deskripsi;

        document.getElementById('form-title').innerText = 'Edit Kegiatan';
        document.getElementById('form-title').classList.replace('bg-primary', 'bg-warning');
        document.getElementById('btn-simpan').innerText = 'Update';
        document.getElementById('btn-batal').classList.remove('d-none');
    }

    // 4. DELETE: Hapus data lewat API
    async function hapusKegiatan(id) {
        if (!confirm('Yakin ingin menghapus kegiatan ini?')) return;

        const res = await fetch(`${API_URL}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });

        const result = await res.json();
        if (res.ok) {
            tampilNotifikasi(result.message);
            loadKegiatan();
        }
    }

    function resetForm() {
        document.getElementById('form-kegiatan').reset();
        document.getElementById('kegiatan_id').value = '';
        document.getElementById('form-title').innerText = 'Tambah Kegiatan Baru';
        document.getElementById('form-title').classList.replace('bg-warning', 'bg-primary');
        document.getElementById('btn-simpan').innerText = 'Simpan';
        document.getElementById('btn-batal').classList.add('d-none');
    }

    function tampilNotifikasi(pesan) {
        const box = document.getElementById('alert-box');
        box.innerText = pesan;
        box.classList.remove('d-none');
        setTimeout(() => box.classList.add('d-none'), 3000);
    }

    // Jalankan pertama kali saat halaman selesai dimuat
    loadKegiatan();
</script>
</body>
</html>
