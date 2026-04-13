import 'package:flutter/material.dart';
import '../theme/app_theme.dart';
import '../models/pengumuman_model.dart';

class PengumumanScreen extends StatefulWidget {
  final List<PengumumanModel> pengumuman;
  final String? selectedId;

  const PengumumanScreen({
    super.key,
    required this.pengumuman,
    this.selectedId,
  });

  @override
  State<PengumumanScreen> createState() => _PengumumanScreenState();
}

class _PengumumanScreenState extends State<PengumumanScreen> {
  PengumumanModel? _selected;
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    if (widget.selectedId != null) {
      _selected = widget.pengumuman.firstWhere(
        (p) => p.id == widget.selectedId,
        orElse: () => widget.pengumuman.first,
      );

      if (_selected != null && !_selected!.isRead) {
        _markAsRead(_selected!);
      }
    }
  }

  // ✅ Fungsi ini sudah diperbaiki
  void _markAsRead(PengumumanModel pengumuman) {
    final index = widget.pengumuman.indexWhere((p) => p.id == pengumuman.id);

    if (index != -1) {
      setState(() {
        // Menggunakan copyWith agar tidak error 'final'
        widget.pengumuman[index] = pengumuman.copyWith(isRead: true);
      });
    }
  }

  List<PengumumanModel> get _filtered {
    if (_searchQuery.isEmpty) return widget.pengumuman;

    final query = _searchQuery.toLowerCase().trim();
    return widget.pengumuman.where((p) {
      return p.judul.toLowerCase().contains(query) ||
          p.isi.toLowerCase().contains(query);
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: _selected != null ? _buildDetail(context) : _buildList(context),
      ),
    );
  }

  // ====================== LIST VIEW ======================
  Widget _buildList(BuildContext context) {
    final unreadCount = widget.pengumuman.where((p) => !p.isRead).length;

    return Column(
      children: [
        _buildHeader(unreadCount),
        _buildSearchBar(),
        Expanded(
          child: _filtered.isEmpty
              ? _buildEmptyState()
              : ListView.builder(
                  physics: const BouncingScrollPhysics(),
                  padding: const EdgeInsets.fromLTRB(20, 8, 20, 20),
                  itemCount: _filtered.length,
                  itemBuilder: (_, i) => _buildCard(_filtered[i]),
                ),
        ),
      ],
    );
  }

  Widget _buildHeader(int unreadCount) {
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 20, 16),
      color: AppTheme.white,
      child: Row(
        children: [
          IconButton(
            onPressed: () => Navigator.pop(context),
            icon: const Icon(Icons.arrow_back_ios_new_rounded,
                color: AppTheme.textDark, size: 20),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Pengumuman',
                  style: TextStyle(
                    color: AppTheme.textDark,
                    fontSize: 20,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                Text(
                  '$unreadCount pengumuman belum dibaca',
                  style: TextStyle(
                    color: unreadCount > 0 ? AppTheme.primary : AppTheme.textMedium,
                    fontSize: 13,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSearchBar() {
    return Container(
      margin: const EdgeInsets.fromLTRB(20, 10, 20, 10),
      padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: BoxDecoration(
        color: AppTheme.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: AppTheme.cardShadowList,
      ),
      child: TextField(
        controller: _searchController,
        onChanged: (value) => setState(() => _searchQuery = value),
        decoration: InputDecoration(
          hintText: 'Cari pengumuman...',
          border: InputBorder.none,
          icon: const Icon(Icons.search, color: AppTheme.textMedium),
          suffixIcon: _searchQuery.isNotEmpty
              ? IconButton(
                  icon: const Icon(Icons.clear),
                  onPressed: () {
                    _searchController.clear();
                    setState(() => _searchQuery = '');
                  },
                )
              : null,
        ),
      ),
    );
  }

  Widget _buildCard(PengumumanModel p) {
    return GestureDetector(
      onTap: () {
        setState(() {
          _selected = p;
          if (!p.isRead) {
            _markAsRead(p);
          }
        });
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        decoration: BoxDecoration(
          color: AppTheme.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: AppTheme.cardShadowList,
        ),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      p.judul,
                      style: TextStyle(
                        color: AppTheme.textDark,
                        fontSize: 15,
                        fontWeight: p.isRead ? FontWeight.w600 : FontWeight.w800,
                      ),
                    ),
                  ),
                  if (!p.isRead)
                    Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(
                        color: Colors.red,
                        shape: BoxShape.circle,
                      ),
                    ),
                ],
              ),
              const SizedBox(height: 8),
              Text(
                p.isi,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                  color: AppTheme.textMedium,
                  fontSize: 13,
                  height: 1.4,
                ),
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  Icon(Icons.access_time_rounded,
                      size: 14, color: AppTheme.textMedium),
                  const SizedBox(width: 6),
                  Text(
                    p.tanggal,
                    style: TextStyle(
                      color: AppTheme.textMedium,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

    Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: Colors.grey[100],
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.search_off_rounded,
              size: 72,
              color: Colors.grey,
            ),
          ),
          const SizedBox(height: 24),
          const Text(
            'Tidak ada pengumuman ditemukan',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: AppTheme.textDark,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Silakan coba kata kunci pencarian lain',
            style: TextStyle(
              fontSize: 14,
              color: AppTheme.textMedium,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  // ====================== DETAIL VIEW ======================
  Widget _buildDetail(BuildContext context) {
    final p = _selected!;

    return Column(
      children: [
        Container(
          padding: const EdgeInsets.fromLTRB(16, 12, 20, 16),
          color: AppTheme.white,
          child: Row(
            children: [
              IconButton(
                onPressed: () => setState(() => _selected = null),
                icon: const Icon(Icons.arrow_back_ios_new_rounded,
                    color: AppTheme.textDark),
              ),
              const SizedBox(width: 8),
              const Expanded(
                child: Text(
                  'Detail Pengumuman',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    color: AppTheme.textDark,
                  ),
                ),
              ),
            ],
          ),
        ),
        Expanded(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  p.judul,
                  style: const TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.w800,
                    height: 1.3,
                  ),
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    const Icon(Icons.access_time_rounded, size: 18),
                    const SizedBox(width: 8),
                    Text(
                      p.tanggal,
                      style: TextStyle(
                        color: AppTheme.textMedium,
                        fontSize: 14,
                      ),
                    ),
                  ],
                ),
                const Divider(height: 32),
                Text(
                  p.isi,
                  style: const TextStyle(
                    fontSize: 15,
                    height: 1.7,
                    color: AppTheme.textDark,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Map<String, dynamic> _getKategoriConfig(String kategori) {
    switch (kategori.toLowerCase()) {
      case 'penting':
        return {'color': Colors.red};
      case 'kegiatan':
        return {'color': Colors.blue};
      case 'informasi':
        return {'color': Colors.orange};
      default:
        return {'color': Colors.green};
    }
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }
}