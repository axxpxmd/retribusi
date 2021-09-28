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
                        {{ $title }} (Surat Tagihan Retribusi Daerah)
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
                            <label for="no_skrd" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">NO SKRD : </label>
                            <div class="col-sm-4">
                                <input type="text" name="no_skrd" id="no_skrd" class="form-control r-0 s-12 col-md-12" autocomplete="off" required/>
                            </div>
                        </div> 
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="status_ttd" class="col-form-label s-12 col-md-4 text-right font-weight-bolder">Status TTD : </label>
                            <div class="col-sm-4">
                                <select name="status_ttd" id="status_ttd" class="select2 form-control r-0 light s-12">
                                    <option value=""></option>
                                    <option value="0">Belum diTTD</option>
                                    <option value="1">Sudah diTTD</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label class="col-form-label s-12 col-md-4 text-right font-weight-bolder">Tanggal SKRD:</label>
                            <div class="col-sm-5 row">
                                <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd" id="tgl_skrd" class="form-control r-0 light s-12 col-md-4 ml-3" autocomplete="off"/>
                                <span class="mt-1 ml-2 mr-2">-</span>
                                <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd1" id="tgl_skrd1" class="form-control r-0 light s-12 col-md-4" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label class="col-form-label s-12 col-md-4 text-right font-weight-bolder"></label>
                            <div class="col-sm-5 row">
                                <button class="btn btn-success btn-sm ml-3" onclick="pressOnChange()"><i class="icon-filter mr-2"></i>Filter</button>
                                {{-- <a href="#" data-toggle="modal" onclick="createRoute()" data-target="#updateStatusTTDs" class="btn btn-sm btn-primary ml-2"><i class="icon-pencil mr-2"></i>Kirim untuk TTD</a> --}}
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
                                            <th>No</th>
                                            <th>Nomor SKRD</th>
                                            <th>Nomor Bayar</th>
                                            <th>Nama WP</th>
                                            {{-- <th width="21%">Nama Dinas</th> --}}
                                            <th>Jenis Retribusi</th>
                                            <th>Tanggal SKRD</th>
                                            <th>Masa Berlaku SKRD</th>
                                            <th>Ketetapan</th>
                                            <th>Aksi</th>
                                            <th>Status TTD</th>
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
<div class="modal fade" id="updateStatusTTD" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p class="font-weight-bold text-black-50">Apakah sudah yakin mengirim data ini untuk ditandatangi ?</p>
                <hr>
                <div class="text-right">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                    <a href="" class="btn btn-sm btn-primary ml-2" id="kirimTTD"><i class="icon-pencil mr-2"></i>Kirim untuk TTD</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="updateStatusTTDs" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p class="font-weight-bold">Apakah sudah yakin mengirim data ini untuk ditandatangi ?</p>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                <a href="{{ route('strd.updateStatusKirimTTDs') }}" class="btn btn-sm btn-primary ml-2" id="kirimTTDs"><i class="icon-pencil mr-2"></i>Kirim untuk TTD</a>
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
                data.tgl_skrd   = $('#tgl_skrd').val();
                data.tgl_skrd1  = $('#tgl_skrd1').val();
                data.opd_id     = $('#opd').val();
                data.no_skrd    = $('#no_skrd').val();
                data.status_ttd = $('#status_ttd').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'no_skrd', name: 'no_skrd'},
            {data: 'no_bayar', name: 'no_bayar'},
            {data: 'nm_wajib_pajak', name: 'nm_wajib_pajak'},
            // {data: 'id_opd', name: 'id_opd'},
            {data: 'id_jenis_pendapatan', name: 'id_jenis_pendapatan'},
            {data: 'tgl_skrd', name: 'tgl_skrd'},
            {data: 'masa_berlaku', name: 'masa_berlaku'},
            {data: 'jumlah_bayar', name: 'jumlah_bayar'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            {data: 'status_ttd', name: 'status_ttd', orderable: false, searchable: false, className: 'text-center'}
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

    function updateStatusTTD(id){
        $('#updateStatusTTD').modal('show');
        $('#updateStatusTTD').modal({keyboard: false});

        $('#kirimTTD').attr('href', "{{ route('strd.updateStatusKirimTTD', ':id') }}".replace(':id', id));
    }

    function createRoute(){
        var tgl_skrd   = $('#tgl_skrd').val();
        var tgl_skrd1  = $('#tgl_skrd1').val();
        var opd_id     = $('#opd').val();
        var no_skrd    = $('#no_skrd').val();
        var status_ttd = $('#status_ttd').val();

        $('#kirimTTDs').attr('href', "{{ route('strd.updateStatusKirimTTDs') }}?tgl_skrd=" + tgl_skrd + "&tgl_skrd1=" + tgl_skrd1 + "&opd_id=" + opd_id + "&status_ttd=" + status_ttd + "&no_skrd=" + no_skrd);
    }
</script>
@endsection
