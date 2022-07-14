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
                        Edit {{ $title }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-edit"></i>Edit Data</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Edit Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="PATCH"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('PATCH') }}
                                    <input type="hidden" id="id" name="id" value="{{ $data->id }}"/>
                                    <div class="form-row form-inline">
                                        <div class="col-md-12">
                                            <div class="form-group m-0">
                                                <label for="id_jenis_pendapatan" class="form-control label-input-custom col-md-2">Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-5 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="id_jenis_pendapatan" id="id_jenis_pendapatan" autocomplete="off">
                                                        <option value="0">Pilih</option>
                                                        @foreach ($jenis_pendapatans as $i)
                                                            <option value="{{ $i->id }}">{{ $i->jenis_pendapatan }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label for="rincian_pendapatan" class="form-control label-input-custom col-md-2">Rincian Jenis Pendapatan<span class="text-danger ml-1">*</span></label>
                                                <textarea rows="4" name="rincian_pendapatan" id="rincian_pendapatan" class="form-control r-0 light s-12 col-md-5" autocomplete="off" required>{{ $data->rincian_pendapatan }}</textarea>
                                            </div> 
                                            <div class="form-group m-0">
                                                <label for="kd_jenis" class="form-control label-input-custom col-md-2">Kode Jenis<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="kd_jenis" id="kd_jenis" class="form-control r-0 light s-12 col-md-2" value="{{ $data->kd_jenis }}" autocomplete="off" required/>
                                            </div>  
                                            <div class="form-group m-0">
                                                <label for="no_hp" class="form-control label-input-custom col-md-2">No HP<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="no_hp" id="no_hp" class="form-control r-0 light s-12 col-md-2" value="{{ $data->no_hp }}" autocomplete="off" required/>
                                            </div>  
                                            <div class="form-group m-0">
                                                <label for="nmr_rekening" class="form-control label-input-custom col-md-2">Nomor Rekening<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="nmr_rekening" id="nmr_rekening" class="form-control r-0 light s-12 col-md-2" value="{{ $data->nmr_rekening }}" autocomplete="off" required/>
                                            </div>  
                                            <div class="form-group m-0">
                                                <label for="nmr_rekening_denda" class="form-control label-input-custom col-md-2">Nomor Rekening Denda<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="nmr_rekening_denda" id="nmr_rekening_denda" class="form-control r-0 light s-12 col-md-2" value="{{ $data->nmr_rekening_denda }}" autocomplete="off" required/>
                                            </div>  
                                            <div class="form-group mt-2">
                                                <div class="col-md-2"></div>
                                                <button type="submit" class="btn btn-primary btn-sm" id="action"><i class="icon-save mr-2"></i>Simpan Perubahan<span id="txtAction"></span></button>
                                            </div>
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
</div>
@endsection
@section('script')
<script type="text/javascript">
    $('#id_jenis_pendapatan').val("{{ $data->id_jenis_pendapatan }}");
    $('#id_jenis_pendapatan').trigger('change.select2');

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            $('#action').attr('disabled', true);
            url = "{{ route($route.'update', ':id') }}".replace(':id', $('#id').val());
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
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
                                keys: ['enter'],
                                action: function () {
                                    location.reload();
                                }
                            }
                        }
                    });
                },
                error : function(data){
                    err = '';
                    respon = data.responseJSON;
                    if(respon.errors){
                        $.each(respon.errors, function( index, value ) {
                            err = err + "<li>" + value +"</li>";
                        });
                    }
                    $('#alert').html("<div role='alert' class='alert alert-danger alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Error!</strong> " + respon.message + "<ol class='pl-3 m-0'>" + err + "</ol></div>");
                    $('#action').removeAttr('disabled');
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
    });
</script>
@endsection