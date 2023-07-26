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
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-document-list"></i>Data WP</a>
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
                        <div class="card mt-2">
                            <h6 class="card-header font-weight-bold">Data Pemohon Retribusi</h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">OPD :</label>
                                        <label class="col-md-8 s-12">{{ $data->opd->n_opd }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Jenis Pendapatan :</label>
                                        <label class="col-md-8 s-12">{{ $data->jenis_pendapatan->jenis_pendapatan }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Rincian Jenis Pendapatan :</label>
                                        <label class="col-md-8 s-12">{{ $data->rincian_jenis->rincian_pendapatan }}</label>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Nama :</label>
                                        <label class="col-md-8 s-12">{{ $data->nm_wajib_pajak }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Email :</label>
                                        <label class="col-md-8 s-12">{{ $data->email }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">No Telp :</label>
                                        <label class="col-md-8 s-12">{{ $data->no_telp }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Alamat :</label>
                                        <label class="col-md-8 s-12">{{ $data->alamat_wp }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Lokasi :</label>
                                        <label class="col-md-8 s-12">{{ $data->lokasi }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Kecamatan :</label>
                                        <label class="col-md-8 s-12">{{ $data->kecamatan->n_kecamatan }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12 font-weight-bold">Kelurahan :</label>
                                        <label class="col-md-8 s-12">{{ $data->kelurahan->n_kelurahan }}</label>
                                    </div>
                                    <div class="row mt-2">
                                        <label class="col-md-2 text-right s-12"></label>
                                        <label class="col-md-3 s-12">
                                            <a href="{{ route('skrd.create', array('data_wp_id' =>  Crypt::encrypt($data->id))) }}" class="btn btn-sm btn-primary"><i class="icon-arrow_forward mr-2"></i>Buat SKRD</a>
                                        </label>
                                    </div> 
                                    <hr>
                                    <div class="container col-md-4">
                                        <p style="border-radius: 5px; background: #F7F7F7" class="font-weight-bold p-1 m-0"><i class="icon icon-document-file-pdf mr-2"></i>Riwayat Retribusi</p>
                                        <table class="table table-bordered table-striped mt-2">
                                            <thead>
                                                <th class="text-center">#</th>
                                                <th>No SKRD</th>
                                                <th>Tanggal SKRD</th>
                                                <th>Status Bayar</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($riwayatRetribusi as $key => $i)
                                                    <tr>
                                                        <td class="text-center">{{ $key+1 }}</td>
                                                        <td> <a target="_blank" href="{{ route('sts.show', Crypt::encrypt($i->id)) }}">{{ $i->no_skrd }}</a></td>
                                                        <td class="text-center">{{ Carbon\Carbon::createFromFormat('Y-m-d', $i->tgl_skrd_awal)->format('d F Y') }}</td>
                                                        <td class="text-center">
                                                            @if ($i->status_bayar)
                                                                <span class="badge badge-success">Sudah Bayar</span>
                                                            @else
                                                                <span class="badge badge-danger">Belum Bayar</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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
@endsection
@section('script')
<script type="text/javascript">
    // 
</script>
@endsection