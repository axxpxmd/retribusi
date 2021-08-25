@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-document mr-1"></i>
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
        @include('layouts.alerts')
        <div class="tab-content " id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card no-b mb-2">
                            <div class="card-body">
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label for="opd_id" class="col-form-label s-12 col-md-3 text-right font-weight-bolder">OPD : </label>
                                    <div class="col-sm-8">
                                        <select name="opd_id" id="opd_id" class="select2 form-control r-0 light s-12">
                                            <option value="0">Semua</option>
                                            @foreach ($opds as $i)
                                                <option value="{{ $i->id }}">{{ $i->n_opd }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label for="jenis_pendapatan_id" class="col-form-label s-12 col-md-3 text-right font-weight-bolder">Jenis Pendapatan : </label>
                                    <div class="col-sm-8">
                                        <select name="jenis_pendapatan_id" id="jenis_pendapatan_id" class="select2 form-control r-0 light s-12">
                                            <option value="0"></option>
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label for="status_diskon" class="col-form-label s-12 col-md-3 text-right font-weight-bolder">Status Diskon : </label>
                                    <div class="col-sm-8">
                                        <select name="status_diskon" id="status_diskon" class="select2 form-control r-0 light s-12">
                                            <option value="">Pilih</option>
                                            <option value="0">Tidak Diskon</option>
                                            <option value="1">Diskon</option>
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label for="no_skrd" class="col-form-label s-12 col-md-3 text-right font-weight-bolder">No SKRD : </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="no_skrd" id="no_skrd" class="form-control r-0 s-12 col-md-12" autocomplete="off" required/>
                                    </div>
                                </div> 
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label class="col-form-label s-12 col-md-3 text-right font-weight-bolder" id="tgl_skrd_text">Tanggal SKRD :</label>
                                    <div class="col-sm-8 row">
                                        <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd" id="tgl_skrd" class="form-control r-0 light s-12 col-md-5 ml-3" autocomplete="off"/>
                                        <span class="mt-1 ml-2 mr-2"> - </span>
                                        <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="tgl_skrd1" id="tgl_skrd1" class="form-control r-0 light s-12 col-md-5" autocomplete="off"/>
                                    </div>
                                </div>
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label class="col-form-label s-12 col-md-3 text-right font-weight-bolder"></label>
                                    <div class="col-sm-5 row">
                                        <button class="btn btn-success btn-sm ml-3" onclick="pressOnChange()"><i class="icon-filter mr-2"></i>Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card no-b mb-2">
                            <div class="card-body">
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label for="status_diskon1" class="col-form-label s-12 col-md-3 text-right font-weight-bolder">Diskon<span class="text-danger ml-1">*</span> : </label>
                                    <div class="col-sm-8">
                                        <select name="status_diskon1" id="status_diskon1" class="select2 form-control r-0 light s-12">
                                            <option value="">Pilih</option>
                                            <option value="0">Tidak Diskon</option>
                                            <option value="1">Diskon</option>
                                        </select>
                                    </div>
                                </div> 
                                <div id="diskon_display" class="form-group row" style="margin-top: -8px !important">
                                    <label for="diskon" class="col-form-label s-12 col-md-3 text-right font-weight-bolder">Diskon<span class="text-danger ml-1">*</span> (%) : </label>
                                    <div class="col-sm-8">
                                        <input type="number" placeholder="0 - 100" min="0" max="100" name="diskon" id="diskon" class="form-control r-0 s-12 col-md-12" autocomplete="off" required/>
                                    </div>
                                </div> 
                                <div class="form-group row" style="margin-top: -8px !important">
                                    <label class="col-form-label s-12 col-md-3 text-right font-weight-bolder"></label>
                                    <div class="col-sm-5 row">
                                        <a href="#" data-toggle="modal" onclick="createRoute()" data-target="#exampleModalCenter" class="btn btn-sm btn-primary ml-3"><i class="icon-system_update_alt mr-2"></i>Update Diskon</a>
                                    </div>
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
                                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <th width="5%">No</th>
                                            <th width="8%">Nomor SKRD</th>
                                            <th width="22%">Nama Dinas</th>
                                            <th width="23%">Jenis Retribusi</th>
                                            <th width="8%">Tanggal SKRD</th>
                                            <th width="8%">Masa Berlaku</th>
                                            <th width="8%">Ketetapan</th>
                                            <th width="8%">Diskon</th>
                                            <th width="9%">Total Bayar</th>
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
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p class="font-weight-bold">Apakah anda yakin ingin mengupdate data ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal"><i class="icon-times mr-2"></i>Close</button>
                <a href="{{ route('diskon.updateDiskon') }}" class="btn btn-sm btn-primary" id="exportpdf"><i class="icon-system_update_alt mr-2"></i>Update Data</a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(function() {
        $('#diskon_display').hide(); 
        $('#status_diskon1').change(function(){
            if($('#status_diskon1').val() === "0") {
                $('#diskon_display').hide(); 
            } else {
                $('#diskon_display').show(); 
            } 
        });
    });

    var table = $('#dataTable').dataTable({
        processing: true,
        serverSide: true,
        order: [ 0, 'asc' ],
        pageLength: 25,
        searching: false,
        ajax: {
            url: "{{ route($route.'api') }}",
            method: 'POST',
            data: function (data) {
                data.tgl_skrd = $('#tgl_skrd').val();
                data.tgl_skrd1 = $('#tgl_skrd1').val();
                data.opd_id = $('#opd_id').val();
                data.jenis_pendapatan_id = $('#jenis_pendapatan_id').val();
                data.status_diskon = $('#status_diskon').val();
                data.no_skrd = $('#no_skrd').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'no_skrd', name: 'no_skrd'},
            {data: 'id_opd', name: 'id_opd'},
            {data: 'id_jenis_pendapatan', name: 'id_jenis_pendapatan'},
            {data: 'tgl_skrd', name: 'tgl_skrd'},
            {data: 'masa_berlaku', name: 'masa_berlaku'},
            {data: 'jumlah_bayar', name: 'jumlah_bayar'},
            {data: 'diskon', name: 'diskon'},
            {data: 'total_bayar', name: 'total_bayar'},
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

    function createRoute(){
        var opd_id = $('#opd_id').val();
        var jenis_pendapatan_id = $('#jenis_pendapatan_id').val();
        var tgl_skrd = $('#tgl_skrd').val();
        var tgl_skrd1 = $('#tgl_skrd1').val();
        var status_diskon = $('#status_diskon').val();
        var no_skrd = $('#no_skrd').val();

        var status_diskon1 = $('#status_diskon1').val();
        var diskon = $('#diskon').val();

        $('#exportpdf').attr('href', "{{ route('diskon.updateDiskon') }}?tgl_skrd=" + tgl_skrd + "&tgl_skrd1=" + tgl_skrd1 + "&opd_id=" + opd_id + "&jenis_pendapatan_id=" + jenis_pendapatan_id + "&status_diskon=" + status_diskon + "&status_diskon1=" + status_diskon1 + "&diskon=" + diskon + "&no_skrd=" + no_skrd)
    }

    function pressOnChange(){
        table.api().ajax.reload();
    }
</script>
@endsection
