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
    .mr-n30{
        margin-right: -30px !important
    }

    .table-wrapper {
        max-height: 340px;
        overflow: auto;
        display:inline-block;
    }
    .table-earnings {
        background: #F3F5F6;
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
                <div class="row p-0 col-md-12 mt-3">
                    <div class="col-md-7 animate__animated animate__backInDown">
                        <div class="card no-b" style="height: 435px !important">
                            <h6 class="card-header bg-white font-weight-bold text-black">Pendapatan {{ $time->year }}</h6>
                            <div class="card-body">
                                <div class="table-wrapper">
                                    <table class="table fs-12 table-striped" style="width:100%">
                                        <thead>
                                            <tr class="text-black">
                                                <th>No</th>
                                                <th>Jenis Pendapatan</th>
                                                <th>Target</th>
                                                <th>Ketetapan</th>
                                                <th>Diterima</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($targetPendapatan as $index => $i)
                                            <tr>
                                                <td class="text-center">{{  $index+1 }}</td>
                                                <td>{{ $i->jenis_pendapatan }}</td>
                                                <td>@currency($i->target_pendapatan)</td>
                                                <td>@currency($i->ketetapan)</td>
                                                <td>@currency($i->diterima)</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>    
                            </div>
                        </div>
                    </div>
                    <div class="p-0 col-md-5 animate__animated animate__backInDown">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header font-weight-bold text-white" style="background: #FFCE3B">Total SKRD</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="row justify-content-center">
                                                <div class="col-auto">
                                                    <i class="icon-taxes amber-text fs-40"></i>
                                                </div>
                                                <div class="col-auto">
                                                    <p class="fs-22 mt-1 text-black">{{ $totalSKRD }}</p>
                                                </div>
                                            </div>
                                            <p class="fs-32 mt-3"><span class="badge badge-pill badge-light ">Rp. {{ number_format($totalSKRDduit) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header bg-danger font-weight-bold text-white">Total STRD</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="row justify-content-center">
                                                <div class="col-auto">
                                                    <i class="icon-calendar-times-o text-danger fs-40"></i>
                                                </div>
                                                <div class="col-auto">
                                                    <p class="fs-22 mt-1 text-black">{{ $totalSTRD }}</p>
                                                </div>
                                            </div>
                                            <p class="fs-32 mt-3"><span class="badge badge-pill badge-light ">Rp. {{ number_format($totalSTRDduit) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header bg-primary font-weight-bold text-white">Total STS</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="row justify-content-center">
                                                <div class="col-auto">
                                                    <i class="icon-pay-point text-primary fs-40"></i>
                                                </div>
                                                <div class="col-auto">
                                                    <p class="fs-22 mt-1 text-black">{{ $totalSTS }}</p>
                                                </div>
                                            </div>
                                            <p class="fs-32 mt-3"><span class="badge badge-pill badge-light ">Rp. {{ number_format($totalSTSduit) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card no-b mr-n15">
                                    <h6 class="card-header bg-success font-weight-bold text-white">Total Retribusi</h6>
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="row justify-content-center">
                                                <div class="col-auto">
                                                    <i class="icon-dollar text-success fs-40"></i>
                                                </div>
                                            </div>
                                            <p class="fs-32 mt-4"><span class="badge badge-pill badge-light ">Rp. {{ number_format($totalWRduit) }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tengah -->
                @role('super-admin|admin-bjb')
                    @include('pages.dashboard.card1')
                @endrole
                <!-- Bawah -->
                <div class="row p-0 col-md-12">
                    @role('super-admin|admin-bjb')
                    <div class="col-md-8 animate__animated animate__backInUp">
                        @include('pages.dashboard.chartDiagram')
                    </div>
                    <div class="p-0 col-md-4 animate__animated animate__backInUp">
                        @include('pages.dashboard.pieChart')
                    </div>
                    @endrole

                    @role('admin-opd|operator-opd|bendahara-opd|penandatangan')
                    <div class="col-md-4">
                        @include('pages.dashboard.pieChart')
                    </div>
                    <div class="col-md-8">
                        {{--  --}}
                    </div>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
