@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<style>
    iframe{
        background:#FFFFFF;
        border:1px #ccc solid;
    }
</style>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4>
                        <i class="icon icon-document-list mr-2"></i>
                        Menampilkan Data | {{ $data->nm_wajib_pajak }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-pencil"></i>Tanda Tangan</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            @include('layouts.alerts')
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mt-2">
                            <h6 class="card-header"><strong>Data {{ $title }}</strong>@if ($data->status_ttd == 1 || $data->status_ttd == 3) | <span class="text-success font-weight-bold">Sudah Ditandatangani</span>@endif</h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Nama OPD  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->opd->n_opd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Jenis Pendapatan :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->jenis_pendapatan->jenis_pendapatan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Rincian Jenis Retribusi :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Uraian Retribusi :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->uraian_retribusi }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Nomor Rekening  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Nama TTD  :</strong></label>
                                                @if ($data->nm_ttd != null)
                                                <label class="col-md-8 s-12">{{ $data->nm_ttd }}</label>
                                                @else
                                                <label class="col-md-8 s-12">{{ $data->opd->nm_ttd }}</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>NIP TTD  :</strong></label>
                                                @if ($data->nip_ttd != null)
                                                <label class="col-md-8 s-12">{{ $data->nip_ttd }}</label>
                                                @else
                                                <label class="col-md-8 s-12">{{ $data->opd->nip_ttd }}</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Tanggal TTD  :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->format('d F Y') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Nomor Daftar :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nmr_daftar }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Nama :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nm_wajib_pajak }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Email :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->email }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>No Telp :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->no_telp }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Alamat  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->alamat_wp }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Kecamatan  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->kecamatan->n_kecamatan }}</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Kelurahan  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->kelurahan->n_kelurahan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Lokasi  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->lokasi }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Tanggal SKRD :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->format('d F Y') }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Jatuh Tempo  :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d F Y') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Nomor SKRD :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->no_skrd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Nomor Bayar  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $status_ttd ? $data->no_bayar : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Ketetapan  :</strong></label>
                                                <label class="col-md-8 s-12">@currency($data->jumlah_bayar)</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Denda  :</label>
                                                <label class="col-md-8 s-12">({{ $kenaikan }}%) &nbsp;@currency($jumlahBunga)</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Diskon  :</label>
                                                @if ($data->status_diskon == 0)
                                                <label class="col-md-8 s-12">({{ $data->diskon }}%) &nbsp;@currency(((int) $data->diskon / 100) * $data->jumlah_bayar)</label>
                                                @else
                                                <label class="col-md-8 s-12">({{ $data->diskon }}%) &nbsp;@currency(((int) $data->diskon / 100) * $data->jumlah_bayar)</label>
                                                @endif
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Total Bayar  :</strong></label>
                                                @if (!$jatuh_tempo)
                                                    <label class="col-md-8 s-12">@currency($data->total_bayar)</label>
                                                @else
                                                    <label class="col-md-8 s-12">@currency($data->total_bayar + $jumlahBunga)</label>
                                                @endif
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Virtual Account BJB  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $status_ttd ? $data->nomor_va_bjb : '-' }}</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Invoice ID QRIS  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->invoice_id }}</label>
                                            </div> 
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Status TTD  :</strong></label>
                                                <label class="col-md-8">
                                                    @if ($data->status_ttd == 1 || $data->status_ttd == 3)
                                                    <span class="badge badge-success">Sudah TTD</span>
                                                    @elseif($data->status_ttd == 0)
                                                    <span class="badge badge-danger">Belum TTD</span>
                                                    @elseif($data->status_ttd == 2 || $data->status_ttd == 4)
                                                    <span class="badge badge-warning">Sedang Proses TTD</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Jumlah Cetak  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->jumlah_cetak }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Terakhir Cetak Pada  :</strong></label>
                                                @if ($data->tgl_cetak_trkhr != null)
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->tgl_cetak_trkhr)->format('d F Y | H:i:s') }}</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Dibuat Oleh  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->created_by }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12"><strong>Diupdate Oleh  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->updated_by }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container col-md-6">
                                        <div class="row justify-content-center">
                                            @if ($data->status_ttd == 0 || $data->status_ttd == 2 || $data->status_ttd == 4)
                                                <div class="col-auto p-1">
                                                    <button class="btn btn-sm btn-success mr-1" data-toggle="modal" data-target="#preview-file"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</button>
                                                    <button class="btn btn-sm btn-primary mr-1" data-toggle="modal" data-target="#tte"><i class="icon-pencil mr-2"></i>Tanda Tangan</button>
                                                </div>
                                            @else
                                                <div class="col-auto p-1">
                                                    <button class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#preview-file"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</button>
                                                </div>
                                            @endif
                                            @if ($data->status_bayar == 0)
                                            <div class="col-auto p-1">
                                                <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#restore"><i class="icon-refresh2 mr-2"></i>Kembalikan</button> 
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Kembalikan -->
    <div class="modal fade" id="restore" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="">
                        <p class="font-weight-bold text-black-50">Data akan dikembalikan pada pendataan ?</p>
                    </div>
                    <hr>
                    <div class="text-right">
                        <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                        <a href="{{ route('tanda-tangan.restoreTTD', $id) }}" class="btn btn-sm btn-primary ml-2"><i class="icon-refresh2 mr-2"></i>Kembalikan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Form TTE-->
    <div class="modal fade bd-example-modal-lg" id="tte" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="card">
                    <h6 class="card-header font-weight-bold">Tanda Tangan</h6>
                    <div class="card-body">
                        <form class="needs-validation" method="POST" action="{{ $tte_backup == 1 ? route('tanda-tangan.tteBackup') : route('tanda-tangan.tandaTangan') }}" enctype="multipart/form-data" novalidate>
                            {{ method_field('POST') }}
                            {{ csrf_field() }} 
                            {{-- <div class="text-center justify-content-center row">
                                <div class="col-sm-6">
                                    <div class="justify-content-center row mb-2">
                                        <label class="col-md-2 p-0">
                                            <input type="radio" class="form-control" name="tte" value="bsre" {{ $nik ? 'required' : '' }} {{ $nik ? '-' : 'disabled' }} style="margin-top: 25px !important">
                                            <div class="invalid-feedback p-0">
                                                Pilih TTE.
                                            </div>
                                        </label>
                                        <div class="col-md-6 p-0">  
                                            <div class="border py-2" style="background: {{ $nik ? '' : '#F7F7F7' }}">
                                                <img src="{{ asset('images/bsre.png') }}" width="118" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="text-center row">
                                <div class="col-sm-6">
                                    <div class="justify-content-center row mb-2">
                                        <label class="col-md-2 p-0">
                                            <input type="radio" class="form-control" name="tte" value="bsre" {{ $nik ? 'checked' : '' }} {{ $nik ? 'required' : '' }} {{ $nik ? '-' : 'disabled' }} style="margin-top: 25px !important">
                                            <div class="invalid-feedback p-0">
                                                Pilih TTE.
                                            </div>
                                        </label>
                                        <div class="col-md-6 p-0">  
                                            <div class="border py-2" style="background: {{ $nik ? '' : '#F7F7F7' }}">
                                                <img src="{{ asset('images/bsre.png') }}" width="118" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="justify-content-center row mb-2">
                                        <label class="col-md-2 p-0">
                                            <input type="radio" class="form-control" name="tte" value="aurograf" {{ $nik ? 'required' : '' }} {{ $nik ? '-' : 'disabled' }} style="margin-top: 25px !important">
                                            <div class="invalid-feedback p-0">
                                                Pilih TTE.
                                            </div>
                                        </label>
                                        <div class="col-md-6 p-0">  
                                            <div class="border py-2" style="background: {{ $nik ? '' : '#F7F7F7' }}">
                                                <img src="{{ asset('images/aurograf.png') }}" width="85" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (!$nik)
                                <div class="text-center">
                                    <span class="text-danger font-weight-bold fs-12">Anda belum mempunya sertifikat elektronik BSRE, Silahkan hubungi BAPENDA / DISKOMINFO untuk melakukan pengajuan akun BSRE.</span>
                                </div>
                            @endif
                            @if (!$aurografCerts)
                            <div class="text-center" id="aurograf_msg" style="display: none">
                                <span class="text-danger font-weight-bold fs-12">Anda belum mempunya sertifikat elektronik AUROGRAF, Silahkan hubungi DISKOMINFO untuk melakukan pengajuan akun AUROGRAF.</span>
                            </div>
                            @endif
                            <hr>
                            <input type="hidden" name="id" value="{{ $id }}">
                            <input type="hidden" name="nik" value="{{ $nik }}"> 
                            <input type="hidden" name="nip" value="{{ $nip }}">    
                            @if ($aurografCerts)
                            <div class="row mb-2" id="aurograf_cert" style="display: none">
                                <label for="password" class="col-form-label s-12 col-md-2 font-weight-bold">Sertifikat</label>
                                <div class="col-md-10">
                                    <select class="select2 form-control r-0 s-12" id="aurograf_cert_id" name="aurograf_cert_id" autocomplete="off">
                                        <option value="">Pilih</option>
                                        @foreach ($aurografCerts as $i)
                                            @if (substr($i['cert_expired_at'], 0,10) >= $dateNow)
                                                {{ $expired = false }}
                                            @else
                                                {{ $expired = true }}
                                            @endif
                                            <option style="background: black !important" value="{{ $i['cert_id'] }}" {{ $expired ? ' disabled' : '' }}>{{ $i['cn'] }}  {{ $expired ? ' Expired' : '' }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback p-0">
                                        Silahkan Pilih Sertifikat
                                    </div>
                                </div>
                            </div>
                            @endif 
                            <div class="row mb-2">
                                <label for="password" class="col-form-label s-12 col-md-2 font-weight-bold">Passphrase</label>
                                <div class="col-md-10">
                                    <input type="password" name="passphrase" id="passphrase" placeholder="Masukan Passphrase" class="form-control r-0 s-12" autocomplete="off" required/>
                                    <div class="invalid-feedback p-0">
                                        Passphrase tidak boleh kosong.
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-md-2"></label>
                                <div class="col-md-10">
                                    <button class="btn btn-sm btn-primary mr-2" {{ !$nik ? 'disabled' : '' }}><i class="icon-pencil mr-2"></i>Tandatangani</button>
                                    <button class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Preview File -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="preview-file" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- SKRD -->
            @if ($data->status_ttd == 2)
            <iframe src="{{ route('print.skrd', \Crypt::encrypt($data->id)) }}" style="margin-left: -160px !important" width="850px" height="940px"></iframe>
            @endif
            <!-- STRD -->
            @if ($data->status_ttd == 4)
            <iframe src="{{ route('print.strd', \Crypt::encrypt($data->id)) }}" style="margin-left: -160px !important" width="850px" height="940px"></iframe>
            @endif

            <!-- TTE SKRD/STRD -->
            @if ($data->status_ttd == 1 || $data->status_ttd == 3)
            <iframe src="{{ config('app.sftp_src').$path_sftp.$fileName }}" style="margin-left: -160px !important" width="850px" height="940px"></iframe>
            @endif
        </div>
    </div>
</div>
@include('layouts.loading')
@endsection
@section('script')
<script type="text/javascript">
    $(':radio[name="tte"]').change(function() {
        val = $(this).filter(':checked').val();
        if (val == 'aurograf') {
            $('#aurograf_cert').show();
            $('#aurograf_msg').show();
            $('#aurograf_cert_id').attr('required', true);
        } else {
            $('#aurograf_cert').hide();
            $('#aurograf_msg').hide();
            $('#aurograf_cert_id').attr('required', false);
        }
    });

    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                    if (form.checkValidity()) {
                        $('#loading').modal('show');   
                    }
                }, false)
            })
    })()

    function alertSend(){
        $.confirm({
            title: 'INFO',
            content: 'Terdapat Error, cek error atau hubungi administrator.',
            icon: 'icon icon-info',
            theme: 'modern',
            closeIcon: true,
            animation: 'scale',
            autoClose: 'ok|5000',
            type: 'red',
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
    }
</script>
@endsection