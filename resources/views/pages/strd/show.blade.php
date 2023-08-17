@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
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
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-document-list"></i>STRD</a>
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
                            <h6 class="card-header"><strong>Data STRD</strong></h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Nama OPD :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->opd->n_opd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Jenis Pendapatan :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->jenis_pendapatan->jenis_pendapatan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Rincian Jenis Retribusi :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Uraian Retribusi :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->uraian_retribusi }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Nomor Rekening :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Nama TTD :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nm_ttd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>NIP TTD :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nip_ttd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Tanggal TTD :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->isoFormat('D MMMM Y') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Nomor Daftar :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nmr_daftar }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Nama :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nm_wajib_pajak }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Alamat :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->alamat_wp }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Kecamatan :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->kecamatan->n_kecamatan }}</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Kelurahan :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->kelurahan->n_kelurahan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Lokasi :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->lokasi }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Tanggal Pembuatan SKRD :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->isoFormat('D MMMM Y') }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Jatuh Tempo :</strong></label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $tgl_jatuh_tempo)->isoFormat('D MMMM Y') }} | 
                                                    @if ($checkJatuhTempo)
                                                    <span class="badge badge-warning" style="font-size: 10.5px !important">Kadaluarsa</span>
                                                    @else
                                                    <span class="badge badge-success" style="font-size: 10.5px !important">Berlaku</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Nomor STRD :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->no_skrd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Nomor Bayar :</strong></label>
                                                <label class="col-md-8 s-12">{{ $status_ttd ? $data->no_bayar : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Ketetapan :</strong></label>
                                                <label class="col-md-8 s-12">@currency($data->jumlah_bayar)</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Denda  :</label>
                                                <label class="col-md-8 s-12">({{ $kenaikan }}%) &nbsp;@currency($jumlahBunga)</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Diskon  :</strong></label>
                                                @if ($data->status_diskon == 0)
                                                <label class="col-md-8 s-12">({{ $data->diskon }}%) &nbsp;@currency(((int) $data->diskon / 100) * $data->jumlah_bayar)</label>
                                                @else
                                                <label class="col-md-8 s-12">({{ $data->diskon }}%) &nbsp;@currency(((int) $data->diskon / 100) * $data->jumlah_bayar)</label>
                                                @endif
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Total Bayar :</strong></label>
                                                <label class="col-md-8 s-12">@currency($data->total_bayar + $jumlahBunga)</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Virtual Account BJB :</strong></label>
                                                <label class="col-md-8 s-12">{{ $status_ttd ? $data->nomor_va_bjb : '-' }}</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Invoice ID QRIS  :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->invoice_id }}</label>
                                            </div> 
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Status TTD :</strong></label>
                                                <label class="col-md-8">
                                                    @if ($data->status_ttd == 3)
                                                    <span class="badge badge-success">Sudah TTD</span>
                                                    @elseif($data->status_ttd == 0 || $data->status_ttd == 1)
                                                    <span class="badge badge-danger">Belum TTD</span>
                                                    @elseif($data->status_ttd == 2 || $data->status_ttd == 4)
                                                    <span class="badge badge-warning">Sedang Proses TTD</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Jumlah Cetak :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->jumlah_cetak }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Terakhir Cetak Pada :</strong></label>
                                                @if ($data->tgl_cetak_trkhr != null)
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->tgl_cetak_trkhr)->format('d F Y | H:i:s') }}</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Dibuat Oleh :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->created_by }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Diupdate Oleh :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->updated_by }} </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container col-md-6">
                                        <div class="row justify-content-center">
                                            @if (!$checkJatuhTempo)
                                                <div class="col-auto p-1">
                                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#preview-file"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</button>
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
</div>
<!-- Preview File -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="preview-file" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            @if ($status_ttd)
            <iframe src="{{ config('app.sftp_src').$path_sftp.$fileName }}" style="margin-left: -160px !important" width="850px" height="940px"></iframe>
            @else
            <iframe src="{{ route('print.strd', \Crypt::encrypt($data->id)) }}" style="margin-left: -160px !important;" width="850px" height="940px"></iframe>
            @endif
        </div>
    </div>
</div>
<!-- Loading -->
@include('layouts.loading')
@endsection
@section('script')
<script type="text/javascript">
</script>
@endsection