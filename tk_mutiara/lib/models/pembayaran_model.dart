class PembayaranModel {
  final String id;
  final String bulan;
  final String tahun;
  final int nominal;
  final String status; // 'lunas', 'belum', 'pending'
  final String tanggalBayar;
  final String metodePembayaran;
  final String kodeTransaksi;

  PembayaranModel({
    required this.id,
    required this.bulan,
    required this.tahun,
    required this.nominal,
    required this.status,
    this.tanggalBayar = '',
    this.metodePembayaran = '',
    this.kodeTransaksi = '',
  });

  factory PembayaranModel.fromJson(Map<String, dynamic> json) {
    return PembayaranModel(
      id: json['id'] ?? '',
      bulan: json['bulan'] ?? '',
      tahun: json['tahun'] ?? '',
      nominal: json['nominal'] ?? 0,
      status: json['status'] ?? 'belum',
      tanggalBayar: json['tanggal_bayar'] ?? '',
      metodePembayaran: json['metode_pembayaran'] ?? '',
      kodeTransaksi: json['kode_transaksi'] ?? '',
    );
  }

  String get nominalFormatted {
    final n = nominal.toString();
    final buffer = StringBuffer();
    int count = 0;
    for (int i = n.length - 1; i >= 0; i--) {
      if (count > 0 && count % 3 == 0) buffer.write('.');
      buffer.write(n[i]);
      count++;
    }
    return 'Rp ${buffer.toString().split('').reversed.join('')}';
  }

  bool get isLunas => status == 'lunas';
  bool get isPending => status == 'pending';
  bool get isBelum => status == 'belum';

  // === DUMMY DATA ===
  static List<PembayaranModel> dummyHistory() {
    return [
      PembayaranModel(
        id: '1',
        bulan: 'Maret',
        tahun: '2025',
        nominal: 350000,
        status: 'lunas',
        tanggalBayar: '3 Mar 2025',
        metodePembayaran: 'Transfer Bank',
        kodeTransaksi: 'TRX-20250303-001',
      ),
      PembayaranModel(
        id: '2',
        bulan: 'Februari',
        tahun: '2025',
        nominal: 350000,
        status: 'lunas',
        tanggalBayar: '2 Feb 2025',
        metodePembayaran: 'QRIS',
        kodeTransaksi: 'TRX-20250202-002',
      ),
      PembayaranModel(
        id: '3',
        bulan: 'Januari',
        tahun: '2025',
        nominal: 350000,
        status: 'lunas',
        tanggalBayar: '5 Jan 2025',
        metodePembayaran: 'Transfer Bank',
        kodeTransaksi: 'TRX-20250105-003',
      ),
      PembayaranModel(
        id: '4',
        bulan: 'April',
        tahun: '2025',
        nominal: 350000,
        status: 'belum',
        tanggalBayar: '',
        metodePembayaran: '',
        kodeTransaksi: '',
      ),
    ];
  }
}