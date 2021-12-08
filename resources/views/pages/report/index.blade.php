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
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="jenis" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">Jenis : </label>
                            <div class="col-sm-4">
                                <select name="jenis" id="jenis" class="select2 form-control r-0 light s-12">
                                    <option value="0">Pilih</option>
                                    <option value="1">SKRD</option>
                                    <option value="2">STS</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="opd_id" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">OPD : </label>
                            <div class="col-sm-4">
                                <select name="opd_id" id="opd_id" class="select2 form-control r-0 light s-12">
                                    <option value="0">Semua</option>
                                    @foreach ($opds as $i)
                                        <option value="{{ $i->id }}">{{ $i->n_opd }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="jenis_pendapatan_id" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">Jenis Pendapatan : </label>
                            <div class="col-sm-4">
                                <select name="jenis_pendapatan_id" id="jenis_pendapatan_id" class="select2 form-control r-0 light s-12">
                                    <option value="0"></option>
                                </select>
                            </div>
                        </div> 
                        <div id="status_bayar_display" class="form-group row" style="margin-top: -8px !important">
                            <label for="status_bayar" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">Status Bayar : </label>
                            <div class="col-sm-4">
                                <select name="status_bayar" id="status_bayar" class="select2 form-control r-0 light s-12">
                                    <option value=""></option>
                                    <option value="0">Belum</option>
                                    <option value="1">Sudah</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label class="col-form-label s-12 col-md-4 text-right font-weight-bolder" id="tgl_skrd_text"> :</label>
                            <div class="col-sm-5 row">
                                <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd" id="tgl_skrd" class="form-control r-0 light s-12 col-md-4 ml-3" autocomplete="off"/>
                                <span class="mt-1 ml-2 mr-2"> - </span>
                                <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd1" id="tgl_skrd1" class="form-control r-0 light s-12 col-md-4" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="form-group row" style="margin-top: -8px !important"kr>
                            <label class="col-form-label s-12 col-md-4 text-right font-weight-bolder"></label>
                            <div class="col-sm-5 row">
                                <button class="btn btn-success btn-sm ml-3" onclick="pressOnChange()"><i class="icon-filter mr-2"></i>Filter</button>
                                <a target="_blank" href="{{ route('report.cetakSKRD') }}" class="btn btn-sm btn-primary ml-2" id="exportpdf"><i class="icon-print mr-2"></i>Print</a>
                            </div>
                        </div>
                        <div>
                            <p class="mb-0 font-weight-bold">Total Bayar : <span id="total_bayar"></span></p>
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
                                            <th>Nomor Bayar </th>
                                            <th>Nomor SKRD </th>
                                            <th>Nama Pembayar</th>
                                            <th>Jenis Retribusi</th>
                                            <th>Tanggal SKRD </th>
                                            <th>Tanggal Bayar</th>
                                            <th>NTB </th>
                                            <th>Diskon </th>
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
    $(function() {
        $('#status_bayar_display').hide(); 

        $('#tgl_skrd_text').html('Tanggal SKRD :');
        $('#jenis').change(function(){
            if($('#jenis').val() === "1") {
                $('#status_bayar_display').hide(); 
                $('#tgl_skrd_text').html('Tanggal SKRD :');
            } else {
                $('#status_bayar_display').show(); 
                $('#tgl_skrd_text').html('Tanggal Bayar :');
            } 
        });
    });

    var table = $('#dataTable').dataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        order: [ 0, 'asc' ],
        pageLength: 25,
        ajax: {
            url: "{{ route($route.'api') }}",
            method: 'POST',
            data: function (data) {
                data.tgl_skrd = $('#tgl_skrd').val();
                data.tgl_skrd1 = $('#tgl_skrd1').val();
                data.opd_id = $('#opd_id').val();
                data.jenis_pendapatan_id = $('#jenis_pendapatan_id').val();
                data.status_bayar = $('#status_bayar').val();
                data.jenis = $('#jenis').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'no_bayar', name: 'no_bayar'},
            {data: 'no_skrd', name: 'no_skrd'},
            {data: 'nm_wajib_pajak', name: 'nm_wajib_pajak'},
            {data: 'id_jenis_pendapatan', name: 'id_jenis_pendapatan'},
            {data: 'tgl_skrd', name: 'tgl_skrd'},
            {data: 'tgl_bayar', name: 'tgl_bayar'},
            {data: 'ntb', name: 'ntb'},
            {data: 'diskon', name: 'diskon'},
            {data: 'denda', name: 'denda'},
            {data: 'total_bayar', name: 'total_bayar'},
            {data: 'status_bayar', name: 'status_bayar'},
            {data: 'cetak_skrd', name: 'cetak_skrd', align: 'center', className: 'text-center'},
            {data: 'cetak_sts', name: 'cetak_sts', align: 'center', className: 'text-center'},
            // {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
        ]
    });

    $('#opd_id').on('change', function(){
        val = $(this).val();
        option = "<option value=''>&nbsp;</option>";
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

    pressOnChange();
    function pressOnChange(){
        table.api().ajax.reload();

        var opd_id = $('#opd_id').val();
        var jenis_pendapatan_id = $('#jenis_pendapatan_id').val();
        var status_bayar = $('#status_bayar').val();
        var tgl_skrd = $('#tgl_skrd').val();
        var tgl_skrd1 = $('#tgl_skrd1').val();
        var jenis = $('#jenis').val();

        params = tgl_skrd + "&tgl_skrd1=" + tgl_skrd1 + "&opd_id=" + opd_id + "&jenis_pendapatan_id=" + jenis_pendapatan_id + "&status_bayar=" + status_bayar + "&jenis=" + jenis;

        url1 = "{{ route('report.cetakSKRD') }}?tgl_skrd=" + params
        url2 = "{{ route('report.getTotalBayar') }}?tgl_skrd=" + params
        
        $('#exportpdf').attr('href', url1)
    
        // total bayar
        $.get(url2, function(data){
            $('#total_bayar').html(data.total_bayar)
        }, 'JSON');
    }
</script>
@endsection
