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
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-document-list"></i>SKRD</a>
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
                        <div class="card mt-2">
                            <h6 class="card-header font-weight-bold">Data SKRD</h6>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Nama OPD :</label>
                                            <label class="col-md-8 s-12">{{ $data->opd->n_opd }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Jenis Pendapatan :</label>
                                            <label class="col-md-8 s-12">{{ $data->jenis_pendapatan->jenis_pendapatan }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Rincian Jenis Retribusi :</label>
                                            <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '-' }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Uraian Retribusi :</label>
                                            <label class="col-md-8 s-12">{{ $data->uraian_retribusi }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Nomor Rekening :</label>
                                            <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Nama TTD :</label>
                                            @if ($data->nm_ttd != null)
                                            <label class="col-md-8 s-12">{{ $data->nm_ttd }}</label>
                                            @else
                                            <label class="col-md-8 s-12">{{ $data->opd->nm_ttd }}</label>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">NIP TTD :</label>
                                            @if ($data->nip_ttd != null)
                                            <label class="col-md-8 s-12">{{ $data->nip_ttd }}</label>
                                            @else
                                            <label class="col-md-8 s-12">{{ $data->opd->nip_ttd }}</label>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Tanggal TTD :</label>
                                            <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->format('d F Y') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Nomor Daftar :</label>
                                            <label class="col-md-8 s-12">{{ $data->nmr_daftar }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Nama :</label>
                                            <label class="col-md-8 s-12">{{ $data->nm_wajib_pajak }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Email :</label>
                                            <label class="col-md-8 s-12">{{ $data->email }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">No Telp :</label>
                                            <label class="col-md-8 s-12">{{ $data->no_telp }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Alamat :</label>
                                            <label class="col-md-8 s-12">{{ $data->alamat_wp }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Kecamatan :</label>
                                            <label class="col-md-8 s-12">{{ $data->kecamatan->n_kecamatan }}</label>
                                        </div> 
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Kelurahan :</label>
                                            <label class="col-md-8 s-12">{{ $data->kelurahan->n_kelurahan }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Lokasi :</label>
                                            <label class="col-md-8 s-12">{{ $data->lokasi }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Tanggal SKRD :</label>
                                            <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->format('d F Y') }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Jatuh Tempo :</label>
                                            <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d F Y') }}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Nomor SKRD :</label>
                                            <label class="col-md-8 s-12">{{ $data->no_skrd }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Nomor Bayar :</label>
                                            <label class="col-md-8 s-12">{{ $status_ttd ? $data->no_bayar : '-' }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Ketetapan :</label>
                                            <label class="col-md-8 s-12">@currency($data->jumlah_bayar)</label>
                                        </div> 
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Denda :</label>
                                            @if ($data->status_denda == 0)
                                            <label class="col-md-8 s-12">(Tidak) @currency($data->denda)</label>
                                            @else
                                            <label class="col-md-8 s-12">(Ya) @currency($data->denda)</label>
                                            @endif
                                        </div> 
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Diskon :</label>
                                            @if ($data->status_diskon == 0)
                                            <label class="col-md-8 s-12">({{ $data->diskon }}%) &nbsp;@currency(((int) $data->diskon / 100) * $data->jumlah_bayar)</label>
                                            @else
                                            <label class="col-md-8 s-12">({{ $data->diskon }}%) &nbsp;@currency(((int) $data->diskon / 100) * $data->jumlah_bayar)</label>
                                            @endif
                                        </div> 
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Total Bayar :</label>
                                            <label class="col-md-8 s-12">@currency($data->total_bayar)</label>
                                        </div> 
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Virtual Account BJB :</label>
                                            <label class="col-md-8 s-12">{{ $status_ttd ? $data->nomor_va_bjb : '-' }}</label>
                                        </div> 
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Invoice ID QRIS :</label>
                                            <label class="col-md-8 s-12">{{ $data->invoice_id }}</label>
                                        </div> 
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Status TTD :</label>
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
                                            <label class="col-md-4 font-weight-bold text-right s-12">Jumlah Cetak :</label>
                                            <label class="col-md-8 s-12">{{ $data->jumlah_cetak }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Terakhir Cetak Pada :</label>
                                            @if ($data->tgl_cetak_trkhr != null)
                                            <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->tgl_cetak_trkhr)->format('d F Y | H:i:s') }}</label>
                                            @else
                                            <label class="col-md-8 s-12">-</label>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Dibuat Oleh :</label>
                                            <label class="col-md-8 s-12">{{ $data->created_by }}</label>
                                        </div>
                                        <div class="row">
                                            <label class="col-md-4 font-weight-bold text-right s-12">Diupdate Oleh :</label>
                                            <label class="col-md-8 s-12">{{ $data->updated_by }}</label>
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
@include('layouts.loading')
@endsection
@section('script')
<script type="text/javascript">
    // 
</script>
@endsection