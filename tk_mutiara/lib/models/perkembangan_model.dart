class PerkembanganModel {
  final String id;
  final String namaAnak;
  final String tanggal;
  final String kategori;
  final String deskripsi;
  final double nilaiKognitif;
  final double nilaiMotorik;
  final double nilaiSosial;
  final double nilaiBahasa;
  final double nilaiSeni;
  final String catatan;
  final String fotoUrl;

  PerkembanganModel({
    required this.id,
    required this.namaAnak,
    required this.tanggal,
    required this.kategori,
    required this.deskripsi,
    required this.nilaiKognitif,
    required this.nilaiMotorik,
    required this.nilaiSosial,
    required this.nilaiBahasa,
    required this.nilaiSeni,
    required this.catatan,
    this.fotoUrl = '',
  });

  factory PerkembanganModel.fromJson(Map<String, dynamic> json) {
    return PerkembanganModel(
      id: json['id'] ?? '',
      namaAnak: json['nama_anak'] ?? '',
      tanggal: json['tanggal'] ?? '',
      kategori: json['kategori'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
      nilaiKognitif: (json['nilai_kognitif'] ?? 0).toDouble(),
      nilaiMotorik: (json['nilai_motorik'] ?? 0).toDouble(),
      nilaiSosial: (json['nilai_sosial'] ?? 0).toDouble(),
      nilaiBahasa: (json['nilai_bahasa'] ?? 0).toDouble(),
      nilaiSeni: (json['nilai_seni'] ?? 0).toDouble(),
      catatan: json['catatan'] ?? '',
      fotoUrl: json['foto_url'] ?? '',
    );
  }

  // === DUMMY DATA ===
  static List<PerkembanganModel> dummyData() {
    return [
      PerkembanganModel(
        id: '1',
        namaAnak: 'Bintang Mutiara',
        tanggal: 'Maret 2025',
        kategori: 'Perkembangan Bulanan',
        deskripsi: 'Bintang menunjukkan perkembangan yang sangat baik bulan ini.',
        nilaiKognitif: 85,
        nilaiMotorik: 90,
        nilaiSosial: 80,
        nilaiBahasa: 88,
        nilaiSeni: 92,
        catatan:
            'Bintang sangat aktif dalam kegiatan kelas. Ia menunjukkan kemampuan mengenal angka dan huruf dengan baik, serta sudah bisa menulis namanya sendiri.',
      ),
      PerkembanganModel(
        id: '2',
        namaAnak: 'Bintang Mutiara',
        tanggal: 'Februari 2025',
        kategori: 'Perkembangan Bulanan',
        deskripsi: 'Perkembangan Bintang di bulan Februari stabil dan memuaskan.',
        nilaiKognitif: 80,
        nilaiMotorik: 85,
        nilaiSosial: 78,
        nilaiBahasa: 82,
        nilaiSeni: 88,
        catatan:
            'Bintang mulai menunjukkan ketertarikan pada seni menggambar. Kemampuan bersosialisasi dengan teman sebaya juga semakin meningkat.',
      ),
    ];
  }
}