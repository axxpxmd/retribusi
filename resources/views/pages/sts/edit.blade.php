@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<style>
    .label-input-custom{
        font-size: 12px !important;
        text-align: right !important;
        border: none !important;
        padding-right: 1.5rem !important;
        color: #86939E !important;
        font-weight: 400 !important
    }
</style>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
         <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-document-list mr-2"></i>
                        Menampilkan {{ $title }} | {{ $data->nm_wajib_pajak }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-edit"></i>Edit Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Edit Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="PATCH"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('PATCH') }}
                                    <input type="hidden" id="id" name="id" value="{{ \Crypt::encrypt($data->id) }}"/>
                                    <div class="form-row form-inline">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="n_opd" class="form-control label-input-custom col-md-4">Nama OPD<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="n_opd" id="n_opd" value="{{ $data->opd->n_opd }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="rincian_jenis_pendapatan" class="form-control label-input-custom col-md-4">Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{ $data->jenis_pendapatan->jenis_pendapatan }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="rincian_jenis_pendapatan" class="form-control label-input-custom col-md-4">Rincian Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{  $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '' }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                <div class="form-group m-0">
                                                        <label for="kode_rekening" class="form-control label-input-custom col-md-4">Kode Rekening<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" id="kode_rekening" value="{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '' }}" class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="uraian_retribusi" class="form-control label-input-custom col-md-4">Uraian Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <textarea type="text" rows="3" name="uraian_retribusi" id="uraian_retribusi" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required>{{ $data->uraian_retribusi }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                <div class="form-group m-0">
                                                        <label class="form-control label-input-custom col-md-4">No Bayar<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" value="{{ $data->no_bayar }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nmr_daftar" class="form-control label-input-custom col-md-4">Nomor Daftar<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nmr_daftar" id="nmr_daftar" value="{{ $data->nmr_daftar }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nm_wajib_pajak" class="form-control label-input-custom col-md-4">Nama Wajib Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nm_wajib_pajak" id="nm_wajib_pajak" value="{{ $data->nm_wajib_pajak }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="alamat_wp" class="form-control label-input-custom col-md-4">Alamat Wajib Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="alamat_wp" id="alamat_wp" value="{{ $data->alamat_wp }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="lokasi" class="form-control label-input-custom col-md-4">Lokasi<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="lokasi" id="lokasi" value="{{ $data->lokasi }}" readonly placeholder="Contoh: Ruko Sektor 1.2 BSD" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="kecamatan_id" class="form-control label-input-custom col-md-4">Kecamatan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="kecamatan_id" id="kecamatan_id" value="{{ $data->kecamatan->n_kecamatan }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="kelurahan_id" class="form-control label-input-custom col-md-4">Kelurahan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="kelurahan_id" id="kelurahan_id" value="{{ $data->kelurahan->n_kelurahan }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="no_skrd" class="form-control label-input-custom col-md-4">Nomor SKRD<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="no_skrd" id="no_skrd" value="{{ $data->no_skrd }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_skrd_awal" class="form-control label-input-custom col-md-4">Tanggal SKRD<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" value="{{ $data->tgl_skrd_awal }}" readonly name="tgl_skrd_awal" id="tgl_skrd_awal" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_skrd_akhir" class="form-control label-input-custom col-md-4">Jatuh Tempo<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" name="tgl_skrd_akhir" value="{{ $data->tgl_skrd_akhir }}" id="tgl_skrd_akhir" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nm_ttd" class="form-control label-input-custom col-md-4">Nama Penandatangan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nm_ttd" id="nm_ttd" value="{{ $data->nm_ttd }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nip_ttd" class="form-control label-input-custom col-md-4">NIP Penandatangan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nip_ttd" id="nip_ttd" value="{{ $data->nip_ttd }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_ttd" class="form-control label-input-custom col-md-4">Tanggal TTD<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" name="tgl_ttd" id="tgl_ttd" value="{{ $data->tgl_ttd }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="jumlah_bayar" class="form-control label-input-custom col-md-4">Ketetapan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="jumlah_bayar" value="{{ $data->jumlah_bayar }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                </div>
                                            </div> 
                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label class="form-control label-input-custom col-md-4">Status</label>
                                                        <div class="col-md-8 p-0 bg-light">
                                                            <select class="select2 form-control r-0 light s-12" id="status_bayar" name="status_bayar" autocomplete="off">
                                                                <option value="">Pilih</option>
                                                                <option value="0">Belum Dibayar</option>
                                                                <!-- check untuk bendahara OPD -->
                                                                @if ($data->ntb != null && $data->total_bayar_bjb != null)
                                                                <option value="1">Sudah Dibayar</option>
                                                                @endif
                                                                <!-- check untuk admin OPD -->
                                                                @role('admin-opd|super-admin')
                                                                    @if ($data->ntb == null && $data->total_bayar_bjb == null)
                                                                    <option value="1">Sudah Dibayar</option>
                                                                    @endif
                                                                @endrole
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-1">
                                                        <label for="tgl_bayar" class="form-control label-input-custom col-md-4">Tanggal Bayar</label>
                                                        <input type="datetime-local" name="tgl_bayar" {{ $readonly }} value="{{ $data->tgl_bayar != null ? date('Y-m-d\TH:i', strtotime($data->tgl_bayar)) : $now }}" id="tgl_bayar" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="no_bku" class="form-control label-input-custom col-md-4">Nomor BKU</label>
                                                        <input type="text" name="no_bku" value="{{ $data->no_bku }}" {{ $readonly }} id="no_bku" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="chanel_bayar" class="form-control label-input-custom col-md-4">Chanel Bayar</label>
                                                        <input type="text" name="chanel_bayar" value="{{ $data->chanel_bayar != null ? $data->chanel_bayar : 'Bendahara OPD'  }}" {{ $readonly }} id="chanel_bayar" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <!-- <div class="form-group m-0">
                                                        <label for="tgl_bku" class="form-control label-input-custom col-md-4">Tanggal BKU</label>
                                                        <input type="date" name="tgl_bku" value="{{ substr($data->tgl_bku,0,10) }}" id="tgl_bku" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div> -->
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="ntb" class="form-control label-input-custom col-md-4">NTB</label>
                                                        <input type="text" name="ntb" value="{{ $data->ntb }}" {{ $readonly }} id="ntb" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="denda" class="form-control label-input-custom col-md-4">Denda</label>
                                                        <input type="text" name="denda" value="{{ $data->denda == 0 ? $jumlahBunga : $data->denda }}" {{ $readonly }} id="rupiah1" onkeyup="totalBayarBJB()" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="diskon" class="form-control label-input-custom col-md-4">Diskon</label>
                                                        <input type="text" name="diskon" value="{{ $data->diskon }} %" readonly id="diskon" class="form-control r-0 light col-md-1 s-12" autocomplete="off"/>
                                                        <input type="text" name="jumlah_diskon" value="@currency($data->diskon / 100 * $data->jumlah_bayar )" readonly id="diskon" class="form-control r-0 light col-md-7 s-12" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="total_bayar_bjb" class="form-control label-input-custom col-md-4">Total Bayar Bank</label>
                                                        <input type="text" name="total_bayar_bjb" value="{{ $data->total_bayar_bjb }}" {{ $readonly }} id="rupiah2" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2">
                                                        <div class="col-md-4"></div>
                                                        <button type="submit" class="btn btn-primary btn-sm"><i class="icon-save mr-2"></i>Simpan</button>
                                                    </div>  
                                                </div>
                                                <div class="col-md-6">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $('#status_bayar').val("{{ $data->status_bayar }}");
    $('#status_bayar').trigger('change.select2');

    $(document).ready(function () {
        totalBayarBJB();
    });

    function totalBayarBJB() {
        total_bayar = "{{ $data->total_bayar }}";
        denda = document.getElementById("rupiah1").value;
        dendaReplace = denda.replace(/\./g, '').replace('Rp', '').replace(' ', '');

        totalBJB = parseInt(total_bayar) + parseInt(dendaReplace);

        if (isNaN(totalBJB)) {
            $('#rupiah2').val('')
        }else{
            $('#rupiah2').val(totalBJB)     
        }
    }

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            $('#action').attr('disabled', true);
            url = "{{ route($route.'update', ':id') }}".replace(':id', $('#id').val());
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    $.confirm({
                        title: 'Success',
                        content: data.message,
                        icon: 'icon icon-check',
                        theme: 'modern',
                        closeIcon: true,
                        animation: 'scale',
                        autoClose: 'ok|3000',
                        type: 'green',
                        buttons: {
                            ok: {
                                text: "ok!",
                                btnClass: 'btn-primary',
                                keys: ['enter'],
                                action: function () {
                                    location.reload();
                                }
                            }
                        }
                    });
                },
                error : function(data){
                    err = '';
                    respon = data.responseJSON;
                    if(respon.errors){
                        $.each(respon.errors, function( index, value ) {
                            err = err + "<li>" + value +"</li>";
                        });
                    }
                    $('#alert').html("<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Error!</strong> " + respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");
                    $('#action').removeAttr('disabled');
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });

    var rupiah = [];
    for (let index = 1; index <= 2; index++) {
        rupiah[index] = document.getElementById('rupiah'+index);
        rupiah[index].addEventListener('keyup', function(e){
            // tambahkan 'Rp.' pada saat form di ketik
            // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
            rupiah[index].value = formatRupiah(this.value, 'Rp. ');
        });
        /* Fungsi formatRupiah */
        function formatRupiah(angka, prefix){
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split   		= number_string.split(','),
            sisa     		= split[0].length % 3,
            rupiah     		= split[0].substr(0, sisa),
            ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);
            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if(ribuan){
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    }
</script>
@endsection