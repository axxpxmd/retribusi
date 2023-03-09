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
                        Edit {{ $title }} | {{ $data->nm_wajib_pajak }}
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
                <div class="">
                    <div class="col-md-12 p-0">
                        @if ($checkDuplicate > 1)
                            <div class="alert font-weight-bold alert-danger mb-2">No Bayar dan No SKRD data ini duplikat, silahkan klik button GENERATE untuk mendapatkan No Bayar dan No SKRD baru.</div>
                        @endif
                        @include('layouts.alerts')
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Edit Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="PATCH"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('PATCH') }}
                                    <input type="hidden" id="id" name="id" value="{{ $data->id }}"/>
                                    <input type="hidden" name="kd_jenis" id="kd_jenis">
                                    <input type="hidden" name="no_hp" id="no_hp">
                                    <div class="">
                                        <div class="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row mb-2">
                                                        <label for="n_opd" class="col-form-label s-12 col-sm-4 text-right">Nama OPD<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" value="{{ $data->opd->n_opd }}"  class="form-control r-0 s-12" autocomplete="off" disabled required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="rincian_jenis_pendapatan" class="col-form-label s-12 col-sm-4 text-right">Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{ $data->jenis_pendapatan->jenis_pendapatan }}"  class="form-control r-0 s-12" autocomplete="off" disabled required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label class="col-form-label s-12 col-sm-4 text-right">Rincian Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <select class="select2 form-control r-0 s-12" id="id_rincian_jenis_pendapatan" name="id_rincian_jenis_pendapatan" autocomplete="off">
                                                                <option value="{{ \Crypt::encrypt(0) }}">Pilih</option>
                                                                @foreach ($rincian_jenis_pendapatans as $i)
                                                                    <option {{ $i->id == $data->id_rincian_jenis_pendapatan ? 'selected' : '-'}} value="{{ \Crypt::encrypt($i->id) }}">{{ $i->rincian_pendapatan }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row mb-2">
                                                        <label for="kode_rekening" class="col-form-label s-12 col-sm-4 text-right">Kode Rekening<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" id="kode_rekening" name="kode_rekening" class="form-control r-0 s-12" autocomplete="off" readonly required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="uraian_retribusi" class="col-form-label s-12 col-sm-4 text-right">Uraian Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <textarea type="text" rows="3" name="uraian_retribusi" id="uraian_retribusi" class="form-control r-0 s-12" autocomplete="off" required>{{ $data->uraian_retribusi }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row mb-2">
                                                        <label for="nmr_daftar" class="col-form-label s-12 col-sm-4 text-right">Nomor Daftar<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="nmr_daftar" id="nmr_daftar" value="{{ $data->nmr_daftar }}" class="form-control r-0 s-12" autocomplete="off" required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="nm_wajib_pajak" class="col-form-label s-12 col-sm-4 text-right">Nama<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="nm_wajib_pajak" id="nm_wajib_pajak" value="{{ $data->nm_wajib_pajak }}" class="form-control r-0 s-12" autocomplete="off" required onkeypress="return (event.charCode > 64 && event.charCode < 91) || (event.charCode > 96 && event.charCode < 123) || (event.charCode==32)"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="email" class="col-form-label s-12 col-sm-4 text-right">Email</label>
                                                        <div class="col-md-8">
                                                            <input type="email" name="email" id="email" value="{{ $data->email }}" class="form-control r-0 s-12"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="no_telp" class="col-form-label s-12 col-sm-4 text-right">No Telp</label>
                                                        <div class="col-md-8">
                                                            <input type="number" name="no_telp" id="no_telp" value="{{ $data->no_telp }}" class="form-control r-0 s-12"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <label for="alamat_wp" class="col-form-label s-12 col-sm-4 text-right">Alamat<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <textarea type="text" rows="3" name="alamat_wp" id="alamat_wp" class="form-control r-0 s-12" autocomplete="off" required>{{ $data->alamat_wp }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <label for="lokasi" class="col-form-label s-12 col-sm-4 text-right">Lokasi<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <textarea type="text" rows="2" name="lokasi" id="lokasi" placeholder="Contoh: Ruko Sektor 1.2 BSD" class="form-control r-0 s-12" autocomplete="off" required>{{ $data->lokasi }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="kecamatan_id" class="col-form-label s-12 col-sm-4 text-right">Kecamatan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" disabled id="kecamatan_id" value="{{ $data->kecamatan->n_kecamatan }}" class="form-control r-0 s-12" autocomplete="off"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="kelurahan_id" class="col-form-label s-12 col-sm-4 text-right">Kelurahan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" disabled id="kelurahan_id" value="{{ $data->kelurahan->n_kelurahan }}" class="form-control r-0 s-12" autocomplete="off"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row mb-2">
                                                        <label for="" class="col-form-label s-12 col-sm-4 text-right">No Bayar<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" disabled value="{{ $data->no_bayar }}" class="form-control r-0 s-12" autocomplete="off"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="" class="col-form-label s-12 col-sm-4 text-right">No SKRD<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" disabled value="{{ $data->no_skrd }}" class="form-control r-0 s-12" autocomplete="off"/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="tgl_skrd_awal" class="col-form-label s-12 col-sm-4 text-right">Tanggal SKRD<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="date" onchange="setDate()" value="{{ $data->tgl_skrd_awal }}" name="tgl_skrd_awal" id="tgl_skrd_awal" class="form-control r-0 s-12" autocomplete="off" required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="tgl_skrd_akhir" class="col-form-label s-12 col-sm-4 text-right">Jatuh Tempo<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="date" name="tgl_skrd_akhir" readonly value="{{ $data->tgl_skrd_akhir }}" id="tgl_skrd_akhir" class="form-control r-0 s-12" autocomplete="off" required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label class="col-form-label s-12 col-sm-4 text-right font-weight-normal">Penanda Tangan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <select class="select2 form-control r-0 s-12" id="penanda_tangan_id" name="penanda_tangan_id" autocomplete="off">
                                                                <option value="0">Pilih</option>
                                                                @foreach ($penanda_tangans as $i)
                                                                    <option value="{{ $i->id }}" {{ $i->user->pengguna->full_name == $data->nm_ttd ? 'selected' : '-' }}>{{ $i->user->pengguna->full_name }} [ NIP. {{ $i->user->pengguna->nip }} ]</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="tgl_ttd" class="col-form-label s-12 col-sm-4 text-right">Tanggal TTD<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="date" name="tgl_ttd" id="tgl_ttd" value="{{ $data->tgl_ttd }}" class="form-control r-0 s-12" autocomplete="off" required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label for="jumlah_bayar" class="col-form-label s-12 col-sm-4 text-right">Ketetapan<span class="text-danger ml-1">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="jumlah_bayar" id="rupiah" value="{{ $data->jumlah_bayar }}" class="form-control r-0 s-12" autocomplete="off" required/>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <label class="col-sm-4"></label>
                                                        <div class="col-md-8">
                                                            <button type="submit" id="action" class="btn btn-block btn-primary btn-sm"><i class="icon-save mr-2"></i>Simpan Perubahan</button>
                                                            @if ($checkDuplicate > 1)
                                                                <button type="button" data-toggle="modal" data-target="#generate" class="btn btn-block btn-outline-danger font-weight-bold btn-sm"><i class="icon-refresh2 mr-2"></i>GENERATE</button>
                                                            @endif
                                                        </div>
                                                    </div>
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
<div class="modal fade" id="generate" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p class="font-weight-bold fs-14">Apakah sudah yakin untuk generate ulang No Bayar dan No SKRD data ini ?</p>
                <hr>
                <div class="text-right">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                    <a href="{{ route('generate', $data->id) }}" class="btn btn-sm btn-success ml-2" id="kirimTTD"><i class="icon-refresh2 mr-2"></i>Generate</a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.loading')
@endsection
@section('script')
<script type="text/javascript">
    $('#kode_rekening').val("{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '' }}");
    $('#kd_jenis').val("{{ $data->rincian_jenis != null ? $data->rincian_jenis->kd_jenis : '' }}");
    $('#no_hp').val("{{ $data->rincian_jenis != null ? $data->rincian_jenis->no_hp : '' }}");

    $('#id_rincian_jenis_pendapatan').on('change', function(){
        val = $(this).val();
        url = "{{ route('skrd.getKodeRekening', ':id') }}".replace(':id', val);
        $.get(url, function(data){
            $('#kode_rekening').val(data.nmr_rekening);
            $('#kd_jenis').val(data.kd_jenis);
            $('#no_hp').val(data.no_hp);
        }, 'JSON');
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

    function setDate(){
        var date = document.getElementById('tgl_skrd_awal').value
        var someDate = new Date(date);
        someDate.setDate(someDate.getDate() + 30); //number  of days to add, e.x. 30 days
        var dateFormated = someDate.toISOString().substr(0,10);
        $('#tgl_skrd_akhir').val(dateFormated);
    }

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            $('#action').attr('disabled', true);
            $('#loading').modal('show');
            url = "{{ route($route.'update', ':id') }}".replace(':id', $('#id').val());
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    $('#loading').modal('toggle');
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
                    $('#loading').modal('toggle');
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });
</script>
@endsection