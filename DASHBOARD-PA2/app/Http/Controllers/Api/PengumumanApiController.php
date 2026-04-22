<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanApiController extends Controller
{
    /**
     * Get semua pengumuman (visible untuk semua siswa)
     */
    public function index()
    {
        try {
            $pengumuman = Pengumuman::with('guru')
                ->orderBy('waktu_unggah', 'desc')
                ->get()
                ->map(function ($item) {
                    $imageUrl = null;
                    if ($item->media) {
                        // Check if media is already full URL or just filename
                        if (str_starts_with($item->media, 'http')) {
                            $imageUrl = $item->media;
                        } else {
                            $imageUrl = config('app.url') . '/storage/pengumuman/' . $item->media;
                        }
                    }
                    
                    return [
                        'id_pengumuman' => $item->id_pengumuman,
                        'judul' => $item->judul,
                        'deskripsi' => $item->deskripsi,
                        'media' => $imageUrl,
                        'nama_guru' => $item->guru?->nama_guru ?? 'Admin',
                        'waktu_unggah' => $item->waktu_unggah?->format('Y-m-d H:i:s'),
                        'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json($pengumuman, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single pengumuman detail
     */
    public function show($id)
    {
        try {
            $pengumuman = Pengumuman::with('guru')->find($id);

            if (!$pengumuman) {
                return response()->json([
                    'error' => 'Pengumuman tidak ditemukan'
                ], 404);
            }

            $imageUrl = null;
            if ($pengumuman->media) {
                // Check if media is already full URL or just filename
                if (str_starts_with($pengumuman->media, 'http')) {
                    $imageUrl = $pengumuman->media;
                } else {
                    $imageUrl = config('app.url') . '/storage/pengumuman/' . $pengumuman->media;
                }
            }

            return response()->json([
                'id_pengumuman' => $pengumuman->id_pengumuman,
                'judul' => $pengumuman->judul,
                'deskripsi' => $pengumuman->deskripsi,
                'media' => $imageUrl,
                'nama_guru' => $pengumuman->guru?->nama_guru ?? 'Admin',
                'waktu_unggah' => $pengumuman->waktu_unggah?->format('Y-m-d H:i:s'),
                'created_at' => $pengumuman->created_at?->format('Y-m-d H:i:s'),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
