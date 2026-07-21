<?php
namespace Database\Seeders;
use App\Models\{User,SanggarProfile,Pelatih,Pengelola,JadwalLatihan,Event,Tarian};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::updateOrCreate(['email'=>'admin@sanggarmulya.id'],[
            'name'=>'Admin Sanggar','alamat'=>'Indramayu, Jawa Barat',
            'password'=>Hash::make('admin123'),'role'=>'admin','status'=>'aktif']);
        foreach([
            ['name'=>'Siti Rahayu','email'=>'siti@example.com','alamat'=>'Jl. Melati No. 12, Indramayu','no_hp'=>'081234567890'],
            ['name'=>'Budi Santoso','email'=>'budi@example.com','alamat'=>'Jl. Mawar No. 5, Cirebon','no_hp'=>'082345678901'],
        ] as $d){User::updateOrCreate(['email'=>$d['email']],array_merge($d,['password'=>Hash::make('password123'),'role'=>'anggota','status'=>'aktif']));}

        SanggarProfile::updateOrCreate(['id'=>1],[
            'nama_sanggar'=>'Sanggar Mulya Bhakti','tagline'=>'Melestarikan Budaya Melalui Seni',
            'sejarah'=>'Sanggar Mulya Bhakti lahir dari kecintaan mendalam terhadap seni tari tradisional Indramayu. Bermula dari sekelompok kecil penari muda yang berkumpul di sebuah halaman rumah pada tahun 2005, sanggar ini tumbuh menjadi salah satu pusat pelestarian budaya terkemuka di Jawa Barat.',
            'visi'=>'Menjadi sanggar seni tari tradisional terdepan di Indonesia yang mampu melestarikan dan memperkenalkan budaya Indramayu kepada dunia.',
            'misi'=>['Melestarikan seni tari tradisional Indramayu melalui pendidikan berkelanjutan.','Mengembangkan bakat generasi muda dalam bidang seni tari.','Memperkenalkan kekayaan budaya lokal ke tingkat nasional dan internasional.','Membangun komunitas pecinta seni yang solid.','Berkolaborasi dengan lembaga pendidikan dan pemerintah.'],
            'tahun_berdiri'=>'2005','alamat'=>'Jl. Raya Jatibarang-Karangampel, km. 3, Desa Tambi, Kec. Sliyeg, Indramayu Jawa Barat',
            'no_hp'=>'+62 812 3456 7890','email'=>'info@sanggarmulya.id',
            'instagram'=>'@sanggarmulya_bhakti','jumlah_anggota'=>200,'jumlah_penghargaan'=>49,'jumlah_event'=>100,
        ]);

        foreach([
            ['nama'=>'Ibu Sari Wulandari','jabatan'=>'Pelatih Utama','spesialisasi'=>'Tari Topeng Cirebon','pengalaman'=>'20+ Tahun','urutan'=>1],
            ['nama'=>'Bpk. Hendra Kusuma','jabatan'=>'Pelatih Senior','spesialisasi'=>'Tari Ronggeng Bugis','pengalaman'=>'15+ Tahun','urutan'=>2],
            ['nama'=>'Ibu Dewi Rahayu','jabatan'=>'Pelatih Tari Kreasi','spesialisasi'=>'Tari Merak & Kontemporer','pengalaman'=>'12+ Tahun','urutan'=>3],
            ['nama'=>'Bpk. Agus Santoso','jabatan'=>'Pelatih Musik Pengiring','spesialisasi'=>'Gamelan & Rebana','pengalaman'=>'18+ Tahun','urutan'=>4],
        ] as $p){Pelatih::firstOrCreate(['nama'=>$p['nama']],array_merge($p,['aktif'=>true]));}

        foreach([
            ['nama'=>'H. Mulyadi, S.Sn','jabatan'=>'Ketua Sanggar','ikon'=>'crown','urutan'=>1],
            ['nama'=>'Ibu Kartini, M.Pd','jabatan'=>'Sekretaris','ikon'=>'edit','urutan'=>2],
            ['nama'=>'Bpk. Surya Adi','jabatan'=>'Bendahara','ikon'=>'briefcase','urutan'=>3],
            ['nama'=>'Nita Sari, S.Sn','jabatan'=>'Koordinator Pelatihan','ikon'=>'star','urutan'=>4],
            ['nama'=>'Rizki Maulana','jabatan'=>'Koordinator Event','ikon'=>'calendar','urutan'=>5],
            ['nama'=>'Ayu Pratiwi','jabatan'=>'Humas & Publikasi','ikon'=>'users','urutan'=>6],
        ] as $pg){Pengelola::firstOrCreate(['nama'=>$pg['nama']],array_merge($pg,['aktif'=>true]));}

        foreach([
            ['hari'=>'Senin','jam_mulai'=>'15:00','jam_selesai'=>'17:30','kelas'=>'Tari Dasar (Pemula)','tempat'=>'Studio Utama','urutan'=>1],
            ['hari'=>'Rabu','jam_mulai'=>'15:00','jam_selesai'=>'17:30','kelas'=>'Tari Menengah','tempat'=>'Studio Utama','urutan'=>2],
            ['hari'=>'Jumat','jam_mulai'=>'15:00','jam_selesai'=>'18:00','kelas'=>'Tari Lanjutan & Persiapan Pentas','tempat'=>'Aula Besar','urutan'=>3],
            ['hari'=>'Sabtu','jam_mulai'=>'08:00','jam_selesai'=>'11:00','kelas'=>'Tari Anak-Anak (Usia 5-12 thn)','tempat'=>'Studio B','urutan'=>4],
            ['hari'=>'Minggu','jam_mulai'=>'08:00','jam_selesai'=>'12:00','kelas'=>'Sesi Khusus & Latihan Gabungan','tempat'=>'Aula Besar','urutan'=>5],
        ] as $j){JadwalLatihan::firstOrCreate(['hari'=>$j['hari'],'kelas'=>$j['kelas']],array_merge($j,['aktif'=>true]));}

        foreach([
            ['nama'=>'ASEAN Cultural Festival','lokasi'=>'Kuala Lumpur, Malaysia','tanggal'=>'2023-10-15','kategori'=>'internasional','level'=>'Internasional','hasil'=>'Best Performance','deskripsi'=>'Festival budaya ASEAN bergengsi mewakili Indonesia.','penghargaan'=>['Best Performance','Most Authentic Costume'],'jumlah_penonton'=>2500,'unggulan'=>true,'status'=>'selesai'],
            ['nama'=>'World Dance Festival','lokasi'=>'Seoul, Korea Selatan','tanggal'=>'2022-11-20','kategori'=>'internasional','level'=>'Internasional','hasil'=>'Silver Award','deskripsi'=>'Festival tari dunia di Seoul Korea.','penghargaan'=>['Silver Award'],'jumlah_penonton'=>1800,'unggulan'=>true,'status'=>'selesai'],
            ['nama'=>'Pekan Budaya Nusantara','lokasi'=>'TMII, Jakarta','tanggal'=>'2024-11-05','kategori'=>'kompetisi','level'=>'Nasional','hasil'=>'Juara 1','deskripsi'=>'Lomba tari tingkat nasional.','penghargaan'=>['Juara 1 Nasional'],'jumlah_penonton'=>5000,'unggulan'=>true,'status'=>'selesai'],
            ['nama'=>'HUT Kemerdekaan RI ke-79','lokasi'=>'Pendopo Indramayu','tanggal'=>'2024-08-17','kategori'=>'pentas','level'=>'Lokal','hasil'=>'Undangan Resmi','deskripsi'=>'Pentas HUT RI.','penghargaan'=>[],'jumlah_penonton'=>2000,'unggulan'=>false,'status'=>'selesai'],
            ['nama'=>'Festival Tari Nusantara Jabar','lokasi'=>'Gedung Kesenian Indramayu','tanggal'=>'2025-05-15','kategori'=>'festival','level'=>'Nasional','hasil'=>null,'deskripsi'=>'Festival mendatang.','penghargaan'=>[],'jumlah_penonton'=>null,'unggulan'=>false,'status'=>'akan_datang'],
        ] as $ev){Event::firstOrCreate(['nama'=>$ev['nama'],'tanggal'=>$ev['tanggal']],$ev);}

        foreach([
            ['nama'=>'Tari Topeng Kelana','asal'=>'Indramayu, Jawa Barat','kategori'=>'sakral','deskripsi'=>'Tari Topeng Kelana menggambarkan karakter Prabu Minakjinggo yang gagah perkasa. Topeng merah melambangkan keberanian dan hawa nafsu dengan gerakan dinamis dan ekspresif.','fungsi'=>'Pertunjukan Seni & Ritual','kostum'=>'Topeng merah, baju kebesaran, selendang','durasi'=>'15-30 menit','unggulan'=>true,'urutan'=>1],
            ['nama'=>'Tari Sintren','asal'=>'Indramayu & Cirebon','kategori'=>'ritual','deskripsi'=>'Tari ritual mistis dari pesisir utara Jawa. Penari utama dimasukkan ke kurungan dan kerasukan roh halus, keluar dengan kostum lengkap diiringi syair Jawa kuno.','fungsi'=>'Ritual Kepercayaan & Hiburan','kostum'=>'Pakaian pengantin Jawa, bunga-bungaan','durasi'=>'45-90 menit','unggulan'=>true,'urutan'=>2],
            ['nama'=>'Tari Ronggeng Bugis','asal'=>'Cirebon & Indramayu','kategori'=>'hiburan','deskripsi'=>'Tarian komedi penari pria berdandan wanita, menceritakan tentara Bugis yang menyamar mengintai musuh.','fungsi'=>'Hiburan & Komedi','kostum'=>'Pakaian wanita, rias tebal','durasi'=>'10-20 menit','unggulan'=>false,'urutan'=>3],
            ['nama'=>'Tari Baladewa','asal'=>'Indramayu','kategori'=>'perang','deskripsi'=>'Menggambarkan Prabu Baladewa dari Mahabharata, ksatria kuat dan berwibawa dengan gerakan tegas.','fungsi'=>'Pertunjukan Seni & Budaya','kostum'=>'Busana ksatria Jawa, mahkota, keris','durasi'=>'15-25 menit','unggulan'=>false,'urutan'=>4],
            ['nama'=>'Tari Buyung','asal'=>'Indramayu','kategori'=>'penyambutan','deskripsi'=>'Tarian penyambutan menggambarkan gadis desa mengambil air dengan buyung di kepala, menunjukkan keanggunan dan keseimbangan.','fungsi'=>'Tari Penyambutan Tamu & Upacara','kostum'=>'Kebaya, batik Indramayu, buyung di kepala','durasi'=>'8-15 menit','unggulan'=>true,'urutan'=>5],
            ['nama'=>'Tari Ngrema','asal'=>'Adaptasi di Indramayu','kategori'=>'hiburan','deskripsi'=>'Tarian pembuka pertunjukan ludruk atau ketoprak yang lincah dan enerjik, dipadukan dengan elemen budaya lokal Indramayu.','fungsi'=>'Pembuka Pertunjukan','kostum'=>'Busana tradisional Jawa, ikat kepala','durasi'=>'5-10 menit','unggulan'=>false,'urutan'=>6],
        ] as $t){Tarian::firstOrCreate(['nama'=>$t['nama']],array_merge($t,['aktif'=>true]));}
    }
}