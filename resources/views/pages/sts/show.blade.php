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
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-document-list"></i>STS</a>
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
                            <h6 class="card-header"><strong>Data STS</strong></h6>
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
                                                <label class="col-md-4 text-right s-12"><strong>Virtual Account BJB :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->nomor_va_bjb }}</label>
                                            </div> 
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Status Bayar:</strong></label>
                                                <label class="col-md-8">
                                                    @if ($data->status_bayar == 1)
                                                    <span class="badge badge-success">Sudah bayar</span>
                                                    @else
                                                    <span class="badge badge-danger">Belum Bayar</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Tanggal Bayar:</strong></label>
                                                @if ($data->tgl_bayar != null)
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->tgl_bayar)->format('d M Y | H:i:s') }}</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>NO BKU :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->no_bku != null ? $data->no_bku : '-'}}</label>
                                            </div> 
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>Channel Bayar :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->chanel_bayar != null ? $data->chanel_bayar : '-'}}</label>
                                            </div> 
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12"><strong>NTB :</strong></label>
                                                <label class="col-md-8 s-12">{{ $data->ntb != null ? $data->ntb : '-'}}</label>
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
                                                <label class="col-md-4 text-right s-12"><strong>Status TTD :</strong></label>
                                                <label class="col-md-8">
                                                    @if ($data->status_ttd == 1)
                                                    <span class="badge badge-success">Sudah TTD</span>
                                                    @elseif($data->status_ttd == 0)
                                                    <span class="badge badge-danger">Belum TTD</span>
                                                    @elseif($data->status_ttd == 2)
                                                    <span class="badge badge-warning">Sedang Proses TTD</span>
                                                    @endif
                                                </label>
                                            </div>
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
                                            <button class="btn btn-sm btn-primary mr-1" data-toggle="modal" data-target="#preview-file"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</button> 
                                            @if ($data->status_bayar == 1)
                                                <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#batal_bayar"><i class="icon-cancel mr-2"></i>Batal Bayar</button>
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
</div>
<div class="modal fade" id="batal_bayar" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="">
                    <p class="font-weight-bold text-black-50">Apakah yakin ingin membatalkan pembayaran ini ?</p>
                </div>
                <hr>
                <div class="text-right">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Tidak</button>
                    <a href="{{ route('sts.batalBayar', $data->id) }}" class="btn btn-sm btn-primary ml-2" id="kirimTTD"><i class="icon-check mr-2"></i>Batalkan</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Preview File -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="preview-file" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <iframe src="{{ route('sts.report', \Crypt::encrypt($data->id)) }}" style="margin-left: -160px !important" width="850px" height="940px"></iframe>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    // 
</script>
@endsection