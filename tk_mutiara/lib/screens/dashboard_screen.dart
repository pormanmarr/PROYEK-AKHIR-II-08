import 'package:flutter/material.dart';
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';
import '../services/api_services.dart';
import 'perkembangan_screen.dart';
import 'pembayaran_screen.dart';
import 'pengumuman_screen.dart';
import 'history_screen.dart';
import 'login_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  String _namaAnak = 'Bintang';

  List<PembayaranModel> _payments = [];
  List<PengumumanModel> _pengumuman = [];
  List<PerkembanganModel> _perkembanganData = [];

  PembayaranModel? get _tagihan {
    for (final p in _payments) {
      if (p.isBelum) return p;
    }
    return null;
  }

  @override
  void initState() {
    super.initState();

    final user = ApiService.userInfo;
    if (user != null) {
      _namaAnak = user['nama_siswa'].toString();
    }

    _loadData();
  }

  void _loadData() async {
    final p = await ApiService.getPembayaran();
    final pg = await ApiService.getPengumuman();
    final perkembangan = await ApiService.getPerkembangan();

    setState(() {
      _payments = p;
      _pengumuman = pg.take(2).toList();
       _perkembanganData = perkembangan;
    });
  }

  String getStatus(String kategori) {
  try {
    final item = _perkembanganData.firstWhere(
      (e) => e.kategori.toLowerCase().contains(kategori.toLowerCase()),
    );

    switch (item.statusUtama) {
      case "BSH":
        return "Baik";
      case "MB":
        return "Mulai";
      case "BB":
        return "Kurang";
      default:
        return item.statusUtama;
    }
  } catch (e) {
    return "-";
  }
}

  @override
Widget build(BuildContext context) {
  return Scaffold(
   backgroundColor: const Color(0xFFFAF3ED),
    body: SafeArea(
      child: SingleChildScrollView(
        child: Column(
          children: [
            _header(),
            const SizedBox(height: 20),
            _menuCards(),
            const SizedBox(height: 20),
            _perkembangan(),
            const SizedBox(height: 20),
            _pengumumanUI(),
            const SizedBox(height: 20),
          ],
        ),
      ),
    ),
  );
}

  Widget _header() {
  return Container(
    height: 160, 
    padding: const EdgeInsets.symmetric(horizontal: 20),
    decoration: const BoxDecoration(
      color: Color(0xFFE57A32),
      borderRadius: BorderRadius.only(
        bottomLeft: Radius.circular(24),
        bottomRight: Radius.circular(24),
      ),
    ),
    child: Center(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // FOTO PROFIL
          const CircleAvatar(
            radius: 28, 
            backgroundColor: Colors.white,
            child: Icon(Icons.person, color: Colors.grey, size: 28),
          ),

          const SizedBox(width: 14),

          // TEXT
          Expanded(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  "Halo, Bunda 👋",
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                  ),
                ),

                const SizedBox(height: 1),

                // NAMA + DROPDOWN (CLICKABLE)
                InkWell(
                  borderRadius: BorderRadius.circular(8),
                  onTap: _showSwitchAccount,
                  child: Row(
                    children: [
                      Flexible(
                        child: Text(
                          _namaAnak,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 22,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                      ),
                      const SizedBox(width: 4),
                      const Icon(
                        Icons.keyboard_arrow_down,
                        color: Colors.white,
                        size: 22,
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 5),

                // KELAS
                Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                  ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                    children: const [
                      Icon(
                        Icons.school,
                        size: 14,
                        color: Color(0xFFE57A32),
              ),
                SizedBox(width: 5),
                Text(
                  "Kelas Tulip - Bu Wina",
                style: TextStyle(
                    color: Color(0xFFE58A45),
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                ),
              ),
             ],
          ),
            ),
              ],
            ),
          ),

          const SizedBox(width: 1),
 
          // NOTIFIKASI
          Padding(
            padding: const EdgeInsets.only(right: 13),
            child: Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.25),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.notifications_none,
                color: Colors.white,
                size: 22,
              ),
            ),
          ),
        ],
      ),
    ),
  );
}

