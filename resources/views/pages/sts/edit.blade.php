@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
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
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Edit Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="PATCH"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('PATCH') }}
                                    <input type="hidden" id="id" name="id" value="{{ \Crypt::encrypt($data->id) }}"/>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <label for="n_opd" class="col-form-label s-12 col-sm-4 text-right">Nama OPD</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="n_opd" id="n_opd" value="{{ $data->opd->n_opd }}"  class="form-control s-12" autocomplete="off" readonly required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="rincian_jenis_pendapatan" class="col-form-label s-12 col-sm-4 text-right">Jenis Pendapatan</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{ $data->jenis_pendapatan->jenis_pendapatan }}"  class="form-control s-12" autocomplete="off" readonly required/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="rincian_jenis_pendapatan" class="col-form-label s-12 col-sm-4 text-right">Rincian Jenis Pendapatan</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="rincian_jenis_pendapatan" id="rincian_jenis_pendapatan" value="{{  $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '' }}"  class="form-control s-12" autocomplete="off" readonly required/>
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                        <div class="row mb-2">
                                                <label for="kode_rekening" class="col-form-label s-12 col-sm-4 text-right">Kode Rekening</label>
                                                <div class="col-md-8">
                                                    <input type="text" id="kode_rekening" value="{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '' }}" class="form-control s-12" autocomplete="off" readonly required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="uraian_retribusi" class="col-form-label s-12 col-sm-4 text-right">Uraian Retribusi</label>
                                                <div class="col-md-8">
                                                    <textarea type="text" rows="3" name="uraian_retribusi" id="uraian_retribusi" readonly class="form-control s-12" autocomplete="off" required>{{ $data->uraian_retribusi }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6">
                                        <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">No Bayar</label>
                                                <div class="col-md-8">
                                                    <input type="text" value="{{ $data->no_bayar }}" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="nmr_daftar" class="col-form-label s-12 col-sm-4 text-right">Nomor Daftar</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="nmr_daftar" id="nmr_daftar" value="{{ $data->nmr_daftar }}" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="nm_wajib_pajak" class="col-form-label s-12 col-sm-4 text-right">Nama</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="nm_wajib_pajak" id="nm_wajib_pajak" value="{{ $data->nm_wajib_pajak }}" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="alamat_wp" class="col-form-label s-12 col-sm-4 text-right">Alamat</label>
                                                <div class="col-md-8">
                                                    <textarea name="alamat_wp" rows="3" id="alamat_wp" readonly class="form-control s-12" autocomplete="off" required>{{ $data->alamat_wp }}</textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="lokasi" class="col-form-label s-12 col-sm-4 text-right">Lokasi</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="lokasi" id="lokasi" value="{{ $data->lokasi }}" readonly placeholder="Contoh: Ruko Sektor 1.2 BSD" class="form-control s-12" autocomplete="off" required/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="kecamatan_id" class="col-form-label s-12 col-sm-4 text-right">Kecamatan</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kecamatan_id" id="kecamatan_id" value="{{ $data->kecamatan->n_kecamatan }}" readonly class="form-control s-12" autocomplete="off"/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="kelurahan_id" class="col-form-label s-12 col-sm-4 text-right">Kelurahan</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="kelurahan_id" id="kelurahan_id" value="{{ $data->kelurahan->n_kelurahan }}" readonly class="form-control s-12" autocomplete="off"/>
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <label for="no_skrd" class="col-form-label s-12 col-sm-4 text-right">Nomor SKRD</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="no_skrd" id="no_skrd" value="{{ $data->no_skrd }}" readonly class="form-control s-12" autocomplete="off"/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="tgl_skrd_awal" class="col-form-label s-12 col-sm-4 text-right">Tanggal SKRD</label>
                                                <div class="col-md-8">
                                                    <input type="date" value="{{ $data->tgl_skrd_awal }}" readonly name="tgl_skrd_awal" id="tgl_skrd_awal" class="form-control s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="tgl_skrd_akhir" class="col-form-label s-12 col-sm-4 text-right">Jatuh Tempo</label>
                                                <div class="col-md-8">
                                                    <input type="date" name="tgl_skrd_akhir" value="{{ $data->tgl_skrd_akhir }}" id="tgl_skrd_akhir" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="nm_ttd" class="col-form-label s-12 col-sm-4 text-right">Penanda Tangan</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="nm_ttd" id="nm_ttd" value="{{ $data->nm_ttd }}" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="nip_ttd" class="col-form-label s-12 col-sm-4 text-right">NIP Penandatangan</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="nip_ttd" id="nip_ttd" value="{{ $data->nip_ttd }}" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="tgl_ttd" class="col-form-label s-12 col-sm-4 text-right">Tanggal TTD</label>
                                                <div class="col-md-8">
                                                    <input type="date" name="tgl_ttd" id="tgl_ttd" value="{{ $data->tgl_ttd }}" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="jumlah_bayar" class="col-form-label s-12 col-sm-4 text-right">Ketetapan</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="jumlah_bayar" value="{{ $data->jumlah_bayar }}" readonly class="form-control s-12" autocomplete="off" required/>
                                                </div>    
                                            </div>
                                        </div>
                                    </div> 
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">Status Bayar<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-8">
                                                    <select class="select2 form-control s-12" id="status_bayar" name="status_bayar" autocomplete="off">
                                                        <option value="0">Pilih</option>
                                                        <option value="1" {{ $data->status_bayar == 1 ? 'selected' : '' }}>Sudah Dibayar</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="tgl_bayar" class="col-form-label s-12 col-sm-4 text-right">Tanggal Bayar<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="datetime-local" max="{{ $now }}" name="tgl_bayar" {{ $data->status_bayar == 1 ? 'readonly' : '' }} {{ $readonly }} value="{{ $data->tgl_bayar != null ? date('Y-m-d\TH:i', strtotime($data->tgl_bayar)) : $now }}" class="form-control s-12" autocomplete="off"/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label for="no_bku" class="col-form-label s-12 col-sm-4 text-right">Nomor BKU</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="no_bku" value="{{ $data->no_bku }}" {{ $readonly }} id="no_bku" class="form-control s-12" autocomplete="off"/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="chanel_bayar" class="col-form-label s-12 col-sm-4 text-right">Chanel Bayar<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-8">
                                                    <select class="select2 form-control s-12" id="chanel_bayar" name="chanel_bayar" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        <option value="Virtual Account" {{ $data->chanel_bayar == 'Virtual Account' ? 'selected' : '' }}>Virtual Account</option>
                                                        <option value="ATM" {{ $data->chanel_bayar == 'ATM' ? 'selected' : '' }}>ATM</option>
                                                        <option value="BJB MOBILE" {{ $data->chanel_bayar == 'BJB MOBILE' ? 'selected' : '' }}>BJB MOBILE</option>
                                                        <option value="TELLER" {{ $data->chanel_bayar == 'TELLER' ? 'selected' : '' }}>TELLER</option>
                                                        <option value="QRIS" {{ $data->chanel_bayar == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                                                        <option value="Bendahara OPD" {{ $data->chanel_bayar == 'Bendahara OPD' ? 'selected' : '' }}>Bendahara OPD</option>
                                                        <option value="Transfer RKUD" {{ $data->chanel_bayar == 'Transfer RKUD' ? 'selected' : '' }}>Transfer RKUD</option>
                                                        <option value="RTGS/SKN" {{ $data->chanel_bayar == 'RTGS/SKN' ? 'selected' : '' }}>RTGS/SKN</option>
                                                        <option value="Lainnya" {{ $data->chanel_bayar == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <label for="ntb" class="col-form-label s-12 col-sm-4 text-right">NTB</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="ntb" value="{{ $data->ntb }}" {{ $readonly }} id="ntb" class="form-control s-12" autocomplete="off"/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="denda" class="col-form-label s-12 col-sm-4 text-right">Denda</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="denda" value="{{ $jumlahBunga }}" {{ $readonly }} id="rupiah1" onkeyup="totalBayarBJB()" class="form-control s-12" autocomplete="off"/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="diskon" class="col-form-label s-12 col-sm-4 text-right">Diskon</label>
                                                <div class="col-md-2 mb-5-m">
                                                    <input type="text" name="diskon" value="{{ $data->diskon }} %" readonly id="diskon" class="form-control s-12" autocomplete="off"/>
                                                </div> 
                                                <div class="col-md-6">
                                                    <input type="text" name="jumlah_diskon" value="@currency($data->diskon / 100 * $data->jumlah_bayar )" readonly id="diskon" class="form-control s-12" autocomplete="off"/>
                                                </div>    
                                            </div>
                                            <div class="row mb-2">
                                                <label for="total_bayar_bjb" class="col-form-label s-12 col-sm-4 text-right">Total Bayar Bank<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-8">
                                                    <input type="text" name="total_bayar_bjb" {{ $data->status_bayar == 1 ? 'readonly' : '' }} value="{{ $data->total_bayar_bjb }}" {{ $readonly }} id="rupiah2" class="form-control s-12" autocomplete="off" required/>
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8">
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="icon-save mr-2"></i>Simpan Perubahan</button>
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
            $('#rupiah2').val("{{ $data->total_bayar }}")
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
                                    window.location.href = "{{ route('sts.show', Crypt::encrypt($id)) }}";
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