<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth', 'checksinglesession']], function () {
    Route::namespace('Profile')->group(function () {
        Route::resource('profile', 'ProfileController');
        Route::get('profile/{id}/edit-password', 'ProfileController@editPassword')->name('profile.editPassword');
        Route::post('profile/{id}/update-password', 'ProfileController@updatePassword')->name('profile.updatePassword');
    });

    /**
     * MASTER ROLES
     */
    Route::prefix('master-roles')->namespace('MasterRole')->name('master-role.')->group(function () {
        // Role
        Route::resource('role', 'RoleController');
        Route::prefix('role')->name('role.')->group(function () {
            Route::post('api', 'RoleController@api')->name('api');
            Route::get('{id}/addPermissions', 'RoleController@permission')->name('addPermissions');
            Route::post('storePermissions', 'RoleController@storePermission')->name('storePermissions');
            Route::get('{id}/getPermissions', 'RoleController@getPermissions')->name('getPermissions');
            Route::delete('{name}/destroyPermission', 'RoleController@destroyPermission')->name('destroyPermission');
        });
        // Permission
        Route::resource('permission', 'PermissionController');
        Route::post('permission/api', 'PermissionController@api')->name('permission.api');
        // Pengguna
        Route::resource('pengguna', 'PenggunaController');
        Route::post('pengguna/api', 'PenggunaController@api')->name('pengguna.api');
    });

    // OPD
    Route::resource('opd', 'OPDController');
    Route::post('opd/api', 'OPDController@api')->name('opd.api');
    Route::get('opd/edit-jenis-pendapatan/{id}', 'OPDController@jenisPendapatan')->name('opd.editJenisPendapatan');
    Route::post('opd/store-jenis-pendapatan', 'OPDController@storeJenisPendapatan')->name('opd.storeJenisPendapatan');
    Route::get('opd/get-jenis-pendapatan/{id}', 'OPDController@getJenisPendapatan')->name('opd.getJenisPendapatan');
    Route::delete('opd/delete-jenis-pendapatan/{id}', 'OPDController@destroyJenisPendapatan')->name('opd.destoryJenisPendapatan');
    Route::get('opd/edit-penanda-tangan/{id}', 'OPDController@penandaTangan')->name('opd.penandaTangan');
    Route::get('opd/get-penanda-tangan/{id}', 'OPDController@getPenandaTangan')->name('opd.getPenandaTangan');
    Route::post('opd/store-penanda-tangan', 'OPDController@storePenandaTangan')->name('opd.storePenandaTangan');
    Route::delete('opd/delete-penanda-tangan/{id}', 'OPDController@destroyPenandaTangan')->name('opd.destroyPenandaTangan');

    // Jenis Pendapatan
    Route::resource('jenis-pendapatan', 'JenisPendapatanController');
    Route::post('jenis-pendapatan/api', 'JenisPendapatanController@api')->name('jenis-pendapatan.api');

    // Rincian Jenis Pendapatan
    Route::resource('rincian-jenis', 'RincianJenisController');
    Route::post('rincian-jenis/api', 'RincianJenisController@api')->name('rincian-jenis.api');

    // Data WP
    Route::resource('datawp', 'DataWPController');
    Route::post('datawp/api', 'DataWPController@api')->name('datawp.api');

    // SKRD
    Route::resource('skrd', 'SKRDController');
    Route::get('skrd/get-data-skrd/{id}', 'SKRDController@getDataSKRD')->name('skrd.getDataSKRD');
    Route::get('skrd/get-jenis-pendapatan/{opd_id}', 'SKRDController@getJenisPendapatanByOpd')->name('skrd.getJenisPendapatan');
    Route::get('skrd/get-kode-rekening/{id_rincian_jenis_pendapatan}', 'SKRDController@getKodeRekening')->name('skrd.getKodeRekening');
    Route::get('skrd/get-kelurahan/{id}', 'SKRDController@kelurahanByKecamatan')->name('skrd.kelurahanByKecamatan');
    Route::get('update-status-kirim-ttd-skrd/{id}', 'SKRDController@updateStatusKirimTTD')->name('skrd.updateStatusKirimTTD');

    // STRD
    Route::resource('strd', 'STRDController');
    Route::post('strd/api', 'STRDController@api')->name('strd.api');
    Route::get('update-status-kirim-ttd-strd/{id}', 'STRDController@updateStatusKirimTTD')->name('strd.updateStatusKirimTTD');
    Route::get('update-status-kirim-ttds-strd', 'STRDController@updateStatusKirimTTDs')->name('strd.updateStatusKirimTTDs');
    Route::get('perbarui-strd/{id}', 'STRDController@perbaruiSTRD')->name('strd.perbaruiSTRD');

    // STS
    Route::resource('sts', 'STSController');
    Route::post('sts/api', 'STSController@api')->name('sts.api');
    Route::get('sts/report-ttd/{id}', 'STSController@printDataTTD')->name('sts.reportTTD');
    Route::get('batal_bayar/{id}', 'STSController@batalBayar')->name('sts.batalBayar');

    // Diskon
    Route::get('diskon', 'DiskonController@index')->name('diskon.index');
    Route::post('diskon/api', 'DiskonController@api')->name('diskon.api');
    Route::get('diskon/update-diskon', 'DiskonController@updateDiskon')->name('diskon.updateDiskon');

    // Denda
    Route::get('denda', 'DendaController@index')->name('denda.index');
    Route::post('denda/api', 'DendaController@api')->name('denda.api');
    Route::get('denda/update-denda', 'DendaController@updateDenda')->name('denda.updateDenda');

    // Tanda Tangan
    Route::resource('tanda-tangan', 'TandaTanganController');
    Route::get('tanda-tangan/restore/{id}', 'TandaTanganController@restoreTTD')->name('tanda-tangan.restoreTTD');
    Route::post('tanda-tangan/tteBackup', 'TandaTanganController@tteBackup')->name('tanda-tangan.tteBackup');
    Route::post('tanda-tangan/tte', 'TandaTanganController@tte')->name('tanda-tangan.tte');
    Route::post('tanda-tangan/tte-bsre', 'TandaTanganController@tteBSRE')->name('tanda-tangan.tteBSRE');
    Route::post('tanda-tangan/', 'TandaTanganController@tandaTangan')->name('tanda-tangan.tandaTangan');

    // Print 
    Route::get('print/skrd/{id}', 'PrintController@printSKRD')->name('print.skrd');
    Route::get('print/strd/{id}', 'PrintController@printSTRD')->name('print.strd');
    Route::get('print/download/{id}', 'PrintController@download')->name('print.download');

    // Report
    Route::get('report', 'ReportController@index')->name('report.index');
    Route::get('report/{id}', 'ReportController@show')->name('report.show');
    Route::post('report/api-skrd', 'ReportController@api')->name('report.api');
    Route::get('cetak-skrd', 'ReportController@cetakSKRD')->name('report.cetakSKRD');
    Route::get('report/get-jenis-pendapatan/{opd_id}', 'ReportController@getJenisPendapatanByOpd')->name('report.getJenisPendapatan');
    Route::get('report/get-rincian-pendapatan/{jenis_pendapatan_id}', 'ReportController@getRincianByJenisPendapatan')->name('report.getRincianByJenisPendapatan');
    Route::get('get-total-bayar', 'ReportController@getTotalBayar')->name('report.getTotalBayar');

    // Send Email
    Route::get('send-email/{id}', 'EmailController@sendEmail')->name('sendEmail');
    Route::get('send-email/skrd/{id}', 'EmailController@skrd')->name('sendEmailSKRD');

    Route::get('log', 'LogController@index')->name('log.index');
    Route::get('log/{id}', 'LogController@show')->name('log.show');
});

Route::get('sts/report/{id}', 'UtilityController@printDataTTD')->name('sendSTS');
Route::get('skrd/report/{id}', 'UtilityController@getDataSKRD')->name('sendSKRD');
