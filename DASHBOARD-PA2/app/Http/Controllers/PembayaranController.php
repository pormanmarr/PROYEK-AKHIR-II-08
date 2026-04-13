<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with('tagihan')->get();
        return view('pembayaran.index', compact('pembayaran'));
    }

    public function create()
    {
        $tagihan = Tagihan::where('status', 'belum_bayar')->get();
        return view('pembayaran.create', compact('tagihan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_tagihan' => 'required|exists:tagihan,id_tagihan',
            'jumlah_bayar' => 'required|numeric|min:0',
            'tgl_pembayaran' => 'required|date',
            'status_bayar' => 'required|in:menunggu,diterima,ditolak',
        ]);

        Pembayaran::create($validated);
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil ditambahkan');
    }

    public function show(Pembayaran $pembayaran)
    {
        return view('pembayaran.show', compact('pembayaran'));
    }

    public function edit(Pembayaran $pembayaran)
    {
        $tagihan = Tagihan::all();
        return view('pembayaran.edit', compact('pembayaran', 'tagihan'));
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'id_tagihan' => 'required|exists:tagihan,id_tagihan',
            'jumlah_bayar' => 'required|numeric|min:0',
            'tgl_pembayaran' => 'required|date',
            'status_bayar' => 'required|in:menunggu,diterima,ditolak',
        ]);

        $pembayaran->update($validated);
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil diperbarui');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        $pembayaran->delete();
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dihapus');
    }
}
