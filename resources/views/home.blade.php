@extends('layouts.app')
@section('title', '| Dashboard  ')
@section('content')
@php
    $opd_id = Auth::user()->pengguna->opd_id;
@endphp
<style>
    .bb-n{
        border-bottom: none !important
    }
</style>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-dashboard mr-2"></i> 
                        Dashboard
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-1">
                @role('super-admin|admin-bjb')
                    @include('pages.dashboard.card1')
                @endrole
                @role('admin-opd|bendahara-opd|operator-opd')
                    @include('pages.dashboard.card2')
                @endrole
                @role('super-admin|admin-bjb')
                <div class="row p-0 col-md-12">
                    <div class="col-md-8">
                        @include('pages.dashboard.chartDiagram')
                    </div>
                    <div class="p-0 col-md-4">
                        @include('pages.dashboard.pieChart')
                    </div>
                </div>
                <div class="p-0 col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header">
                            <h6>Transaksi Hari Ini</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead >
                                        <tr>
                                            <th class="bb-n text-center" width="4%">NO</th>
                                            <th class="bb-n" width="10%">NO SKRD</th>
                                            <th class="bb-n" width="10%">Nomor Bayar</th>
                                            <th class="bb-n" width="31%">Nama Dinas</th>
                                            <th class="bb-n" width="25%">Jenis Retribusi</th>
                                            <th class="bb-n" width="10%">Tanggal SKRD</th>
                                            <th class="bb-n" width="10%">Ketetapan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($todayDatas as $index => $i)
                                        <tr>
                                            <td class="text-center">{{ $index+1 }}</td>
                                            <td>{{ $i->no_skrd }}</td>
                                            <td>{{ $i->no_bayar }}</td>
                                            <td>{{ $i->opd->n_opd }}</td>
                                            <td>{{ $i->jenis_pendapatan->jenis_pendapatan }}</td>
                                            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d', $i->tgl_skrd_awal)->format('d M Y') }}</td>
                                            <td>Rp. {{ number_format($i->total_bayar) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data hari ini</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</div>
@endsection
