<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AkunSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 guru
        $guruData = [
            [
                'nama_guru' => 'Ibu Ani (Super Admin)',
                'no_hp' => '08123456789',
                'email' => 'ibu.ani@sekolah.com',
                'username' => 'admin_guru',
                'is_super_admin' => true,
            ],
            [
                'nama_guru' => 'Ibu Budi',
                'no_hp' => '08234567890',
                'email' => 'ibu.budi@sekolah.com',
                'username' => 'guru1',
                'is_super_admin' => false,
            ],
            [
                'nama_guru' => 'Bapak Citra',
                'no_hp' => '08345678901',
                'email' => 'bapak.citra@sekolah.com',
                'username' => 'guru2',
                'is_super_admin' => false,
            ],
            [
                'nama_guru' => 'Ibu Dina',
                'no_hp' => '08456789012',
                'email' => 'ibu.dina@sekolah.com',
                'username' => 'guru3',
                'is_super_admin' => false,
            ],
            [
                'nama_guru' => 'Bapak Eddy',
                'no_hp' => '08567890123',
                'email' => 'bapak.eddy@sekolah.com',
                'username' => 'guru4',
                'is_super_admin' => false,
            ],
        ];

        foreach ($guruData as $data) {
            $username = $data['username'];
            $is_super_admin = $data['is_super_admin'];
            unset($data['username'], $data['is_super_admin']);

            // Create guru
            $guru = Guru::firstOrCreate([
                'email' => $data['email'],
            ], $data);

            // Create kelas for each guru
            $kelas = Kelas::firstOrCreate([
                'id_guru' => $guru->id_guru,
            ], [
                'nama_kelas' => 'Kelas ' . chr(64 + $guru->id_guru),
            ]);

            // Create guru account with is_super_admin flag
            Akun::firstOrCreate([
                'username' => $username,
            ], [
                'id_guru' => $guru->id_guru,
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'is_super_admin' => $is_super_admin,
            ]);
        }

        // Create test siswa for each guru
        $siswaCounter = 1;
        $orangtuaCounter = 1;
        
        $guruList = Guru::all();
        $siswaData = [
            ['Adi', 'Bapak Ahmad', '2021-01-15', 'L'],
            ['Bina', 'Ibu Bella', '2021-02-20', 'P'],
            ['Citra', 'Bapak Cecep', '2021-03-10', 'L'],
            ['Dini', 'Ibu Diana', '2021-04-05', 'P'],
            ['Eka', 'Bapak Edi', '2021-05-12', 'L'],
            ['Fina', 'Ibu Fitri', '2021-06-08', 'P'],
            ['Gita', 'Bapak Guruh', '2021-07-15', 'L'],
            ['Hadi', 'Ibu Hana', '2021-08-03', 'P'],
            ['Indra', 'Bapak Iwan', '2021-09-18', 'L'],
            ['Juno', 'Ibu Jane', '2021-10-25', 'P'],
        ];

        foreach ($guruList as $guru) {
            $kelas = Kelas::where('id_guru', $guru->id_guru)->first();

            // Create 2 siswa untuk setiap guru
            for ($i = 0; $i < 2; $i++) {
                $data = $siswaData[($guru->id_guru - 1) * 2 + $i];
                
                // Format NIS dengan leading zeros (001, 002, 003, etc)
                $formattedNIS = str_pad($siswaCounter, 6, '0', STR_PAD_LEFT);
                
                $siswa = Siswa::firstOrCreate([
                    'nomor_induk_siswa' => $formattedNIS,
                ], [
                    'id_kelas' => $kelas->id_kelas,
                    'nama_siswa' => $data[0],
                    'nama_orgtua' => $data[1],
                    'tgl_lahir' => $data[2],
                    'jenis_kelamin' => $data[3],
                    'alamat' => 'Jl. Siswa No. ' . $formattedNIS,
                ]);

                // Create orangtua account for each student
                Akun::firstOrCreate([
                    'username' => 'orangtua' . $orangtuaCounter,
                ], [
                    'nomor_induk_siswa' => $siswa->nomor_induk_siswa,
                    'password' => Hash::make('password123'),
                    'role' => 'orangtua',
                    'is_super_admin' => false,
                ]);

                $siswaCounter++;
                $orangtuaCounter++;
            }
        }
    }
}
