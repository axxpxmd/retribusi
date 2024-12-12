@extends('layouts.app')
@section('title', '| Dashboard  ')
@section('content')
<style>
    #dtHorizontalVerticalExample td {
        white-space: nowrap;
    }

    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_asc_disabled:after,
    table.dataTable thead .sorting_asc_disabled:before,
    table.dataTable thead .sorting_desc:after,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_desc_disabled:after,
    table.dataTable thead .sorting_desc_disabled:before {
        bottom: .5em;
    }
    table thead tr th{
        font-size: 12px !important;
        font-weight: bolder !important;
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
    <div class="container my-3 col-md-12 relative animatedParent animateOnce">
        <div class="row">
           <div class="col-md-8 mb-5-m">
                <div class="card shadow-sm no-b mb-2">
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-auto mb-5-m">
                                <a href="#" data-toggle="modal" data-target="#modalFilter" class="btn btn-sm btn-outline-success fs-14">Pilih Filter<i class="icon-filter_list ml-2"></i></a>
                            </div>
                            <div class="col-auto mt-2">
                                <div class="row">
                                    <div class="col-auto">
                                        <span class="font-weight-bold">Tahun : {{ $year }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="font-weight-bold">OPD : {{ $n_opd->n_opd }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card no-b r-15">
                    <h6 class="card-header r-15 bg-white font-weight-bold">Pendapatan {{ $year }}</h6>
                    <div class="card-body pt-1">
                        <table id="dtHorizontalVerticalExample" class="table table-hover fs-12" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jenis Pendapatan</th>
                                    <th>OPD</th>
                                    <th>Target</th>
                                    <th>Diterima</th>
                                    <th>Denda</th>
                                    <th>Jumlah</th>
                                    <th>Realisasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($targetPendapatan as $index => $i)
                                <tr>
                                    <td class="text-center">{{  $index+1 }}</td>
                                    <td>{{ $i->jenis_pendapatan }}</td>
                                    <td title="{{ $i->n_opd }}">{{ $i->initial }}</td>
                                    <td>@currency($i->target_pendapatan)</td>
                                    <td>@currency($i->diterima)</td>
                                    <td>@currency($i->totalDenda)</td>
                                    <td class="text-center">{{ number_format($i->jumlah) }}</td>
                                    <td class="text-center">{{ $i->realisasi ? $i->realisasi : '0' }} %</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"></td>
                                    <td colspan="1" class="font-weight-bold">Total</td>
                                    <td colspan="1" class="font-weight-bold">@currency($targetPendapatan->sum('diterima'))</td>
                                    <td colspan="3" class="font-weight-bold">@currency($targetPendapatan->sum('totalDenda'))</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
           </div>
           <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6 px-1 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white" style="background: #FFCE3B; border-top-right-radius: 15px; border-top-left-radius: 15px">Total SKRD {{ $year }}</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 amber-text mr-2"></i>
                                    <span class="m-0 font-weight-bold fs-16">{{ number_format($totalSKRD->total_skrd) }}</span>
                                    <a href="{{ route('report.index', ['year' => $year, 'status' => 1, 'opd_id' => $n_opd->id]) }}" target="_blank" class="ml-2" title="Lihat Data"><i class="icon-external-link"></i></a>
                                </div>
                                <p class="m-0 fs-14">@currency($totalSKRD->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 px-2 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white bg-danger" title="SKRD yang telah jatuh tempo" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Total STRD {{ $year }}</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 text-danger mr-2"></i>
                                    <span class="m-0 font-weight-bold fs-16">{{ number_format($totalSTRD->total_skrd) }}</span>
                                    @can('STRD')
                                    <a href="{{ route('strd.index', ['year' => $year, 'status' => 1, 'opd_id' => $n_opd->id]) }}" target="_blank" class="ml-2" title="Lihat Data"><i class="icon-external-link"></i></a>
                                    @endcan
                                </div>
                                <p class="m-0 fs-14">@currency($totalSTRD->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6 px-1 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white bg-primary" title="SKRD yang sudah dibayar" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Total STS {{ $year }}</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 text-primary mr-2"></i>
                                    <span class="m-0 font-weight-bold fs-16">{{ number_format($totalSTS->total_skrd) }}</span>
                                    <a href="{{ route('report.index', ['year' => $year, 'status' => 2, 'opd_id' => $n_opd->id]) }}" target="_blank" class="ml-2" title="Lihat Data"><i class="icon-external-link"></i></a>
                                </div>
                                <p class="m-0 fs-14">@currency($totalSTS->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 px-2 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white bg-success" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Keseluruhan {{ $year }}</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 text-success mr-2"></i>
                                    <span class="m-0 font-weight-bold fs-16">{{ number_format($totalKeseluruhan->total_skrd) }}</span>
                                    {{-- <a href="#" class="ml-2" title="Lihat Data"><i class="icon-external-link"></i></a> --}}
                                </div>
                                <p class="m-0 fs-14">@currency($totalKeseluruhan->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 px-1">
                        <div class="card no-b r-15" style="height: 162px !important">
                            <h6 class="card-header bg-primary text-white font-weight-bold" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Notifikasi <i class="icon-notifications ml-2"></i></h6>
                            <div class="card-body px-4 py-1">
                                @can('Tanda Tangan')
                                <p style="margin-bottom: -5px !important" class="fs-12"><i class="icon icon-data_usage text-primary mr-2"></i><a href="{{ route('tanda-tangan.index') }}" title="Lihat Data">Terdapat {{ $tandaTanganToday }} SKRD yang belum ditanda tangani hari ini.</a></p>
                                @endcan
                                @can('STRD')
                                <p style="margin-bottom: -5px !important" class="fs-12"><i class="icon icon-data_usage text-danger mr-2"></i><a href="{{ route('strd.index', ['year' => $year, 'status' => 2, 'opd_id' => $n_opd->id]) }}" target="_blank" title="Lihat Data">Terdapat {{ $strdToday }} SKRD telah jatuh tempo.</a></p>
                                @endcan
                                @can('SKRD')
                                <p style="margin-bottom: -5px !important" class="fs-12"><i class="icon icon-data_usage amber-text mr-2"></i><a href="{{ route('sts.index', ['status' => 2, 'opd_id' => $n_opd->id]) }}" target="_blank" title="Lihat Data">Terdapat {{ $skrdToday }} SKRD terbuat pada hari ini.</a></p>
                                @endcan
                                @can('STS')
                                <p style="margin-bottom: -5px !important" class="fs-12"><i class="icon icon-data_usage text-success mr-2"></i><a href="{{ route('sts.index', ['status' => 1, 'opd_id' => $n_opd->id]) }}" target="_blank" title="Lihat Data">Terdapat {{ $stsToday }} SKRD telah dibayar pada hari ini.</a></p>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
           </div>
        </div>
        @hasanyrole('super-admin|admin-bjb')
        <div class="card mt-3 no-b bg-transparent">
            <div class="card-body p-0 bg-transparent">
                <div class="lightSlider bg-transparent" data-item="6" data-item-xl="4" data-item-md="2" data-item-sm="1" data-pause="5000" data-pager="false" data-auto="true" data-loop="true">
                    <div class="bg-primary border text-white r-15 py-2 text-center">
                        <p class="font-weight-bold text-primary fs-16 m-0" title="Total Retribusi Seluruh Dinas">Total Retribusi</p>
                        <p class="fs-16 amber-text m-0">{{ number_format($totalRetribusi->total_skrd) }}</p->
                        <p class="fs-14 text-black m-0">@currency($totalRetribusi->total_bayar)</p>
                    </div>
                    @foreach ($totalRetribusiOPD as $index => $i)
                        @if ($index % 2 == 0)
                        <div class="bg-white text-center r-15 py-2">
                            <p class="font-weight-bold amber-text fs-16 m-0" title="{{ $i->n_opd }}" style="margin-bottom: 20px">{{ $i->initial }}</p>
                            <p class="fs-16 amber-text m-0">{{ number_format($i->total) }}</p>
                            <p class="fs-14 amber-text  m-0">@currency($i->total_bayar)</p>
                        </div>
                        @else
                        <div class="text-center py-2 r-15 bg-white">
                            <p class="font-weight-bold text-primary fs-16 m-0" title="{{ $i->n_opd }}" style="margin-bottom: 20px">{{ $i->initial }}</p>
                            <p class="fs-16 text-primary m-0">{{ number_format($i->total) }}</p>
                            <p class="fs-14 text-primary m-0">@currency($i->total_bayar)</p>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endhasanyrole
        <div class="row mt-3">
            <div class="col-md-4 mb-5-m">
                <div class="card no-b r-15" style="height: 300px !important">
                    <h6 class="card-header bg-danger text-white font-weight-bold" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Metode Pembayaran {{ $year }}<i class="icon-payment ml-2"></i></h6>
                    <div class="card-body pt-1">
                        <table id="tableChannelBayar" class="table table-hover fs-12" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Metode Bayar</th>
                                    <th>Jumlah</th>
                                    <th>Total Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($totalChannelBayar as $index => $i)
                                    @if ($i['total'])
                                    <tr>
                                        <td>{{ str_contains($i['chanel_bayar'], 'QRIS') ? 'QRIS' : $i['chanel_bayar'] }}</td>
                                        <td>{{ number_format($i['total']) }}</td>
                                        <td>@currency($i['total_bayar'])</td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-5-m">
                <div class="card r-15 no-b" style="height: 300px !important">
                    <h6 class="card-header bg-success text-white font-weight-bold" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Metode Pembayaran {{ $year }}<i class="icon-payment ml-2"></i></h6>
                    <div class="card-body py-0 px-1">
                        @include('pages.dashboard.pieMetodePembayaran')
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card r-15 no-b" style="height: 300px !important">
                    <div class="card-body py-1 px-2">
                        @include('pages.dashboard.chartPendapatanTahun')
                    </div>
                </div>
            </div>
        </div>
        @hasanyrole('super-admin|admin-bjb')
        <div class="row mt-3">
            <div class="col-md-7 mb-5-m">
                <div class="card no-b r-15" style="height: 475px !important">
                    <h6 class="card-header text-white font-weight-bold" style="background: #FFCE3B; border-top-right-radius: 15px; border-top-left-radius: 15px">Pendapatan OPD {{ $year }}</h6>
                    <div class="card-body p-0">
                        @include('pages.dashboard.chartDiagram')
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card no-b r-15" style="height: 475px !important">
                    <h6 class="card-header text-white font-weight-bold bg-blue-grey" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Pembayaran hari ini</h6>
                    <div class="card-body">
                        <table id="tablePembayaran" class="table table-hover fs-12 mt-2" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>No Bayar</th>
                                    <th>OPD</th>
                                    <th>Channel Bayar</th>
                                    <th>Total Bayar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembayaranHariIni as $index => $i)
                                <tr>
                                    <td class="text-center">{{ $index+1 }}</td>
                                    <td><a href="{{ route('sts.show', Crypt::encrypt($i->id)) }}" title="Lihat Detail">{{ $i->no_bayar }}</a></td>
                                    <td title="{{ $i->n_opd }}">{{ $i->initial }}</td>
                                    <td>{{ str_contains($i->chanel_bayar, 'QRIS') ? 'QRIS' : $i->chanel_bayar }}</td>
                                    <td>@currency($i->total_bayar)</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"></td>
                                    <td colspan="1" class="font-weight-bold">Total</td>
                                    <td colspan="1">@currency($pembayaranHariIni->sum('total_bayar'))</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endhasanyrole
    </div>
</div>
<div class="modal fade" id="modalFilter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content no-b">
            <div class="modal-header py-2 bg-light">
                <p class="fs-14 m-0 text-uppercase font-weight-bold">Filter Data</p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <label for="tahun" class="col-sm-2 col-form-label font-weight-bold">Tahun</label>
                    <div class="col-sm-10">
                        <select class="select2 form-control bg s-12" name="tahun" id="tahun_filter">
                            <option value="2021" {{ $year == 2021 ? 'selected' : '' }}>2021</option>
                            <option value="2022" {{ $year == 2022 ? 'selected' : '' }}>2022</option>
                            <option value="2023" {{ $year == 2023 ? 'selected' : '' }}>2023</option>
                            <option value="2024" {{ $year == 2024 ? 'selected' : '' }}>2024</option>
                            <option value="2024" {{ $year == 2024 ? 'selected' : '' }}>2024</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="opd_id" class="col-sm-2 col-form-label font-weight-bold">OPD</label>
                    <div class="col-sm-10">
                        <select class="select2 form-control bg s-12" name="opd_id" id="opd_filter">
                            @if ($role == 'super-admin' || $role == 'admin-bjb')
                                <option value="0">Semua</option>
                            @endif
                            @foreach ($opds as $i)
                                <option value="{{ Crypt::encrypt($i->id) }}" {{ $opd_id == $i->id ? 'selected' : '' }}>{{ $i->n_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <a href="#" id="filterData" class="btn btn-success btn-sm fs-14">Filter<i class="icon-filter_list ml-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
@stack('scriptDashboard')
<script type="text/javascript">
    $(document).ready(function () {
        $('#dtHorizontalVerticalExample').DataTable({
            "scrollX": true,
            "scrollY": 300,
            "bPaginate": false,
            "bInfo": false,
            "searching": false
        });
        $('.dataTables_length').addClass('bs-select');
    });

    $(document).ready(function () {
        $('#tableChannelBayar').DataTable({
            "scrollX": true,
            "scrollY": 180,
            "bPaginate": false,
            "bInfo": false,
            "searching": false,
            "order": [[1, 'desc']],
        });
        $('.dataTables_length').addClass('bs-select');
    });

    $(document).ready(function () {
        $('#tablePembayaran').DataTable({
            "scrollX": true,
            "scrollY": 300,
            "bPaginate": false,
            "bInfo": false,
            "searching": false
        });
        $('.dataTables_length').addClass('bs-select');
    });

    $('.select2').select2({
        dropdownParent: $('#modalFilter')
    });

    $('#tahun_filter').on('change', function(){
        getParamFilter()
    });

    $('#opd_filter').on('change', function(){
        getParamFilter()
    });

    function getParamFilter()
    {
        tahun =  $("#tahun_filter").val();
        opd_id = $("#opd_filter").val();

        url = "{{ route('home') }}?tahun=" + tahun + "&opd_id=" + opd_id;

        $('#filterData').attr('href', url);
    }
</script>
@endsection