void _showSwitchAccount() {
  showModalBottomSheet(
    context: context,
    backgroundColor: Colors.transparent,
    builder: (context) {
      return Container(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 20),
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            //HEADER 
            Stack(
              alignment: Alignment.center,
              children: [
                const Center(
                  child: Text(
                    "Pilih Anak",
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
                Positioned(
                  right: 0,
                  child: IconButton(
                    onPressed: () => Navigator.pop(context),
                    icon: const Icon(Icons.close),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 6),

            const Text(
              "Pilih akun anak untuk melihat informasi dan aktivitasnya",
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 12, color: Colors.grey),
            ),

            const SizedBox(height: 18),

            // AKUN AKTIF
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFFF3E8),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: const Color(0xFFE57A32)),
              ),
              child: Row(
                children: [
                  const CircleAvatar(
                    radius: 22,
                    backgroundColor: Color(0xFFEFEFEF),
                    child: Icon(Icons.person, color: Colors.grey),
                  ),

                  const SizedBox(width: 12),

                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          _namaAnak,
                          style: const TextStyle(
                            fontWeight: FontWeight.w600,
                            fontSize: 14,
                          ),
                        ),
                        const SizedBox(height: 3),
                        const Row(
                          children: [
                            Icon(Icons.school,
                                size: 14, color: Color(0xFFE57A32)),
                            SizedBox(width: 4),
                            Text(
                              "Kelas Tulip - Bu Wina",
                              style: TextStyle(
                                fontSize: 11,
                                color: Color(0xFFE57A32),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const Icon(
                    Icons.check_circle,
                    color: Color(0xFFE57A32),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 16),

            // TAMBAH AKUN
            GestureDetector(
              onTap: () {
                Navigator.pop(context);

                // halaman login
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => const LoginScreen(), 
                  ),
                );
              },
              child: Container(
                padding: const EdgeInsets.symmetric(vertical: 14),
                decoration: BoxDecoration(
                  color: const Color(0xFFF9F9F9),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Colors.grey.shade300),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: const [
                    Icon(Icons.add, size: 18, color: Color(0xFFE57A32)),
                    SizedBox(width: 6),
                    Text(
                      "Tambah Akun Anak Baru",
                      style: TextStyle(
                        color: Color(0xFFE57A32),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ),

            const SizedBox(height: 16),

            // INFO BOX
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFF5F5F5),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                children: const [
                  Icon(Icons.verified_user, size: 18),
                  SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      "Data setiap anak terpisah dan aman. Pastikan informasi yang dimasukkan benar.",
                      style: TextStyle(fontSize: 11),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      );
    },
  );
}

  Widget _menuCards() {
  final bool lunas = _tagihan == null;

  return Padding(
    padding: const EdgeInsets.symmetric(horizontal: 16),
    child: Row(
      children: [
        Expanded(
          child: _cardMenu(
            title: "Bayar SPP",
            subtitle: "Pembayaran SPP bulanan mudah dan cepat",
            icon: Icons.account_balance_wallet_outlined,
            badge: lunas ? "Lunas" : "Belum",
            badgeColor: lunas ? Colors.green : Colors.red,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => PembayaranScreen(tagihan: _tagihan),
                ),
              );
            },
          ),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: _cardMenu(
            title: "Riwayat Pembayaran",
            subtitle: "Lihat riwayat pembayaran",
            icon: Icons.receipt_long,
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => const HistoryScreen(),
                ),
              );
            },
          ),
        ),
      ],
    ),
  );
}

  Widget _cardMenu({
    required String title,
    required String subtitle,
    required IconData icon,
    String? badge,
    Color? badgeColor,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        height: 180,
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(
              color: Colors.grey.withOpacity(0.15),
              blurRadius: 8,
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                CircleAvatar(
                  radius: 28,
                  backgroundColor: const Color(0xFFFCEBDC),
                  child: Icon(icon, size: 28,color: const Color(0xFFE98943)),
                ),
                if (badge != null)
                Padding( padding: const EdgeInsets.only(bottom: 35),
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: badgeColor,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      badge,
                      style: const TextStyle(
                          color: Colors.white, fontSize: 12),
                    ),
                  ),
                ),
          
              ],
            ),
            const Spacer(),
            Text(title,
                style:
                    const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
            const SizedBox(height: 6),
            Text(
              subtitle,
              style: const TextStyle(color: Colors.grey, fontSize: 13),
            )
          ],
        ),
      ),
    );
  }

