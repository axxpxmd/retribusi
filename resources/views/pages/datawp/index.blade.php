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
                                            <th>No</th>
                                            <th>Nama WP</th>
                                            <th>Nama Dinas</th>
                                            <th>Jenis Retribusi</th>
                                            <th>Alamat WP</th>
                                            {{-- <th>Jumlah SKRD</th> --}}
                                            <th></th>
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
        pageLength: 15,
        ajax: {
            url: "{{ route($route.'api') }}",
            method: 'POST',
            data: function (data) {
                data.opd_id = $('#opd_id').val();
                data.jenis_pendapatan_id = $('#jenis_pendapatan_id').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'nm_wajib_pajak', name: 'nm_wajib_pajak'},
            {data: 'id_opd', name: 'id_opd'},
            {data: 'id_jenis_pendapatan', name: 'id_jenis_pendapatan'},
            {data: 'alamat_wp', name: 'alamat_wp'},
            // {data: 'jumlah_skrd', name: 'jumlah_skrd'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
        ]
    });

    function pressOnChange(){
        table.api().ajax.reload();
    }

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
