// lib/models/pengumuman_model.dart

class PengumumanModel {
  final String id;
  final String judul;
  final String isi;
  final String tanggal;
  final String kategori;
  final bool isRead;

  const PengumumanModel({
    required this.id,
    required this.judul,
    required this.isi,
    required this.tanggal,
    required this.kategori,
    this.isRead = false,          // default false
  });

  // Factory untuk parsing dari API
  factory PengumumanModel.fromJson(Map<String, dynamic> json) {
    return PengumumanModel(
      id: json['id']?.toString() ?? '',
      judul: json['judul'] ?? 'Tidak ada judul',
      isi: json['isi'] ?? '',
      tanggal: json['tanggal'] ?? json['created_at'] ?? 'Tanggal tidak diketahui',
      kategori: json['kategori']?.toString().toLowerCase() ?? 'informasi',
      isRead: json['is_read'] ?? false,   // kalau backend kirim field ini
    );
  }

  // Method copyWith (WAJIB untuk mengubah isRead)
  PengumumanModel copyWith({
    String? id,
    String? judul,
    String? isi,
    String? tanggal,
    String? kategori,
    bool? isRead,
  }) {
    return PengumumanModel(
      id: id ?? this.id,
      judul: judul ?? this.judul,
      isi: isi ?? this.isi,
      tanggal: tanggal ?? this.tanggal,
      kategori: kategori ?? this.kategori,
      isRead: isRead ?? this.isRead,
    );
  }

// dummy data
  static List<PengumumanModel> dummyData() {
  return [
    PengumumanModel(
      id: '1',
      judul: 'Pengumuman Libur Semester',
      isi: 'Libur semester akan dimulai tanggal 10 April 2026. Anak-anak diharapkan kembali masuk pada tanggal 20 April 2026.',
      tanggal: '30 Maret 2026',
      kategori: 'penting',
    ),
    PengumumanModel(
      id: '2',
      judul: 'Jadwal Kegiatan Olahraga',
      isi: 'Lomba olahraga antar kelas akan dilaksanakan minggu depan. Mohon orang tua menyiapkan perlengkapan anak.',
      tanggal: '29 Maret 2026',
      kategori: 'kegiatan',
    ),
    PengumumanModel(
      id: '3',
      judul: 'Pembayaran SPP Bulan April',
      isi: 'Pembayaran SPP bulan April sudah dapat dilakukan mulai tanggal 1 April 2026.',
      tanggal: '28 Maret 2026',
      kategori: 'info',
    ),
    PengumumanModel(
      id: '4',
      judul: 'Kegiatan Field Trip',
      isi: 'Anak-anak akan mengikuti kegiatan field trip ke kebun binatang pada tanggal 15 April 2026.',
      tanggal: '27 Maret 2026',
      kategori: 'kegiatan',
    ),
    PengumumanModel(
      id: '5',
      judul: 'Penting: Perubahan Jam Masuk',
      isi: 'Mulai minggu depan, jam masuk sekolah menjadi pukul 07.30 WIB.',
      tanggal: '26 Maret 2026',
      kategori: 'penting',
    ),
    PengumumanModel(
      id: '6',
      judul: 'Info Seragam Sekolah',
      isi: 'Mohon memastikan anak menggunakan seragam sesuai jadwal yang telah ditentukan.',
      tanggal: '25 Maret 2026',
      kategori: 'info',
    ),
    PengumumanModel(
      id: '7',
      judul: 'Kegiatan Pentas Seni',
      isi: 'Pentas seni akan dilaksanakan pada akhir bulan. Orang tua diundang untuk hadir.',
      tanggal: '24 Maret 2026',
      kategori: 'kegiatan',
    ),
    PengumumanModel(
      id: '8',
      judul: 'Penting: Kesehatan Anak',
      isi: 'Jika anak sedang sakit, diharapkan untuk tidak masuk sekolah demi menjaga kesehatan bersama.',
      tanggal: '23 Maret 2026',
      kategori: 'penting',
    ),
  ];
}
}