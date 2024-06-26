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
                        Edit {{ $title }} | {{ $data->n_opd }}
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
                                                <label for="kode" class="col-form-label s-12 col-md-2">Kode<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="kode" id="kode" class="form-control r-0 light s-12 col-md-4" value="{{ $data->kode }}" autocomplete="off" required/>
                                            </div> 
                                            <div class="form-group m-0">
                                                <label for="n_opd" class="col-form-label s-12 col-md-2">Nama OPD<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="n_opd" id="n_opd" class="form-control r-0 light s-12 col-md-4" value="{{ $data->n_opd }}" autocomplete="off" required/>
                                            </div> 
                                            <div class="form-group m-0">
                                                <label for="initial" class="col-form-label s-12 col-md-2">Inisial<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="initial" id="initial" class="form-control r-0 light s-12 col-md-4" value="{{ $data->initial }}" autocomplete="off" required/>
                                            </div>  
                                            <div class="form-group m-0">
                                                <label for="npwpd" class="col-form-label s-12 col-md-2">NPWPD</label>
                                                <input type="text" name="npwpd" id="npwpd" class="form-control r-0 light s-12 col-md-4" value="{{ $data->npwpd }}" autocomplete="off"/>
                                            </div> 
                                            <div class="form-group m-0">
                                                <label for="alamat" class="col-form-label s-12 col-md-2">Alamat</label>
                                                <textarea type="text" rows="4" name="alamat" id="alamat" class="form-control r-0 light s-12 col-md-4" autocomplete="off">{{ $data->alamat }}</textarea>
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