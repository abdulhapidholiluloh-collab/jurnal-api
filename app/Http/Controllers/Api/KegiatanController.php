<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    // GET: Ambil semua data kegiatan
    public function index()
    {
        $kegiatans = Kegiatan::latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Daftar kegiatan berhasil diambil',
            'data' => $kegiatans
        ], 200);
    }

    // POST: Simpan kegiatan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'judul_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $kegiatan = Kegiatan::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Kegiatan berhasil ditambahkan',
            'data' => $kegiatan
        ], 201);
    }

    // GET: Ambil detail 1 kegiatan (untuk form edit)
    public function show(Kegiatan $kegiatan)
    {
        return response()->json([
            'status' => true,
            'data' => $kegiatan
        ], 200);
    }

    // PUT/PATCH: Update kegiatan
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'judul_kegiatan' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $kegiatan->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Kegiatan berhasil diperbarui',
            'data' => $kegiatan
        ], 200);
    }

    // DELETE: Hapus kegiatan
    public function destroy(Kegiatan $kegiatan)
    {
        $kegiatan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kegiatan berhasil dihapus'
        ], 200);
    }
}