Widget _perkembangan() {
  return GestureDetector(
    onTap: () {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => const PerkembanganScreen(),
        ),
      );
    },
    child: Container(
      margin: const EdgeInsets.symmetric(horizontal: 16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: const BoxDecoration(
                      color: Color(0xFFFCEBDC),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.show_chart,
                      color: Color(0xFFE58A45),
                      size: 28,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Text(
                    "Lihat Perkembangan",
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                    ),
                  ),
                ],
              ),
              const Text(
                "Lihat detail",
                style: TextStyle(
                  color: Color(0xFFE58A45),
                  fontSize: 12,
                ),
              )
            ],
          ),
          const SizedBox(height: 14),
         Row(
  mainAxisAlignment: MainAxisAlignment.spaceAround,
  children: [
    _item(Icons.directions_run, "Motorik", getStatus("Motorik")),
    _item(Icons.people, "Sosial", getStatus("Sosial")),
    _item(Icons.psychology, "Kognitif", getStatus("Kognitif")),
  ],
),
        ],
      ),
    ),
  );
}

Widget _item(IconData icon, String title, String status) {
  return Column(
    children: [
      CircleAvatar(
        backgroundColor: Colors.white,
        child: Icon(icon, color: Colors.orange),
      ),
      const SizedBox(height: 6),
      Text(
        title,
        style: const TextStyle(
          fontWeight: FontWeight.bold,
        ),
      ),
      Text(
        status,
        style: const TextStyle(
          color: Colors.green,
          fontSize: 12,
        ),
      ),
    ],
  );
}

 Widget _pengumumanUI() {
  return Container(
    margin: const EdgeInsets.symmetric(horizontal: 16),
    padding: const EdgeInsets.all(16),
    decoration: BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(20),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // HEADER
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: const BoxDecoration(
                    color: Color(0xFFFCEBDC),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.campaign,
                    color: Color(0xFFE58A45),
                    size: 25,
                  ),
                ),
                const SizedBox(width: 8),
                const Text(
                  "Pengumuman",
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 18,
                  ),
                ),
              ],
            ),
            GestureDetector(
              onTap: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => const PengumumanScreen(),
                  ),
                );
              },
              child: const Text(
                "Lihat semua",
                style: TextStyle(
                  color: Color(0xFFE58A45),
                  fontSize: 12,
                ),
              ),
            )
          ],
        ),

        const SizedBox(height: 12),

        // LIST
        ..._pengumuman.map(
          (e) => Column(
            children: [
              GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => const PengumumanScreen(),
                    ),
                  );
                },
                child: ListTile(
                  contentPadding: EdgeInsets.zero,

                  // DOT
                  leading: const Icon(
                    Icons.circle,
                    size: 10,
                    color: Colors.orange,
                  ),

                  // JUDUL
                  title: Text(
                    e.judul,
                    style: const TextStyle(
                      fontWeight: FontWeight.w600,
                      fontSize: 14,
                    ),
                  ),

                
                  subtitle: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        e.waktuUnggah,
                        style: const TextStyle(fontSize: 11),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        e.deskripsi, // 
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 12,
                          color: Colors.grey,
                        ),
                      ),
                    ],
                  ),

                  // BADGE
                  trailing: Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.orange.shade100,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Text(
                      "Baru",
                      style: TextStyle(fontSize: 11),
                    ),
                  ),
                ),
              ),

             
            ],
          ),
        ),
      ],
    ),
  );
}
}