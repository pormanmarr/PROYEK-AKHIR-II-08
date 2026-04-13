import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/pembayaran_model.dart';
import '../models/pengumuman_model.dart';
import '../models/perkembangan_model.dart';

class ApiService {
  static const String baseUrl = 'http://10.0.2.2:8081';

  // Simpan token setelah login
  static String? _token;

  static Map<String, String> get _headers => {
        'Content-Type': 'application/json',
        if (_token != null) 'Authorization': 'Bearer $_token',
      };

  // LOGIN
  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final res = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'email': email, 'password': password}),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200) {
        _token = data['token']; 
        return {'success': true, 'user': data['user']};
      } else {
        return {'success': false, 'message': data['error']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Tidak bisa terhubung ke server'};
    }
  }

  // LOGOUT
  static void logout() {
    _token = null;
  }

  // PENGUMUMAN
  static Future<List<PengumumanModel>> getPengumuman() async {
    try {
      final res = await http.get(
        Uri.parse('$baseUrl/pengumuman'),
        headers: _headers,
      );

      if (res.statusCode == 200) {
        final List data = jsonDecode(res.body);
        return data.map((e) => PengumumanModel.fromJson(e)).toList();
      } else if (res.statusCode == 401) {
        throw Exception('Sesi habis, silakan login ulang');
      }
      return [];
    } catch (e) {
      return PengumumanModel.dummyData();
    }
  }

  // PERKEMBANGAN
  static Future<List<PerkembanganModel>> getPerkembangan() async {
    try {
      final res = await http.get(
        Uri.parse('$baseUrl/perkembangan'),
        headers: _headers,
      );

      if (res.statusCode == 200) {
        final List data = jsonDecode(res.body);
        return data.map((e) => PerkembanganModel.fromJson(e)).toList();
      } else if (res.statusCode == 401) {
        throw Exception('Sesi habis, silakan login ulang');
      }
      return [];
    } catch (e) {
      return PerkembanganModel.dummyData();
    }
  }

  // PEMBAYARAN
  static Future<List<PembayaranModel>> getPembayaran() async {
    try {
      final res = await http.get(
        Uri.parse('$baseUrl/pembayaran'),
        headers: _headers,
      );

      if (res.statusCode == 200) {
        final List data = jsonDecode(res.body);
        return data.map((e) => PembayaranModel.fromJson(e)).toList();
      } else if (res.statusCode == 401) {
        throw Exception('Sesi habis, silakan login ulang');
      }
      return [];
    } catch (e) {
      return PembayaranModel.dummyHistory();
    }
  }

  // BAYAR SPP
  static Future<Map<String, dynamic>> bayarSPP(String id, String metode) async {
    try {
      final res = await http.post(
        Uri.parse('$baseUrl/pembayaran/bayar'),
        headers: _headers,
        body: jsonEncode({'id': id, 'metode': metode}),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200) {
        return {'success': true, 'kode_transaksi': data['kode_transaksi']};
      } else {
        return {'success': false, 'message': data['error']};
      }
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server'};
    }
  }
}