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
            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show text-center bdr-5 col-md-12 container mb-0 mt-0" id="successAlert" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if (count($errors) > 0)
            <div class="alert alert-danger mb-0 mt-0" id="errorAlert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <strong>Whoops Error!</strong>&nbsp;
                <span>You have {{ $errors->count() }} error</span>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif 
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mt-2">
                            <h6 class="card-header"><strong>Data {{ $title }}</strong>@if ($data->status_ttd == 1) | <span class="text-success font-weight-bold">Sudah Ditandatangani</span>@endif</h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Nama OPD :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->opd->n_opd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Jenis Pendapatan:</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->jenis_pendapatan->jenis_pendapatan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Rincian Jenis Retribusi:</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Uraian Retribusi:</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->uraian_retribusi }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Nomor Rekening :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Nama TTD :</strong></label>
                                                @if ($data->nm_ttd != null)
                                                <label class="col-md-8 s-12">{{ $data->nm_ttd }}</label>
                                                @else
                                                <label class="col-md-8 s-12">{{ $data->opd->nm_ttd }}</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>NIP TTD :</strong></label>
                                                @if ($data->nip_ttd != null)
                                                <label class="col-md-8 s-12">{{ $data->nip_ttd }}</label>
                                                @else
                                                <label class="col-md-8 s-12">{{ $data->opd->nip_ttd }}</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Tanggal TTD :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->format('d M Y') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Nomor Daftar:</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nmr_daftar }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Nama Wajib Retribusi:</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nm_wajib_pajak }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Alamat Wajib Retribusi :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->alamat_wp }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Kecamatan :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->kecamatan->n_kecamatan }}</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Kelurahan :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->kelurahan->n_kelurahan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Lokasi :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->lokasi }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Tanggal SKRD:</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->format('d M Y') }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Jatuh Tempo :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d M Y') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Nomor SKRD:</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->no_skrd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Nomor Bayar :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->no_bayar }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Ketetapan :</strong></label>
                                                <label class="col-md-8 s-12">@currency($data->jumlah_bayar)</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Denda :</strong></label>
                                                @if ($data->denda != null)
                                                <label class="col-md-8 s-12">@currency($data->denda)</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Diskon :</strong></label>
                                                @if ($data->diskon != null)
                                                <label class="col-md-8 s-12">({{ $data->diskon }}%) &nbsp;@currency(((int) $data->diskon / 100) * $data->jumlah_bayar)</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Total Bayar :</strong></label>
                                                <label class="col-md-8 s-12">@currency($data->total_bayar)</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Total Bayar BJB:</strong></label>
                                                @if ($data->total_bayar_bjb != null)
                                                <label class="col-md-8 s-12">@currency($data->total_bayar_bjb)</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div> 
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Jumlah Cetak :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->jumlah_cetak }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Terakhir Cetak Pada :</strong></label>
                                                @if ($data->tgl_cetak_trkhr != null)
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->tgl_cetak_trkhr)->format('d M Y | H:i:s') }}</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Dibuat Oleh :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->created_by }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Diupdate Oleh :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->updated_by }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <label class="col-md-2 text-right s-12"></label>
                                        <label class="col-md-3 s-12">
                                            @if ($data->status_ttd == 0)
                                                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#preview-file"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</button>
                                                @if (count($errors) > 0)
                                                <button class="btn btn-sm btn-primary" onclick="alertSend()"><i class="icon-pencil mr-2"></i>TandaTangani</button>
                                                @else
                                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="icon-pencil mr-2"></i>TandaTangani</button>
                                                @endif
                                            @else
                                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#preview-file"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</button>
                                            <a href="{{ config('app.sftp_src').$path_sftp.$fileName }}" target="_blank" class="btn btn-sm btn-primary"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</a>    
                                            @endif
                                        </label>
                                    </div> 
                                </div>
                            </div>
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
                <iframe src="{{ config('app.sftp_src').$path_sftp.$fileName }}" width="850px" height="850px"></iframe>
            </div>
        </div>
    </div>
    <!-- Form TTE -->
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="card">
                    <h6 class="card-header font-weight-bold">Konfirmasi <b>Passphrase</b> Tandatangan Digital</h6>
                    <div class="card-body">
                        <form class="needs-validation" method="POST" action="{{ route('tanda-tangan.tte') }}" enctype="multipart/form-data" novalidate>
                            {{ method_field('POST') }}
                            {{ csrf_field() }} 
                            <input type="hidden" name="id" value="{{ $id }}">
                            <input type="hidden" name="token_godem" value="{{ $token_godem }}">
                            <input type="hidden" name="id_cert" value="{{ $id_cert }}">
                            <input type="hidden" name="nip_ttd" value="{{ $data->nip_ttd }}">
                            <img src="{{ asset('images/iotentik.jpg') }}" class="mx-auto d-block" alt="">
                            <div class="form-row form-inline">
                                <div class="col-md-12">
                                    <div class="form-group m-0">
                                        <label class="col-form-label s-12 col-md-2">Di TTD Oleh</label>
                                        <input type="text" class="form-control r-0 light s-12 col-md-9" value="{{ $data->nm_ttd }}" autocomplete="off" readonly required/>
                                    </div>
                                    <div class="form-group m-0">
                                        <label for="passphrase" class="col-form-label s-12 col-md-2">Passphrase</label>
                                        <input type="password" name="passphrase" id="passphrase" placeholder="Masukan Passphrase" class="form-control r-0 light s-12 col-md-9" autocomplete="off" required/>
                                        <label class="col-form-label s-12 col-md-2"></label>
                                        <div class="invalid-feedback p-0 col-md-9">
                                            Passphrase tidak boleh kosong.
                                        </div>
                                    </div>
                                    <div class="form-group m-0">
                                        <label for="passphrase" class="col-form-label s-12 col-md-2"></label>
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-primary mr-2"><i class="icon-send mr-2"></i>Tandatangani</button>
                                            <button class="btn btn-sm btn-secondary" data-dismiss="modal">Batalkan</button>
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
@endsection
@section('script')
<script type="text/javascript">
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