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
                        {{ $title }} (Surat Tanda Setoran)
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
        @include('layouts.alerts')
        <div class="tab-content " id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="card no-b mb-2">
                    <div class="card-body">
                        @if ($opd_id == 0)
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="opd" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">OPD : </label>
                            <div class="col-sm-4">
                                <select name="opd" id="opd" class="select2 form-control r-0 light s-12">
                                    <option value="0">Semua</option>
                                    @foreach ($opds as $i)
                                        <option value="{{ $i->id }}">{{ $i->n_opd }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        @endif
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="status_bayar" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">Status Bayar : </label>
                            <div class="col-sm-4">
                                <select name="status_bayar" id="status_bayar" class="select2 form-control r-0 light s-12">
                                    <option value="">Semua</option>
                                    <option value="0">Belum Dibayar</option>
                                    <option value="1">Sudah Dibayar</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="no_bayar" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">NO Bayar : </label>
                            <div class="col-sm-4">
                                <input type="text" name="no_bayar" id="no_bayar" class="form-control r-0 s-12 col-md-12" autocomplete="off" required/>
                            </div>
                        </div>
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label class="col-form-label s-12 col-md-4 text-right font-weight-bolder">
                                Tanggal &nbsp;
                            </label>
                            <div class="col-md-5 row">
                                <div class="col-md-3 ml-0 pr-1">
                                    <select name="jenis_tanggal" id="jenis_tanggal" class="select2 form-control r-0 light s-12">
                                        <option value="1">SKRD</option>
                                        <option value="2">Bayar</option>
                                    </select>
                                </div>
                                <input type="date" name="tgl_bayar" id="tgl_bayar" value="{{ $today }}" placeholder="" class="form-control r-0 light s-12 col-md-3 ml-3" autocomplete="off"/>
                                <span class="mt-1 ml-2 mr-2">--</span>
                                <input type="date" name="tgl_bayar1" id="tgl_bayar1" value="{{ $today }}" placeholder="" class="form-control r-0 light s-12 col-md-3" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label class="col-form-label s-12 col-md-4 text-right font-weight-bolder"></label>
                            <div class="col-sm-5 row">
                                <button class="btn btn-success btn-sm ml-3" onclick="pressOnChange()"><i class="icon-filter mr-2"></i>Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card no-b">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table display nowrap table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <th width="5%">No</th>
                                            <th width="8%">Nomor Bayar </th>
                                            <th width="8%">Nomor SKRD </th>
                                            <th width="11%">Nama Pembayar</th>
                                            <th width="20%">Jenis Retribusi</th>
                                            <th width="8%">Tanggal SKRD </th>
                                            <th width="8%">Tanggal Bayar</th>
                                            <th width="10%">NTB </th>
                                            <th width="9%">Total Bayar Bank </th>
                                            <th width="8%">Status Bayar</th>
                                            <th width="5%"></th>
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
    $(document).ready(function() {
        $("#errorAlert").fadeTo(5000, 1000).slideUp(1000, function() {
            $("#errorAlert").slideUp(1000);
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
                data.tgl_bayar = $('#tgl_bayar').val();
                data.tgl_bayar1 = $('#tgl_bayar1').val();
                data.opd_id = $('#opd').val();
                data.status_bayar = $('#status_bayar').val();
                data.jenis_tanggal = $('#jenis_tanggal').val();
                data.no_bayar = $('#no_bayar').val();
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
            {data: 'total_bayar', name: 'total_bayar'},
            {data: 'status_bayar', name: 'status_bayar'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
        ]
    });

    function pressOnChange(){
        table.api().ajax.reload();
    }

    function remove(id){
        $.confirm({
            title: '',
            content: 'Apakah Anda yakin akan menghapus data ini ?',
            icon: 'icon icon-question amber-text',
            theme: 'modern',
            closeIcon: true,
            animation: 'scale',
            type: 'red',
            buttons: {
                ok: {
                    text: "ok!",
                    btnClass: 'btn-primary',
                    keys: ['enter'],
                    action: function(){
                        $.post("{{ route($route.'destroy', ':id') }}".replace(':id', id), {'_method' : 'DELETE'}, function(data) {
                            $('#dataTable').DataTable().ajax.reload();
                            $.confirm({
                                title: 'Success',
                                content: data.message,
                                icon: 'icon icon-check',
                                theme: 'modern',
                                closeIcon: true,
                                animation: 'scale',
                                autoClose: 'ok|3000',
                                type: 'green',
                                buttons: {
                                    ok: {
                                        text: "ok!",
                                        btnClass: 'btn-primary',
                                        keys: ['enter']
                                    }
                                }
                            });
                        }, "JSON").fail(function(){
                            reload();
                        });
                    }
                },
                cancel: function(){}
            }
        });
    }
</script>
@endsection
