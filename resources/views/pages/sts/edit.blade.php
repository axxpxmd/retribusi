@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-document mr-2"></i>
                        Show {{ $title }} | {{ $data->nm_wajib_pajak }}
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
                                                        <label for="n_opd" class="col-form-label s-12 col-md-4">Nama OPD<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="n_opd" id="n_opd" value="{{ $data->opd->n_opd }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="rincian_jenis_pendapatan" class="col-form-label s-12 col-md-4">Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{ $data->jenis_pendapatan->jenis_pendapatan }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="rincian_jenis_pendapatan" class="col-form-label s-12 col-md-4">Rincian Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{  $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '' }}"  class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                <div class="form-group m-0">
                                                        <label for="kode_rekening" class="col-form-label s-12 col-md-4">Kode Rekening<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" id="kode_rekening" value="{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '' }}" class="form-control r-0 light s-12 col-md-8" autocomplete="off" readonly required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="uraian_retribusi" class="col-form-label s-12 col-md-4">Uraian Retribusi<span class="text-danger ml-1">*</span></label>
                                                        <textarea type="text" rows="3" name="uraian_retribusi" id="uraian_retribusi" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required>{{ $data->uraian_retribusi }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                <div class="form-group m-0">
                                                        <label class="col-form-label s-12 col-md-4">No Bayar<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" value="{{ $data->no_bayar }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nmr_daftar" class="col-form-label s-12 col-md-4">Nomor Daftar<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nmr_daftar" id="nmr_daftar" value="{{ $data->nmr_daftar }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nm_wajib_pajak" class="col-form-label s-12 col-md-4">Nama Wajib Pajak<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nm_wajib_pajak" id="nm_wajib_pajak" value="{{ $data->nm_wajib_pajak }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="alamat_wp" class="col-form-label s-12 col-md-4">Alamat Wajib Pajak<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="alamat_wp" id="alamat_wp" value="{{ $data->alamat_wp }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="kecamatan_id" class="col-form-label s-12 col-md-4">Kecamatan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="kecamatan_id" id="kecamatan_id" value="{{ $data->kecamatan->n_kecamatan }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="kelurahan_id" class="col-form-label s-12 col-md-4">Kelurahan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="kelurahan_id" id="kelurahan_id" value="{{ $data->kelurahan->n_kelurahan }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="lokasi" class="col-form-label s-12 col-md-4">Lokasi<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="lokasi" id="lokasi" value="{{ $data->lokasi }}" readonly placeholder="Contoh: Ruko Sektor 1.2 BSD" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label for="no_skrd" class="col-form-label s-12 col-md-4">Nomor SKRD<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="no_skrd" id="no_skrd" value="{{ $data->no_skrd }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_skrd_awal" class="col-form-label s-12 col-md-4">Tanggal SKRD<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" value="{{ $data->tgl_skrd_awal }}" readonly name="tgl_skrd_awal" id="tgl_skrd_awal" class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_skrd_akhir" class="col-form-label s-12 col-md-4">Jatuh Tempo<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" name="tgl_skrd_akhir" value="{{ $data->tgl_skrd_akhir }}" id="tgl_skrd_akhir" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="nm_ttd" class="col-form-label s-12 col-md-4">Nama Penandatangan<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="nm_ttd" id="nm_ttd" value="{{ $data->nm_ttd }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="tgl_ttd" class="col-form-label s-12 col-md-4">Tanggal TTD<span class="text-danger ml-1">*</span></label>
                                                        <input type="date" name="tgl_ttd" id="tgl_ttd" value="{{ $data->tgl_ttd }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="jumlah_bayar" class="col-form-label s-12 col-md-4">Jumlah Bayar<span class="text-danger ml-1">*</span></label>
                                                        <input type="text" name="jumlah_bayar" id="rupiah1" value="{{ $data->jumlah_bayar }}" readonly class="form-control r-0 light s-12 col-md-8" autocomplete="off" required/>
                                                    </div>
                                                </div>
                                            </div> 
                                            <hr>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group m-0">
                                                        <label class="col-form-label s-12 col-md-4">Status</label>
                                                        <div class="col-md-8 p-0 bg-light">
                                                            <select class="select2 form-control r-0 light s-12" id="status" name="status" autocomplete="off">
                                                                <option value="0">Belum Dibayar</option>
                                                                <option value="1">Sudah Dibayar</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mt-1">
                                                        <label for="tgl_bayar" class="col-form-label s-12 col-md-4">Tanggal Bayar</label>
                                                        <input type="datetime-local" name="tgl_bayar" value="{{ date('Y-m-d\TH:i', strtotime($data->tgl_bayar)) }}" id="tgl_bayar" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="no_bku" class="col-form-label s-12 col-md-4">Nomor BKU</label>
                                                        <input type="text" name="no_bku" value="{{ $data->no_bku }}" id="no_bku" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="chanel_bayar" class="col-form-label s-12 col-md-4">Chanel Bayar</label>
                                                        <input type="text" name="chanel_bayar" value="{{ $data->chanel_bayar }}" readonly id="chanel_bayar" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <!-- <div class="form-group m-0">
                                                        <label for="tgl_bku" class="col-form-label s-12 col-md-4">Tanggal BKU</label>
                                                        <input type="date" name="tgl_bku" value="{{ substr($data->tgl_bku,0,10) }}" id="tgl_bku" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div> -->
                                                    <div class="form-group m-0">
                                                        <label for="ntb" class="col-form-label s-12 col-md-4">NTB</label>
                                                        <input type="text" name="ntb" value="{{ $data->ntb }}" readonly id="ntb" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    
                                                    <div class="form-group m-0">
                                                        <label for="denda" class="col-form-label s-12 col-md-4">Denda</label>
                                                        <input type="text" name="denda" value="{{ $data->denda }}" readonly id="denda" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="diskon" class="col-form-label s-12 col-md-4">Diskon</label>
                                                        <input type="text" name="diskon" value="{{ $data->diskon }}" readonly id="diskon" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
                                                    </div>
                                                    <div class="form-group m-0">
                                                        <label for="total_bayar_bjb" class="col-form-label s-12 col-md-4">Total Bayar Bank</label>
                                                        <input type="text" name="total_bayar_bjb" value="{{ $data->total_bayar_bjb }}" readonly id="total_bayar_bjb" class="form-control r-0 light s-12 col-md-8" autocomplete="off"/>
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
    $('#status').val("{{ $data->status_bayar }}");
    $('#status').trigger('change.select2');

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
                    console.log(data);
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
</script>
@endsection