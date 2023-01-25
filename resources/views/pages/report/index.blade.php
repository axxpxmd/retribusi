@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-documents mr-2"></i>
                        {{ $title }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-home2"></i>Semua Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid my-3 relative animatedParent animateOnce">
        <div class="tab-content " id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="card no-b mb-2">
                    <div class="card-body">
                        <input type="hidden" id="year" value="{{ $tahun }}">
                        <input type="hidden" id="status" value="{{ $status }}">
                        <div class="container col-md-8">
                            <div class="row mb-2">
                                <label for="jenis" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Jenis </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="jenis" name="jenis">
                                        <option value="0">Pilih</option>
                                        <option value="1" {{ $status == 1 ? 'selected' : '' }}>SKRD</option>
                                        <option value="2" {{ $status == 2 ? 'selected' : '' }}>STS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="opd_id" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">OPD </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="opd_id" name="opd_id">
                                        @if ($role == 'super-admin' || $role == 'admin-bjb')
                                            <option value="0">Semua</option>
                                        @endif
                                        @foreach ($opds as $i)
                                            <option value="{{ $i->id }}" {{ $i->id == $opd_id ? 'selected' : ''}}>{{ $i->n_opd }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="jenis_pendapatan_id" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Jenis Pendapatan </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="jenis_pendapatan_id" name="jenis_pendapatan_id">
                                        <option value="0"></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="rincian_pendapatan_id" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Rincian Pendapatan </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="rincian_pendapatan_id" name="rincian_pendapatan_id">
                                        <option value="0"></option>
                                    </select>
                                </div>
                            </div>
                            <div id="status_bayar_display" class="row mb-2">
                                <label for="status_bayar" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Status Bayar </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="status_bayar" name="status_bayar">
                                        <option value=""></option>
                                        <option value="0">Belum</option>
                                        <option value="1">Sudah</option>
                                    </select>
                                </div>
                            </div>
                            <div id="display_channel_bayar" class="row mb-2">
                                <label for="channel_bayar" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Metode Bayar </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="channel_bayar" name="channel_bayar">
                                        <option value="0">Semua</option>
                                        <option value="1">Virtual Account</option>
                                        <option value="2">ATM</option>
                                        <option value="3">BJB MOBILE</option>
                                        <option value="4">TELLER</option>
                                        <option value="5">QRIS</option>
                                        <option value="6">Bendahara OPD</option>
                                        <option value="7">Transfer RKUD</option>
                                        <option value="8">RTGS/SKN</option>
                                        <option value="9">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="channel_bayar" class="col-form-label s-12 col-md-2 text-right font-weight-bolder" id="tgl_skrd_text"></label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="from" id="from" class="form-control r-0 light s-12 mb-5-m" autocomplete="off"/>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="to" id="to" class="form-control r-0 light s-12" autocomplete="off"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <button class="btn btn-success btn-sm" onclick="pressOnChange()"><i class="icon-filter mr-2"></i>Filter</button>
                                    <a target="_blank" href="{{ route('report.cetakSKRD') }}" class="btn btn-sm btn-primary ml-2" id="exportpdf"><i class="icon-print mr-2"></i>Print</a>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <p class="mb-0 font-weight-bold">Total Ketetapan : <span id="total_bayar"></span></p>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card no-b">
                            <div class="card-body">
                                <div class="">
                                    <table id="dataTable" class="table display nowrap table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <th>No</th>
                                            <th>Nomor SKRD </th>
                                            <th>Nomor Bayar </th>
                                            <th>Nama </th>
                                            <th>Rincian Pendapatan</th>
                                            <th>Tanggal SKRD </th>
                                            <th>Tanggal Bayar</th>
                                            <th>NTB </th>
                                            <th>Ketetapan </th>
                                            <th>Denda </th>
                                            <th>Total Bayar </th>
                                            <th>Status Bayar</th>
                                            <th>SKRD</th>
                                            <th>STS</th>
                                        </thead>
                                        <tbody></tbody>
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
@endsection
@section('script')
<script type="text/javascript">
    $('#status_bayar_display').hide(); 
    $('#display_channel_bayar').hide(); 
    $('#tgl_skrd_text').html('Tanggal SKRD');

    if ("{{ $status }}" == 2) {
        console.log('jalan');
        $('#status_bayar_display').hide(); 
        $("#status_bayar").val(0).trigger("change.select2");
        $('#display_channel_bayar').show(); 
        $('#tgl_skrd_text').html('Tanggal Bayar');
    }

    $('#jenis').change(function(){
        if($('#jenis').val() === "1") {
            $('#status_bayar_display').show(); 
            $('#display_channel_bayar').hide(); 
            $("#channel_bayar").val(0).trigger("change.select2");
            $('#tgl_skrd_text').html('Tanggal SKRD');
        } else {
            $('#status_bayar_display').hide(); 
            $("#status_bayar").val(0).trigger("change.select2");
            $('#display_channel_bayar').show(); 
            $('#tgl_skrd_text').html('Tanggal Bayar');
        } 
    });

    $('#status_bayar').change(function(){
        if($('#status_bayar').val() === "1") {
            $('#display_channel_bayar').show(); 
        } else {
            $('#display_channel_bayar').hide(); 
            $("#channel_bayar").val(0).trigger("change.select2");
        } 
    });

    var table = $('#dataTable').dataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        order: [ 0, 'asc' ],
        pageLength: 25,
        ajax: {
            url: "{{ route($route.'index') }}",
            method: 'GET',
            data: function (data) {
                data.from = $('#from').val();
                data.to = $('#to').val();
                data.opd_id = $('#opd_id').val();
                data.jenis_pendapatan_id = $('#jenis_pendapatan_id').val();
                data.status_bayar = $('#status_bayar').val();
                data.jenis = $('#jenis').val();
                data.channel_bayar = $('#channel_bayar').val();
                data.rincian_pendapatan_id = $('#rincian_pendapatan_id').val();
                data.year   = $('#year').val();
                data.status = $('#status').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'no_skrd', name: 'no_skrd'},
            {data: 'no_bayar', name: 'no_bayar'},
            {data: 'nm_wajib_pajak', name: 'nm_wajib_pajak'},
            {data: 'rincian_pendapatan', name: 'rincian_pendapatan'},
            {data: 'tgl_skrd', name: 'tgl_skrd'},
            {data: 'tgl_bayar', name: 'tgl_bayar'},
            {data: 'ntb', name: 'ntb'},
            {data: 'ketetapan', name: 'ketetapan'},
            {data: 'denda', name: 'denda'},
            {data: 'total_bayar', name: 'total_bayar'},
            {data: 'status_bayar', name: 'status_bayar'},
            {data: 'cetak_skrd', name: 'cetak_skrd', align: 'center', className: 'text-center'},
            {data: 'cetak_sts', name: 'cetak_sts', align: 'center', className: 'text-center'},
            // {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
        ]
    });

    $(document).ready(function(){
        $("#opd_id").trigger('change');
    });
    $('#opd_id').on('change', function(){
        val = $(this).val();
        $("#rincian_pendapatan_id").val(0).trigger("change.select2");
        option = "<option value=''>Semua</option>";
        if(val == ""){
            $('#jenis_pendapatan_id').html(option);
        }else{
            $('#jenis_pendapatan_id').html("<option value=''>Loading...</option>");
            url = "{{ route('report.getJenisPendapatan', ':id') }}".replace(':id', val);
            $.get(url, function(data){
                if(data){
                    $.each(data, function(index, value){
                        option += "<option value='" +  value.id + "'>" + value.jenis_pendapatan +"</li>";
                    });
                    $('#jenis_pendapatan_id').empty().html(option);

                    $("#jenis_pendapatan_id").val($("#jenis_pendapatan_id option:first").val()).trigger("change.select2");
                }else{
                    $('#jenis_pendapatan_id').html(option);
                }
            }, 'JSON');
        }
    });

    $('#jenis_pendapatan_id').on('change', function(){
        val = $(this).val();
        option = "<option value=''>Semua</option>";
        if(val == ""){
            $('#rincian_pendapatan_id').html(option);
        }else{
            $('#rincian_pendapatan_id').html("<option value=''>Loading...</option>");
            url = "{{ route('report.getRincianByJenisPendapatan', ':id') }}".replace(':id', val);
            $.get(url, function(data){
                if(data){
                    $.each(data, function(index, value){
                        option += "<option value='" +  value.id + "'>" + value.rincian_pendapatan +"</li>";
                    });
                    $('#rincian_pendapatan_id').empty().html(option);

                    $("#rincian_pendapatan_id").val($("#rincian_pendapatan_id option:first").val()).trigger("change.select2");
                }else{
                    $('#rincian_pendapatan_id').html(option);
                }
            }, 'JSON');
        }
    });

    pressOnChange();
    function pressOnChange(){
        table.api().ajax.reload();

        opd_id = $('#opd_id').val();
        jenis_pendapatan_id = $('#jenis_pendapatan_id').val();
        rincian_pendapatan_id = $('#rincian_pendapatan_id').val();
        status_bayar = $('#status_bayar').val();
        from = $('#from').val();
        to = $('#to').val();
        jenis = $('#jenis').val();
        channel_bayar = $('#channel_bayar').val();
        year   = $('#year').val();
        status = $('#status').val();

        params = from + "&to=" + to + "&opd_id=" + opd_id + "&jenis_pendapatan_id=" + jenis_pendapatan_id + "&status_bayar=" + status_bayar + "&jenis=" + jenis + "&channel_bayar=" + channel_bayar + "&rincian_pendapatan_id=" + rincian_pendapatan_id + "&year=" + year + "&status=" + status;

        url1 = "{{ route('report.cetakSKRD') }}?from=" + params
        url2 = "{{ route('report.getTotalBayar') }}?from=" + params
        
        $('#exportpdf').attr('href', url1)
    
        // total bayar
        $.get(url2, function(data){
            $('#total_bayar').html(data.total_bayar)
        }, 'JSON');
    }
</script>
@endsection
