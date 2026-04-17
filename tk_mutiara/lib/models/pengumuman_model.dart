// lib/models/pengumuman_model.dart

class PengumumanModel {
  final int idPengumuman;
  final int idGuru;
  final String namaGuru;
  final String judul;
  final String media;
  final String waktuUnggah;
  final String deskripsi;
  final String createdAt;
  final String updatedAt;

  const PengumumanModel({
    required this.idPengumuman,
    required this.idGuru,
    required this.namaGuru,
    required this.judul,
    required this.media,
    required this.waktuUnggah,
    required this.deskripsi,
    required this.createdAt,
    required this.updatedAt,
  });

  // Factory untuk parsing dari API
  factory PengumumanModel.fromJson(Map<String, dynamic> json) {
    return PengumumanModel(
      idPengumuman: json['id_pengumuman'] ?? 0,
      idGuru: json['id_guru'] ?? 0,
      namaGuru: json['nama_guru'] ?? 'Admin',
      judul: json['judul'] ?? 'Tidak ada judul',
      media: json['media'] ?? '',
      waktuUnggah: json['waktu_unggah'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
    );
  }

  // Method copyWith untuk immutability
  PengumumanModel copyWith({
    int? idPengumuman,
    int? idGuru,
    String? namaGuru,
    String? judul,
    String? media,
    String? waktuUnggah,
    String? deskripsi,
    String? createdAt,
    String? updatedAt,
  }) {
    return PengumumanModel(
      idPengumuman: idPengumuman ?? this.idPengumuman,
      idGuru: idGuru ?? this.idGuru,
      namaGuru: namaGuru ?? this.namaGuru,
      judul: judul ?? this.judul,
      media: media ?? this.media,
      waktuUnggah: waktuUnggah ?? this.waktuUnggah,
      deskripsi: deskripsi ?? this.deskripsi,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }

  // Dummy data untuk testing
  static List<PengumumanModel> dummyData() {
    return [
      PengumumanModel(
        idPengumuman: 1,
        idGuru: 1,
        namaGuru: 'Ibu Ani',
        judul: 'Pengumuman Libur Sekolah',
        media: '',
        waktuUnggah: '2026-04-15 10:30:00',
        deskripsi: 'Libur sekolah akan diadakan dari tanggal 20-25 April 2026',
        createdAt: '2026-04-15 10:30:00',
        updatedAt: '2026-04-15 10:30:00',
      ),
    ];
  }
}