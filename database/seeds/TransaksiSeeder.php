<?php

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\OPDJenisPendapatan;
use Illuminate\Database\Seeder;

use Carbon\Carbon;
use Faker\Factory as Faker;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 50; $i++) {

            $jenisPendapatan = OPDJenisPendapatan::where('id_jenis_pendapatan', rand(8, 17))->first();
            $kecamatan = Kecamatan::where('id', rand(1, 4959))->first();
            $kelurahan = Kelurahan::where('kecamatan_id', $kecamatan->id)->first();

            $jumlah_bayar = $faker->numerify('##########');
            $denda = $faker->numerify('#######');

            DB::table('tmtransaksi_opd')->insert([
                'id_opd' => 16,
                'tgl_ttd' => Carbon::create(2021, 8, \rand(1, 30)),
                'nm_ttd' => $faker->name,
                'id_jenis_pendapatan' => $jenisPendapatan->id_jenis_pendapatan,
                'rincian_jenis_pendapatan' => $jenisPendapatan->jenis_pendapatan->jenis_pendapatan,
                'nmr_daftar' => $faker->numerify('##########'),
                'nm_wajib_pajak' => $faker->name,
                'alamat_wp' => $faker->address,
                'lokasi' => $faker->streetAddress,
                'kecamatan_id' => $kecamatan->id,
                'kelurahan_id' => $kelurahan->id,
                'uraian_retribusi' => $faker->word,
                'jumlah_bayar' => $faker->numerify('##########'),
                'denda' => $faker->numerify('##########'),
                'total_bayar' => $jumlah_bayar + $denda,
                'status_bayar' => rand(0, 1),
                'no_skrd' => '16' . $jenisPendapatan->id_jenis_pendapatan . rand(1, 999),
                'tgl_skrd_awal' => Carbon::create(2021, 8, \rand(1, 30)),
                'tgl_skrd_akhir' => Carbon::create(2021, 8, \rand(1, 30)),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
