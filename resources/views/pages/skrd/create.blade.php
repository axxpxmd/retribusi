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
            <div class="row p-t-b-10">
                <div class="col">
                    <h4>
                        <i class="icon icon-document-list mr-2"></i>
                        Tambah {{ $title }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-add"></i>Tambah Data</a>
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
                            <h6 class="card-header"><strong>Tambah Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="POST"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="id_opd" value="{{ $opd->id }}">
                                    <input type="hidden" name="id_jenis_pendapatan" value="{{ $jenis_pendapatan->id }}">
                                    <input type="hidden" name="kd_jenis" id="kd_jenis" value="{{ $data_wp != null ? $data_wp->rincian_jenis->kd_jenis : '' }}">
                                    <div class="form-row form-inline">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="n_opd" class="form-control label-input-custom col-md-4 font-weight-normal">Nama OPD<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="n_opd" id="n_opd" value="{{ $opd->n_opd }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="rincian_jenis_pendapatan" class="form-control label-input-custom col-md-4 font-weight-normal">Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{ $jenis_pendapatan->jenis_pendapatan }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label class="form-control label-input-custom col-md-4 font-weight-normal">Rincian Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8 p-0 bg-light">
                                                            <select class="select2 form-control r-0 light s-12" id="id_rincian_jenis_pendapatan" name="id_rincian_jenis_pendapatan" autocomplete="off">
                                                                <option value="{{ \Crypt::encrypt(0) }}">Pilih</option>
                                                                @foreach ($rincian_jenis_pendapatans as $i)
                                                                    <option {{ $data_wp != null ? $data_wp->id_rincian_jenis_pendapatan == $i->id ? 'selected' : '-' : '' }} value="{{ \Crypt::encrypt($i->id) }}">{{ $i->rincian_pendapatan }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="kode_rekening" class="form-control label-input-custom col-md-4 font-weight-normal">Kode Rekening<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="kode_rekening" id="kode_rekening" value="{{ $data_wp != null ? $data_wp->rincian_jenis->nmr_rekening : '' }}" class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="uraian_retribusi" class="form-control label-input-custom col-md-4 font-weight-normal">Uraian Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <textarea type="text" rows="3" name="uraian_retribusi" id="uraian_retribusi" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="nmr_daftar" class="form-control label-input-custom col-md-4 font-weight-normal">Nomor Daftar<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nmr_daftar" id="nmr_daftar" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nm_wajib_pajak" class="form-control label-input-custom col-md-4 font-weight-normal">Nama Wajib Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nm_wajib_pajak" id="nm_wajib_pajak" value="{{ $data_wp != null ? $data_wp->nm_wajib_pajak : '' }}" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode==32)"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="alamat_wp" class="form-control label-input-custom col-md-4 font-weight-normal">Alamat Wajib Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <textarea type="text" rows="3" name="alamat_wp" id="alamat_wp" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required>{{ $data_wp != null ? $data_wp->alamat_wp : '' }}</textarea>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="lokasi" class="form-control label-input-custom col-md-4 font-weight-normal">Lokasi<span class="text-danger ml-1">*</span></label>
                                                        <textarea type="text" rows="2" name="lokasi" id="lokasi" placeholder="Contoh: Ruko Sektor 1.2 BSD" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required>{{ $data_wp != null ? $data_wp->lokasi : '' }}</textarea>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label class="form-control label-input-custom col-md-4 font-weight-normal">Kecamatan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8 p-0 bg-light">
                                                            <select class="select2 form-control r-0 light s-12" id="kecamatan_id" name="kecamatan_id" autocomplete="off">
                                                                <option value="">Pilih</option>
                                                                @foreach ($kecamatans as $i)
                                                                    <option {{ $data_wp != null ? $data_wp->kecamatan_id == $i->id ? 'selected' : '-' : '' }} value="{{ $i->id }}">{{ $i->n_kecamatan }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-1">
                                                        <label class="form-control label-input-custom col-md-4 font-weight-normal">Kelurahan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8 p-0 bg-light">
                                                            <select class="select2 form-control r-0 light s-12" id="kelurahan_id" name="kelurahan_id" autocomplete="off">
                                                                @if ($data_wp != null)
                                                                    <option value="{{ $data_wp->kelurahan_id }}">{{ $data_wp->kelurahan->n_kelurahan }}</option>
                                                                @endif
                                                                <option value="">Pilih</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="tgl_skrd_awal" class="form-control label-input-custom col-md-4 font-weight-normal">Tanggal SKRD<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" onchange="setDate()" name="tgl_skrd_awal" id="tgl_skrd_awal" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_skrd_akhir" class="form-control label-input-custom col-md-4 font-weight-normal">Jatuh Tempo<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" name="tgl_skrd_akhir" id="tgl_skrd_akhir" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nm_ttd" class="form-control label-input-custom col-md-4 font-weight-normal">Nama Penandatangan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nm_ttd" id="nm_ttd" readonly value="{{ $opd->nm_ttd }}" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nip_ttd" class="form-control label-input-custom col-md-4 font-weight-normal">NIP Penandatangan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nip_ttd" id="nip_ttd" readonly value="{{ $opd->nip_ttd }}" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_ttd" class="form-control label-input-custom col-md-4 font-weight-normal">Tanggal TTD<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" name="tgl_ttd" id="tgl_ttd" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="jumlah_bayar" class="form-control label-input-custom col-md-4 font-weight-normal">Ketetapan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="jumlah_bayar" id="rupiah" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                </div>
                                            </div> 

                                            <div class="row">
                                                <div class="col-md-6">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mt-2">
                                                        <div class="col-md-4"></div>
                                                        <button type="submit" id="action" class="btn btn-primary btn-sm"><i class="icon-save mr-2"></i>Simpan</button>
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
@include('layouts.loading')
@endsection
@section('script')
<script type="text/javascript">
    function setDate(){
        var date = document.getElementById('tgl_skrd_awal').value
        var someDate = new Date(date);
        someDate.setDate(someDate.getDate() + 30); // number  of days to add, e.x. 30 days
        var dateFormated = someDate.toISOString().substr(0,10);
        $('#tgl_skrd_akhir').val(dateFormated);
    }

    $('#id_rincian_jenis_pendapatan').on('change', function(){
        val = $(this).val();
        url = "{{ route('skrd.getKodeRekening', ':id') }}".replace(':id', val);
        $.get(url, function(data){
            $('#kode_rekening').val(data.nmr_rekening);
            $('#kd_jenis').val(data.kd_jenis);
        }, 'JSON');    
    });

    $('#kecamatan_id').on('change', function(){
        val = $(this).val();
        option = "<option value=''>&nbsp;</option>";
        if(val == ""){
            $('#kelurahan_id').html(option);
        }else{
            $('#kelurahan_id').html("<option value=''>Loading...</option>");
            url = "{{ route('skrd.kelurahanByKecamatan', ':id') }}".replace(':id', val);
            $.get(url, function(data){
                if(data){
                    $.each(data, function(index, value){
                        option += "<option value='" + value.id + "'>" + value.n_kelurahan +"</li>";
                    });
                    $('#kelurahan_id').empty().html(option);

                    $("#kelurahan_id").val($("#kelurahan_id option:first").val()).trigger("change.select2");
                }else{
                    $('#kelurahan_id').html(option);
                }
            }, 'JSON'); 
        }
    });

    rupiah = document.getElementById('rupiah');
    rupiah.addEventListener('keyup', function(e){
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value, 'Rp. ');
    });

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix){
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split  = number_string.split(','),
        sisa   = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if(ribuan){
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            $("#kecamatan_id").prop("disabled", false);
            $("#kelurahan_id").prop("disabled", false);
            $("#id_rincian_jenis_pendapatan").prop("disabled", false);
            $('#action').attr('disabled', true);
            $('#loading').modal('show');
            url = "{{ route($route.'store') }}";
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    $('#loading').modal('hide');
                    $.confirm({
                        title: 'Success',
                        content: data.message,
                        icon: 'icon icon-check', 
                        theme: 'modern',
                        animation: 'scale',
                        autoClose: 'ok|3000',
                        type: 'green',
                        buttons: {
                            ok: {
                                text: "ok!",
                                btnClass: 'btn-primary',
                                keys: ['enter'],
                                action: function () {
                                    window.location.href = "{{ route('skrd.index')}}";
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
                    $('#loading').modal('hide');
                    $('#alert').html("<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Error!</strong> " + respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");
                    $('#action').removeAttr('disabled');
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });
    
</script>
@endsection
