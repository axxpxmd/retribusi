@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<style>
    .label-input-custom{
        font-size: 12px !important;
        text-align: right !important;
        border: none !important;
        padding-right: 1.5rem !important;
        color: #86939E !important;
        font-weight: 400 !important
    }
</style>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-document-list mr-2"></i>
                        {{ $title }} (Surat Ketetapan Retribusi Daerah)
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-home2"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2" data-toggle="tab" href="#tambah-data" role="tab"><i class="icon icon-plus"></i>Tambah Data</a>
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
                        <div class="col-md-8 container">
                            @if ($opd_id == 0)
                            <div class="row mb-2">
                                <label for="opd" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">OPD</label>
                                <div class="col-sm-8">
                                    <select name="opd" id="opd" class="select2 form-control r-0 light s-12">
                                        <option value="0">Semua</option>
                                        @foreach ($opds as $i)
                                            <option value="{{ $i->id }}">{{ $i->n_opd }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            <div class="row mb-2">
                                <label for="no_skrd" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">NO SKRD</label>
                                <div class="col-sm-8">
                                    <input type="text" name="no_skrd" id="no_skrd" class="form-control r-0 s-12 col-md-12" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="status_ttd" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Status TTD</label>
                                <div class="col-sm-8">
                                    <select name="status_ttd" id="status_ttd" class="select2 form-control r-0 light s-12">
                                        <option value=""></option>
                                        <option value="0">Belum TTD</option>
                                        <option value="1">Sudah TTD</option>
                                        <option value="2">Proses TTD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Tanggal SKRD</label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd" id="tgl_skrd" class="form-control r-0 s-12 mb-5-m" autocomplete="off"/>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd1" id="tgl_skrd1" class="form-control r-0 s-12" autocomplete="off"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <button class="btn btn-success btn-sm" onclick="pressOnChange()"><i class="icon-filter mr-2"></i>Filter</button>
                                </div> 
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
            <div class="tab-pane animated fadeInUpShort" id="tambah-data" role="tabpanel">
                <div class="col-md-12 p-0">
                    <div id="alert"></div>
                    <div class="card">
                        <h6 class="card-header"><strong>Tambah Data</strong></h6>
                        <div class="card-body">
                            <form class="needs-validation col-md-6" method="GET" action="{{ route('skrd.create') }}" enctype="multipart/form-data" novalidate>
                                {{ method_field('GET') }}
                                <div class="row mb-2">
                                    <label class="col-form-label s-12 col-sm-4 text-right">OPD<span class="text-danger ml-1">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="select2 form-control r-0 light s-12" id="opd_id" name="opd_id" autocomplete="off">
                                            <option value="0">Pilih</option>
                                            @foreach ($opds as $i)
                                                <option value="{{ \Crypt::encrypt($i->id) }}">{{ $i->n_opd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <label class="col-form-label s-12 col-sm-4 text-right">Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="select2 form-control r-0 light s-12" id="jenis_pendapatan_id" name="jenis_pendapatan_id" autocomplete="off">
                                            <option value="">Pilih</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-8">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="icon-arrow_forward mr-2"></i>Selanjutnya</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Send TTD -->
<div class="modal fade" id="updateStatusTTD" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                        <label class="col-form-label col-sm-3 s-12 font-weight-bold"><strong>Nama </strong></label>
                        <label class="col-form-label col-sm-9 font-weight-normal s-12" id="nm_wajib_pajak_ttd"></label>
                    </div>
                    <div class="row" style="margin-top: -5px !important">
                        <label class="col-form-label col-sm-3 s-12 font-weight-bold"><strong>No SKRD </strong></label>
                        <label class="col-form-label col-sm-9 font-weight-normal s-12" id="no_skrd_ttd"></label>
                    </div>
                    <div class="row" style="margin-top: -5px !important">
                        <label class="col-form-label col-sm-3 s-12 font-weight-bold"><strong>Ketetapan </strong></label>
                        <label class="col-form-label col-sm-9 font-weight-normal s-12" id="ketetapan"></label>
                    </div>
                    <p class="font-weight-bold text-black-50">Apakah sudah yakin mengirim data ini untuk ditandatangi ?</p>
                </div>
                <hr>
                <div class="text-right">
                    <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Batalkan</button>
                    <a href="" class="btn btn-sm btn-primary ml-2" id="kirimTTD"><i class="icon-pencil mr-2"></i>Kirim untuk TTD</a>
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

    $('#opd_id').on('change', function(){
        val = $(this).val();
        option = "<option value=''>&nbsp;</option>";
        if(val == ""){
            $('#jenis_pendapatan_id').html(option);
        }else{
            $('#jenis_pendapatan_id').html("<option value=''>Loading...</option>");
            url = "{{ route('skrd.getJenisPendapatan', ':id') }}".replace(':id', val);
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

    // Single TTD
    function updateStatusTTD(id){
        $('#updateStatusTTD').modal('show');
        $('#updateStatusTTD').modal({keyboard: false});

        // get detail data
        url = "{{ route('skrd.getDataSKRD', ':id') }}".replace(':id', id);
        $.get(url, function(data){
            $('#no_skrd_ttd').html(data.no_skrd)
            $('#nm_wajib_pajak_ttd').html(data.nm_wajib_pajak)

            var bilangan = data.jumlah_bayar;
            var	number_string = bilangan.toString(),
                sisa 	= number_string.length % 3,
                rupiah 	= number_string.substr(0, sisa),
                ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
                                    
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            $('#ketetapan').html(' Rp. '+ rupiah)
        }, 'JSON');

        $('#kirimTTD').attr('href', "{{ route('skrd.updateStatusKirimTTD', ':id') }}".replace(':id', id));
    }
</script>
@endsection
