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
                        <div id="alert"></div>
                        <div class="card mt-2">
                            <h6 class="card-header font-weight-bold">Data STS</h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Nama OPD :</label>
                                                <label class="col-md-8 s-12">{{ $data->opd->n_opd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Jenis Pendapatan :</label>
                                                <label class="col-md-8 s-12">{{ $data->jenis_pendapatan->jenis_pendapatan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Rincian Jenis Retribusi :</label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Uraian Retribusi :</label>
                                                <label class="col-md-8 s-12">{{ $data->uraian_retribusi }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Nomor Rekening :</label>
                                                <label class="col-md-8 s-12">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Nama TTD :</label>
                                                @if ($data->nm_ttd != null)
                                                <label class="col-md-8 s-12">{{ $data->nm_ttd }}</label>
                                                @else
                                                <label class="col-md-8 s-12">{{ $data->opd->nm_ttd }}</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">NIP TTD :</label>
                                                @if ($data->nip_ttd != null)
                                                <label class="col-md-8 s-12">{{ $data->nip_ttd }}</label>
                                                @else
                                                <label class="col-md-8 s-12">{{ $data->opd->nip_ttd }}</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Tanggal TTD :</label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->isoFormat('D MMMM Y') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Nomor Daftar :</label>
                                                <label class="col-md-8 s-12">{{ $data->nmr_daftar }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Nama :</label>
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
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Alamat :</label>
                                                <label class="col-md-8 s-12">{{ $data->alamat_wp }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Kecamatan :</label>
                                                <label class="col-md-8 s-12">{{ $data->kecamatan->n_kecamatan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Kelurahan :</label>
                                                <label class="col-md-8 s-12">{{ $data->kelurahan->n_kelurahan }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Lokasi :</label>
                                                <label class="col-md-8 s-12">{{ $data->lokasi }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Tanggal SKRD :</label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->isoFormat('D MMMM Y') }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Jatuh Tempo :</label>
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->isoFormat('D MMMM Y') }}</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Nomor SKRD :</label>
                                                <label class="col-md-8 s-12">{{ $data->no_skrd }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Nomor Bayar :</label>
                                                <label class="col-md-8 s-12">{{ $status_ttd ? $data->no_bayar : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Ketetapan :</label>
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
                                                <label class="col-md-4 text-right s-12 font-weight-bold"><strong>Total Bayar :</strong></label>
                                                <label class="col-md-8 s-12">@currency($data->total_bayar + $jumlahBunga)</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Virtual Account BJB :</label>
                                                <label class="col-md-8 s-12">{{ $status_ttd ? $data->nomor_va_bjb : '-' }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Invoice ID QRIS  :</label>
                                                <label class="col-md-8 s-12">{{ $data->invoice_id }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Status Bayar :</label>
                                                <label class="col-md-8">
                                                    @if ($data->status_bayar == 1)
                                                    <span class="badge badge-success">Sudah bayar</span>
                                                    @else
                                                    <span class="badge badge-danger">Belum Bayar</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Tanggal Bayar :</label>
                                                @if ($data->tgl_bayar != null)
                                                <label class="col-md-8 s-12">{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->tgl_bayar)->format('d F Y | H:i:s') }}</label>
                                                @else
                                                <label class="col-md-8 s-12">-</label>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">NO BKU :</label>
                                                <label class="col-md-8 s-12">{{ $data->no_bku != null ? $data->no_bku : '-'}}</label>
                                            </div>
                                            @if ($data->file_pendukung)
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">File Pendukung :</label>
                                                <label class="col-md-8 s-12">
                                                    <a href="{{ config('app.sftp_src').'file_pendukung/'.$data->file_pendukung }}" target="_blank" class="fs-16"><span class="badge badge-primary"><i class="icon-document-file-pdf2 mr-2"></i>Lihat File</span></a>
                                                </label>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Channel Bayar :</label>
                                                <label class="col-md-8 s-12">{{ $data->chanel_bayar != null ? $data->chanel_bayar : '-'}}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">NTB :</label>
                                                <label class="col-md-8 s-12">{{ $data->ntb != null ? $data->ntb : '-'}}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Total Bayar BJB :</label>
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
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Status TTD :</label>
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
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Jumlah Cetak :</label>
                                                <label class="col-md-8 s-12">{{ $data->jumlah_cetak }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 text-right s-12 font-weight-bold">Terakhir Cetak Pada :</label>
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
                                                <label class="col-md-8 s-12">{{ $data->created_by }} | {{ $data->created_at }}</label>
                                            </div>
                                            <div class="row">
                                                <label class="col-md-4 font-weight-bold text-right s-12">Diupdate Oleh :</label>
                                                <label class="col-md-8 s-12">{{ $data->updated_by }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="container col-md-6">
                                        <div class="row justify-content-center">
                                            @if ($data->status_bayar == 1)
                                                <div class="col-auto p-1">
                                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#batal_bayar"><i class="icon-cancel mr-2"></i>Batal Bayar</button>
                                                </div>
                                            @endif
                                            @if ($data->status_ttd == 1 || $data->status_ttd == 3)
                                                <div class="col-auto p-1">
                                                    <a href="{{ config('app.sftp_src').$path_sftp.$fileName }}" target="blank" class="btn btn-primary btn-sm"><i class="icon-document-file-pdf2 mr-2"></i>File TTD SKRD</a>
                                                </div>
                                                @if ($data->status_bayar == 1)
                                                    <!-- Send Email -->
                                                    <div class="col-auto p-1">
                                                        <a href="#" data-toggle="modal" data-target="#sendEmail" class="btn btn-sm btn-success"><i class="icon-envelope mr-2"></i>Kirim STS via Email</a>
                                                    </div>
                                                    <!-- Send WA -->
                                                    <div class="col-auto p-1">
                                                        <a href="#" data-toggle="modal" data-target="#sendWA" class="btn btn-sm btn-success"><i class="icon-envelope mr-2"></i>Kirim STS via WA</a>
                                                    </div>
                                                @endif
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
<!-- Batal Bayar -->
<div class="modal fade" id="batal_bayar" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="">
                    <p class="font-weight-bold text-black-50">Apakah yakin ingin membatalkan pembayaran ini ?</p>
                </div>
                <hr>
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Tidak</button>
                    <a href="{{ route('sts.batalBayar', $data->id) }}" class="btn btn-sm btn-primary ml-2" id="kirimTTD"><i class="icon-check mr-2"></i>Batalkan</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Send Email -->
<div class="modal fade" id="sendEmail" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row mb-2">
                        <label for="nm_wajib_pajak" class="col-form-label col-sm-3 s-12 font-weight-bold font-weight-bold">Nama</label>
                        <div class="col-sm-9">
                            <input type="text" name="nm_wajib_pajak" id="nm_wajib_pajak" value="{{ $data->nm_wajib_pajak }}" disabled class="form-control r-0 s-12" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="email" class="col-form-label col-sm-3 s-12 font-weight-bold font-weight-bold">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" id="email" value="{{ $data->email }}" class="form-control r-0 s-12" autocomplete="off"/>
                        </div>
                    </div>
                    <p class="font-weight-bold text-black-50">Apakah anda yakin ingin mengirim file STS ini ?</p>
                </div>
                <hr>
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                    <a onclick="sendEmail({{ $data->id }})" class="btn btn-sm btn-primary ml-2" id="kirimTTD"><i class="icon-send mr-2"></i>Kirim</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Send WA -->
<div class="modal fade" id="sendWA" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row mb-2">
                        <label for="nm_wajib_pajak" class="col-form-label col-sm-3 s-12 font-weight-bold font-weight-bold">Nama</label>
                        <div class="col-sm-9">
                            <input type="text" name="nm_wajib_pajak" id="nm_wajib_pajak" value="{{ $data->nm_wajib_pajak }}" disabled class="form-control r-0 s-12" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label for="no_telp" class="col-form-label col-sm-3 s-12 font-weight-bold font-weight-bold">No Telp</label>
                        <div class="col-sm-9">
                            <input type="email" name="no_telp" id="no_telp" value="{{ $data->no_telp }}" class="form-control r-0 s-12" autocomplete="off"/>
                        </div>
                    </div>
                    <p class="font-weight-bold text-black-50">Apakah anda yakin ingin mengirim file STS ini ?</p>
                </div>
                <hr>
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                    <a onclick="sendWA({{ $data->id }})" class="btn btn-sm btn-primary ml-2" id="kirimTTD"><i class="icon-send mr-2"></i>Kirim</a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.loading')
@endsection
@section('script')
<script type="text/javascript">
    // Send WA
    function sendWA(id){
        $('#loading').modal('show');
        $('#sendWA').modal('toggle');
        no_telp = $('#no_telp').val();
        url = "{{ route('sendWASTS', ':id') }}?no_telp=".replace(':id', id)+no_telp;
        $.get(url, function(data){
            $('#loading').modal('toggle');
            console.log(data);
            if (data.status === 200) {
                $('#loading').modal('toggle');
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
                            keys: ['enter']
                        }
                    }
                });
            }else{
                $('#loading').modal('toggle');
                $('#alert').html("<div role='alert' class='alert alert-danger alert-dismissible'><butto type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></butto font-weight-boldn>Error! " + data.message + "</div>");
            }
        }, 'JSON');
    }

    // Send Email
    function sendEmail(id){
        $('#loading').modal('show');
        $('#sendEmail').modal('toggle');
        email = $('#email').val();
        url = "{{ route('sendEmailSTS', ':id') }}?email=".replace(':id', id)+email;
        $.get(url, function(data){
            $('#loading').modal('toggle');
            console.log(data);
            if (data.status === 200) {
                $('#loading').modal('toggle');
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
                            keys: ['enter']
                        }
                    }
                });
            }else{
                $('#loading').modal('toggle');
                $('#alert').html("<div role='alert' class='alert alert-danger alert-dismissible'><butto type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></butto font-weight-boldn>Error! " + data.message + "</div>");
            }
        }, 'JSON');
    }
</script>
@endsection
