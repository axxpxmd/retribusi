@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4 class="ml-1">
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
                            <h6 class="card-header"><strong>Data Wajib Pajak</strong></h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>OPD :</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->opd->n_opd }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Jenis Pendapatan :</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->jenis_pendapatan->jenis_pendapatan }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Rincian Jenis Pendapatan :</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->rincian_jenis->rincian_pendapatan }}</label>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Nama :</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->nm_wajib_pajak }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Alama t:</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->alamat_wp }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Lokasi :</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->lokasi }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Kecamatan :</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->kecamatan->n_kecamatan }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Kelurahan :</strong></label>
                                        <label class="col-md-8 s-12">{{ $data->kelurahan->n_kelurahan }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"></label>
                                        <label class="col-md-3 s-12">
                                            <a href="{{ route('skrd.create', array('data_wp_id' =>  Crypt::encrypt($data->id))) }}" class="btn btn-sm btn-primary"><i class="icon-arrow_forward mr-2"></i>Buat SKRD</a>
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
@endsection
@section('script')
<script type="text/javascript">
    // 
</script>
@endsection