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
        <div id="alert"></div>
        <div class="tab-content " id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="card no-b mb-2">
                    <div class="card-body">
                        <div class="col-md-8 container">
                            <div class="row mb-2">
                                <label for="opd" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">OPD</label>
                                <div class="col-sm-8">
                                    <select id="opd" class="select2 form-control r-0 s-12">
                                        @if ($role == 'super-admin' || $role == 'admin-bjb')
                                            <option value="0">Semua</option>
                                        @endif
                                        @foreach ($opds as $i)
                                            <option value="{{ $i->id }}" {{ $opd_id == $i->id ? 'selected' : '' }}>{{ $i->n_opd }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="no_skrd" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">No SKRD</label>
                                <div class="col-sm-8">
                                    <input type="text" name="no_skrd" id="no_skrd" class="form-control r-0 s-12" placeholder="Masukan No SKRD" autocomplete="off">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-8">
                                    <button class="btn btn-success btn-sm" onclick="pressOnChange()"><i class="icon-search mr-2"></i>Cari Data</button>
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
                                            <th>Nama</th>
                                            <th>Jenis Retribusi</th>
                                            <th>Tanggal SKRD</th>
                                            <th>Masa Berlaku SKRD</th>
                                            <th>Ketetapan</th>
                                            <th>Aksi</th>
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
<!-- Send TTD -->
<div class="modal fade" id="batalSkrd" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form class="needs-validation" id="form" method="POST"  enctype="multipart/form-data" novalidate>
                {{ method_field('POST') }}
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="text-center">
                            <i class="icon-exclamation-triangle fs-40 text-danger"></i>
                            <p class="fs-14 font-weight-bold">Apakah Anda yakin akan membatalkan data SKRD ini ?</p>
                        </div>
                        <hr>
                        <div id="alertError"></div>
                        <div class="row mb-1">
                            <label for="keterangan" class="col-form-label font-weight-bold s-12 col-md-3">Keterangan<span class="text-danger ml-1">*</span></label>
                            <div class="col-md-9">
                                <textarea type="text" rows="3" name="keterangan" id="keterangan" placeholder="Berikan keterangan" class="form-control r-0 s-12" autocomplete="off" required></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <label for="file_pendukung" class="col-form-label font-weight-bold s-12 col-md-3">File</label>
                            <div class="col-sm-9">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file_pendukung" id="inputGroupFile"/>
                                    <label for="inputGroupFile" class="custom-file-label">File Pendukung</label>
                                    <span class="text-danger fs-10">format : PDF, JPG, PNG, JPEG</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-right">
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><i class="icon-times mr-2"></i>Tutup</button>
                        <button type="submit" id="action" class="btn btn-sm btn-danger ml-2"><i class="icon-exclamation mr-2"></i>Batal SKRD</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    var table = $('#dataTable').dataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        order: [ 0, 'asc' ],
        pageLength: 25,
        ajax: {
            url: "{{ route('batalSkrd.cari') }}",
            method: 'GET',
            data: function (data) {
                data.no_skrd    = $('#no_skrd').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'no_skrd', name: 'no_skrd'},
            {data: 'nm_wajib_pajak', name: 'nm_wajib_pajak'},
            {data: 'id_jenis_pendapatan', name: 'id_jenis_pendapatan'},
            {data: 'tgl_skrd', name: 'tgl_skrd'},
            {data: 'masa_berlaku', name: 'masa_berlaku'},
            {data: 'jumlah_bayar', name: 'jumlah_bayar'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
        ]
    });

    function pressOnChange(){
        table.api().ajax.reload();
    }

    function batalSkrd(id){
        $('#batalSkrd').modal('show');
        $('#batalSkrd').modal({keyboard: false});

        $('#id').val(id);
    }

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#action').attr('disabled', true);
            $('#alert').html('');
            $('#alertError').html('');
            url = "{{ route('batalSkrd.batal') }}";
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    $('#alert').html("<div class='alert alert-success alert-dismissible' role='alert'><strong>Sukses!</strong> " + data.message + "</div>");
                    $('#form').removeClass('was-validated');
                    $('#batalSkrd').modal('toggle');
                    pressOnChange();
                },
                error : function(data){
                    err = '';
                    respon = data.responseJSON;
                    if(respon.errors){
                        $.each(respon.errors, function( index, value ) {
                            err = err + "<li>" + value +"</li>";
                        });
                    }
                    $('#alertError').html("<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Error!</strong> " + respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");
                    $('#action').removeAttr('disabled');
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });

  
    $('#inputGroupFile').on('change',function(){
        // get the file name
        var fileName = $(this).val();
        text = fileName.substring(fileName.lastIndexOf("\\") + 1, fileName.length);
        // replace the "Choose a file" label
        $(this).next('.custom-file-label').html(text);
    })
    
</script>
@endsection
