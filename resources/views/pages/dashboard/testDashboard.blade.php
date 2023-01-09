@extends('layouts.app')
@section('title', '| Dashboard  ')
@section('content')
<style>
    .dtHorizontalVerticalExampleWrapper {
        max-width: 600px;
        margin: 0 auto;
        height: 600px !important;
    }
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
        font-size: 14px !important;
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
                <div class="card no-b mb-2">
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-auto mb-5-m">
                                <a href="#" data-toggle="modal" data-target="#modalFilter" class="btn btn-sm btn-success fs-14">Pilih Filter<i class="icon icon-filter_list m-l-8"></i></a>
                            </div>
                            <div class="col-auto mt-1">
                                <div class="row">
                                    <div class="col-auto">
                                        <span>Tahun : {{ $year }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <span>OPD : {{ $n_opd->n_opd }} </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card no-b r-15">
                    <h6 class="card-header r-15 bg-white font-weight-bold">Jenis Pendapatan {{ $year }}</h6>
                    <div class="card-body pt-1">
                        <table id="dtHorizontalVerticalExample" class="table table-hover fs-12" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Pendapatan</th>
                                    <th>OPD</th>
                                    <th>Target</th>
                                    <th>Ketetapan</th>
                                    <th>Diterima</th>
                                    {{-- <th>Denda</th> --}}
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
                                    <td>@currency($i->ketetapan)</td>
                                    <td>@currency($i->diterima)</td>
                                    {{-- <td>@currency($i->denda)</td> --}}
                                    <td class="text-center">{{ $i->realisasi ? $i->realisasi : '0' }} %</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
           </div>
           <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6 px-2 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white" style="background: #FFCE3B; border-top-right-radius: 15px; border-top-left-radius: 15px">Total SKRD</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 amber-text mr-2"></i><span class="m-0 font-weight-bold fs-16">{{ $totalSKRD->total_skrd }}</sp>
                                </div>
                                <p class="m-0 fs-16">@currency($totalSKRD->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 px-2 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white bg-danger" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Total STRD</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 text-danger mr-2"></i><span class="m-0 font-weight-bold fs-16">{{ $totalSTRD->total_skrd }}</sp>
                                </div>
                                <p class="m-0 fs-16">@currency($totalSTRD->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6 px-2 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white bg-primary" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Total STS</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 text-primary mr-2"></i><span class="m-0 font-weight-bold fs-16">{{ $totalSTS->total_skrd }}</sp>
                                </div>
                                <p class="m-0 fs-16">@currency($totalSTS->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 px-2 mb-5-m">
                        <div class="card no-b r-15">
                            <h6 class="card-header font-weight-bold text-white bg-success" style="border-top-right-radius: 15px; border-top-left-radius: 15px">Total Keseluruhan</h6>
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <i class="icon-notebook-text fs-24 text-success mr-2"></i><span class="m-0 font-weight-bold fs-16">{{ $totalKeseluruhan->total_skrd }}</sp>
                                </div>
                                <p class="m-0 fs-16">@currency($totalKeseluruhan->total_bayar)</p>
                            </div>
                        </div>
                    </div>
                </div>
           </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalFilter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content no-b">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="exampleModalLabel">Filter Data</h5>
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
                        </select>
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="opd_id" class="col-sm-2 col-form-label font-weight-bold">OPD</label>
                    <div class="col-sm-10">
                        <select class="select2 form-control bg s-12" name="opd_id" id="opd_filter">
                            <option value="">Semua</option>
                            @foreach ($opds as $i)
                                <option value="{{ $i->id }}" {{ $opd_id == $i->id ? 'selected' : '' }}>{{ $i->n_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <a href="#" id="filterData" class="btn btn-success btn-sm fs-14"><i class="icon icon-filter_list"></i>Filter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
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
       
        url = "{{ route('test-home') }}?tahun=" + tahun + "&opd_id=" + opd_id;

        console.log(url);

        $('#filterData').attr('href', url);
    }
</script>
@endsection