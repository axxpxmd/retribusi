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
                        Menampilkan {{ $title }} | {{ $data->n_opd }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-document-list"></i>OPD</a>
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
                            <h6 class="card-header"><strong>Data OPD</strong></h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Kode:</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->kode }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Nama OPD:</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->n_opd }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Inisial:</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->initial }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Alamat:</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->alamat }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>NPWPD:</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->npwpd }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Penanda Tangan :</strong></label>
                                        <label class="col-md-10 p-0 s-12">
                                            <ol style="margin-left: -15px ">
                                                @forelse  ($penanda_tangans as $i)
                                                <li class="mb-2 s-12">{{ $i->user->pengguna->full_name }} [ {{ $i->user->pengguna->nip }} ]</li>
                                                @empty
                                                <span>-</span>
                                                @endforelse
                                            </ol>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-2">
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Jenis Pendapatan :</strong></label>
                                        <label class="col-md-10 p-0 s-12">
                                            <ol style="margin-left: -15px ">
                                                @forelse  ($jenis_pendaptans as $i)
                                                <li class="mb-2 s-12">{{ $i->jenis_pendapatan->jenis_pendapatan }}</li>
                                                @empty
                                                <span>-</span>
                                                @endforelse
                                            </ol>
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

</script>
@endsection