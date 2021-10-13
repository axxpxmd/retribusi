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
    .mr-n15{
        margin-right: -15px !important
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
            <div class="tab-pane animated fadeInUpShort show p-0 active" id="v-pills-1">
                @role('super-admin')
                <!-- Atas -->
                <div class="row p-0 col-md-12 mt-3">
                    <div class="col-md-7">
                        <div class="card no-b" style="height: 374px !important">
                            <h6 class="card-header bg-white font-weight-bold text-black">Pendapatan</h6>
                            <div class="card-body">
                                <div class="">
                                    <table class="table table-striped">
                                        <thead >
                                            <tr class="text-black">
                                                <th>No</th>
                                                <th>Jenis Pendapatan</th>
                                                <th>Target</th>
                                                <th>Diterima</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jenisPendapatan as $index => $i)
                                            <tr>
                                                <td class="text-center">{{  $index + $jenisPendapatan->firstItem() }}</td>
                                                <td>{{ $i->jenis_pendapatan }}</td>
                                                <td>@currency($i->target_pendapatan)</td>
                                                <td>@currency($i->diterima)</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div>
                                        {{ $jenisPendapatan->links() }}
                                    </div>
                                </div>    
                            </div>
                        </div>
                    </div>
                    <div class="p-0 col-md-5">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header bg-white font-weight-bold text-black">Total SKRD</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <i class="icon-taxes amber-text fs-40"></i>
                                            <p class="fs-32 mt-3 mb-0"><span class="badge badge-pill badge-light sc-counter">{{ $totalSKRD }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header bg-white font-weight-bold text-black">Total STRD</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <i class="icon-calendar-times-o text-danger fs-40"></i>
                                            <p class="fs-32 mt-3 mb-0"><span class="badge badge-pill badge-light sc-counter">{{ $totalSTRD }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header bg-white font-weight-bold text-black">Total STS</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <i class="icon-pay-point text-primary fs-40"></i>
                                            <p class="fs-32 mt-3 mb-0"><span class="badge badge-pill badge-light sc-counter">{{ $totalSTS }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header bg-white font-weight-bold text-black">Total Wajib Retribusi</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <i class="icon-group text-success fs-40"></i>
                                            <p class="fs-32 mt-3 mb-0"><span class="badge badge-pill badge-light sc-counter">{{ $totalWR }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tengah -->
                @include('pages.dashboard.card1')
                <!-- Bawah -->
                <div class="row p-0 col-md-12 my-3">
                    <div class="col-md-8">
                        @include('pages.dashboard.chartDiagram')
                    </div>
                    <div class="p-0 col-md-4">
                        @include('pages.dashboard.pieChart')
                    </div>
                </div>
                @endrole
            </div>
        </div>
    </div>
</div>
@endsection
