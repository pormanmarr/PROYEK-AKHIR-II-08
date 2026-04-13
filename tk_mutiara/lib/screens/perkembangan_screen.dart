import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/perkembangan_model.dart';

class PerkembanganScreen extends StatefulWidget {
  const PerkembanganScreen({super.key});

  @override
  State<PerkembanganScreen> createState() => _PerkembanganScreenState();
}

class _PerkembanganScreenState extends State<PerkembanganScreen>
    with SingleTickerProviderStateMixin {
  final List<PerkembanganModel> _data = PerkembanganModel.dummyData();
  late TabController _tabController;
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: _data.length, vsync: this);
    _tabController.addListener(() {
      setState(() => _selectedIndex = _tabController.index);
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: Column(
          children: [
            _buildHeader(context),
            _buildTabBar(),
            Expanded(
              child: TabBarView(
                controller: _tabController,
                children: _data.map((d) => _buildContent(d)).toList(),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 20, 16),
      decoration: BoxDecoration(
        color: AppTheme.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          IconButton(
            onPressed: () => Navigator.pop(context),
            icon: const Icon(
              Icons.arrow_back_ios_new_rounded,
              color: AppTheme.textDark,
              size: 20,
            ),
          ),
          const SizedBox(width: 4),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Perkembangan Anak',
                style: TextStyle(
                  color: AppTheme.textDark,
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                ),
              ),
              Text(
                'Bintang Mutiara · Kelas A',
                style: TextStyle(
                  color: AppTheme.primary,
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
          const Spacer(),
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: const Color(0xFFFFEDE0),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              Icons.child_care_rounded,
              color: AppTheme.primary,
              size: 22,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTabBar() {
    return Container(
      height: 48,
      color: AppTheme.white,
      child: TabBar(
        controller: _tabController,
        indicatorColor: AppTheme.primary,
        indicatorWeight: 3,
        indicatorSize: TabBarIndicatorSize.label,
        labelColor: AppTheme.primary,
        unselectedLabelColor: AppTheme.textMedium,
        labelStyle: const TextStyle(
          fontWeight: FontWeight.w700,
          fontSize: 13,
          fontFamily: 'Nunito',
        ),
        unselectedLabelStyle: const TextStyle(
          fontWeight: FontWeight.w500,
          fontSize: 13,
          fontFamily: 'Nunito',
        ),
        tabs: _data.map((d) => Tab(text: d.tanggal)).toList(),
      ),
    );
  }

  Widget _buildContent(PerkembanganModel data) {
    return SingleChildScrollView(
      physics: const BouncingScrollPhysics(),
      padding: const EdgeInsets.all(20),
      child: Column(
        children: [
          _buildSummaryCard(data),
          const SizedBox(height: 20),
          _buildRadarChart(data),
          const SizedBox(height: 20),
          _buildAspekList(data),
          const SizedBox(height: 20),
          _buildCatatanCard(data),
          const SizedBox(height: 20),
        ],
      ),
    );
  }

  Widget _buildSummaryCard(PerkembanganModel data) {
    final rata =
        (data.nilaiKognitif +
            data.nilaiMotorik +
            data.nilaiSosial +
            data.nilaiBahasa +
            data.nilaiSeni) /
        5;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: AppTheme.primaryGradient,
        borderRadius: BorderRadius.circular(24),
        boxShadow: AppTheme.softShadow,
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Nilai Rata-Rata',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  '${rata.toStringAsFixed(1)}',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 42,
                    fontWeight: FontWeight.w900,
                    height: 1,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  data.deskripsi,
                  style: const TextStyle(
                    color: Colors.white70,
                    fontSize: 12,
                    fontWeight: FontWeight.w500,
                  ),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
            ),
          ),
          const SizedBox(width: 16),
          _buildCircularScore(rata),
        ],
      ),
    );
  }

  Widget _buildCircularScore(double value) {
    return SizedBox(
      width: 80,
      height: 80,
      child: Stack(
        alignment: Alignment.center,
        children: [
          SizedBox(
            width: 80,
            height: 80,
            child: CircularProgressIndicator(
              value: value / 100,
              backgroundColor: Colors.white.withOpacity(0.3),
              valueColor: const AlwaysStoppedAnimation<Color>(Colors.white),
              strokeWidth: 6,
              strokeCap: StrokeCap.round,
            ),
          ),
          Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _getGrade(value),
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const Text(
                'Grade',
                style: TextStyle(
                  color: Colors.white70,
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  String _getGrade(double v) {
    if (v >= 90) return 'A+';
    if (v >= 85) return 'A';
    if (v >= 80) return 'B+';
    if (v >= 75) return 'B';
    return 'C+';
  }

  Widget _buildRadarChart(PerkembanganModel data) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Grafik Perkembangan',
            style: TextStyle(
              color: AppTheme.textDark,
              fontSize: 15,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 20),
          CustomPaint(
            size: const Size(double.infinity, 200),
            painter: _RadarPainter(data: data),
          ),
          const SizedBox(height: 16),
          _buildLegend(),
        ],
      ),
    );
  }

  Widget _buildLegend() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        _legendItem(AppTheme.primary, 'Nilai Anak'),
        const SizedBox(width: 20),
        _legendItem(AppTheme.primary.withOpacity(0.25), 'Target'),
      ],
    );
  }

  Widget _legendItem(Color color, String label) {
    return Row(
      children: [
        Container(
          width: 12,
          height: 12,
          decoration: BoxDecoration(color: color, shape: BoxShape.circle),
        ),
        const SizedBox(width: 6),
        Text(
          label,
          style: const TextStyle(
            color: AppTheme.textMedium,
            fontSize: 12,
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  Widget _buildAspekList(PerkembanganModel data) {
    final aspek = [
      {
        'label': 'Kognitif',
        'nilai': data.nilaiKognitif,
        'icon': Icons.psychology_rounded,
        'color': const Color(0xFF6366F1),
      },
      {
        'label': 'Motorik',
        'nilai': data.nilaiMotorik,
        'icon': Icons.directions_run_rounded,
        'color': const Color(0xFF22C55E),
      },
      {
        'label': 'Sosial',
        'nilai': data.nilaiSosial,
        'icon': Icons.people_rounded,
        'color': const Color(0xFFF59E0B),
      },
      {
        'label': 'Bahasa',
        'nilai': data.nilaiBahasa,
        'icon': Icons.record_voice_over_rounded,
        'color': const Color(0xFFFF6B1A),
      },
      {
        'label': 'Seni',
        'nilai': data.nilaiSeni,
        'icon': Icons.palette_rounded,
        'color': const Color(0xFFEC4899),
      },
    ];

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Detail Penilaian',
            style: TextStyle(
              color: AppTheme.textDark,
              fontSize: 15,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 16),
          ...aspek.map(
            (a) => _buildAspekItem(
              a['label'] as String,
              a['nilai'] as double,
              a['icon'] as IconData,
              a['color'] as Color,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAspekItem(
    String label,
    double nilai,
    IconData icon,
    Color color,
  ) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Column(
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.12),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(icon, color: color, size: 16),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Text(
                  label,
                  style: const TextStyle(
                    color: AppTheme.textDark,
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
              Text(
                '${nilai.toInt()}',
                style: TextStyle(
                  color: color,
                  fontSize: 14,
                  fontWeight: FontWeight.w800,
                ),
              ),
              const SizedBox(width: 4),
              Text(
                '/ 100',
                style: const TextStyle(
                  color: AppTheme.textLight,
                  fontSize: 11,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: TweenAnimationBuilder<double>(
              tween: Tween(begin: 0, end: nilai / 100),
              duration: const Duration(milliseconds: 1000),
              curve: Curves.easeOutCubic,
              builder: (_, v, __) => LinearProgressIndicator(
                value: v,
                backgroundColor: color.withOpacity(0.12),
                valueColor: AlwaysStoppedAnimation<Color>(color),
                minHeight: 8,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCatatanCard(PerkembanganModel data) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFFFFEDE0),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
          color: AppTheme.primary.withOpacity(0.2),
          width: 1.5,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(
                Icons.sticky_note_2_rounded,
                color: AppTheme.primary,
                size: 20,
              ),
              const SizedBox(width: 8),
              Text(
                'Catatan Guru',
                style: TextStyle(
                  color: AppTheme.primary,
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            data.catatan,
            style: const TextStyle(
              color: AppTheme.textDark,
              fontSize: 13,
              fontWeight: FontWeight.w500,
              height: 1.6,
            ),
          ),
        ],
      ),
    );
  }
}

class _RadarPainter extends CustomPainter {
  final PerkembanganModel data;

  _RadarPainter({required this.data});

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = size.height / 2 - 20;

    final labels = ['Kognitif', 'Motorik', 'Sosial', 'Bahasa', 'Seni'];
    final values = [
      data.nilaiKognitif / 100,
      data.nilaiMotorik / 100,
      data.nilaiSosial / 100,
      data.nilaiBahasa / 100,
      data.nilaiSeni / 100,
    ];

    final n = labels.length;
    final angleStep = (2 * 3.14159265) / n;

    // Draw grid
    final gridPaint = Paint()
      ..color = AppTheme.primary.withOpacity(0.1)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1;

    for (int level = 1; level <= 4; level++) {
      final r = radius * level / 4;
      final path = Path();
      for (int i = 0; i < n; i++) {
        final angle = -3.14159265 / 2 + i * angleStep;
        final x = center.dx + r * _cos(angle);
        final y = center.dy + r * _sin(angle);
        if (i == 0) {
          path.moveTo(x, y);
        } else {
          path.lineTo(x, y);
        }
      }
      path.close();
      canvas.drawPath(path, gridPaint);
    }

    // Draw spokes
    final spokePaint = Paint()
      ..color = AppTheme.primary.withOpacity(0.15)
      ..strokeWidth = 1;
    for (int i = 0; i < n; i++) {
      final angle = -3.14159265 / 2 + i * angleStep;
      canvas.drawLine(
        center,
        Offset(
          center.dx + radius * _cos(angle),
          center.dy + radius * _sin(angle),
        ),
        spokePaint,
      );
    }

    // Draw data area
    final dataPath = Path();
    for (int i = 0; i < n; i++) {
      final angle = -3.14159265 / 2 + i * angleStep;
      final r = radius * values[i];
      final x = center.dx + r * _cos(angle);
      final y = center.dy + r * _sin(angle);
      if (i == 0) {
        dataPath.moveTo(x, y);
      } else {
        dataPath.lineTo(x, y);
      }
    }
    dataPath.close();

    canvas.drawPath(
      dataPath,
      Paint()
        ..color = AppTheme.primary.withOpacity(0.2)
        ..style = PaintingStyle.fill,
    );
    canvas.drawPath(
      dataPath,
      Paint()
        ..color = AppTheme.primary
        ..style = PaintingStyle.stroke
        ..strokeWidth = 2.5,
    );

    // Draw labels
    final textStyle = const TextStyle(
      color: AppTheme.textDark,
      fontSize: 11,
      fontWeight: FontWeight.w700,
    );

    for (int i = 0; i < n; i++) {
      final angle = -3.14159265 / 2 + i * angleStep;
      final labelR = radius + 18;
      final x = center.dx + labelR * _cos(angle);
      final y = center.dy + labelR * _sin(angle);

      final tp = TextPainter(
        text: TextSpan(text: labels[i], style: textStyle),
        textDirection: TextDirection.ltr,
      )..layout();
      tp.paint(canvas, Offset(x - tp.width / 2, y - tp.height / 2));
    }

    // Draw dots on data points
    for (int i = 0; i < n; i++) {
      final angle = -3.14159265 / 2 + i * angleStep;
      final r = radius * values[i];
      final x = center.dx + r * _cos(angle);
      final y = center.dy + r * _sin(angle);
      canvas.drawCircle(Offset(x, y), 5, Paint()..color = AppTheme.white);
      canvas.drawCircle(Offset(x, y), 4, Paint()..color = AppTheme.primary);
    }
  }

  double _cos(double angle) => _math_cos(angle);
  double _sin(double angle) => _math_sin(angle);

  double _math_cos(double x) {
    double result = 1;
    double term = 1;
    for (int i = 1; i <= 10; i++) {
      term *= -x * x / (2 * i * (2 * i - 1));
      result += term;
    }
    return result;
  }

  double _math_sin(double x) {
    double result = x;
    double term = x;
    for (int i = 1; i <= 10; i++) {
      term *= -x * x / ((2 * i) * (2 * i + 1));
      result += term;
    }
    return result;
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;
}
