@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row">
                <div class="col">
                    <h4 class="ml-1">
                        <i class="icon icon-user-o mr-2"></i>
                        Show {{ $title }} | {{ $pengguna->full_name }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li>
                        <a class="nav-link" href="{{ route($route.'index') }}"><i class="icon icon-arrow_back"></i>Semua Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#semua-data" role="tab"><i class="icon icon-user-o"></i>Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab2" data-toggle="tab" href="#edit-data" role="tab"><i class="icon icon-edit"></i>Edit Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.editPassword', $pengguna->user_id) }}"><i class="icon icon-key3"></i>Ganti Password</a>
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
                        <div class="card mt-2">
                            <h6 class="card-header"><strong>Data Pengguna</strong></h6>
                            <div class="card-body">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Role :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->modelHasRole->role->name }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Username :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->user->username }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Nama Lengkap :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->full_name }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>NIP :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->nip }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>NIK :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->nik }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>OPD :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->opd != null ? $pengguna->opd->n_opd : '' }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Email :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->email }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>No. Telp :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->phone }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>API KEY :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->api_key }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>URL Callback :</strong></label>
                                        <label class="col-md-10 s-12">{{ $pengguna->url_callback }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Foto :</strong></label>
                                        <img class="ml-2 m-t-7 img-circular rounded-circle" src="{{ asset($path.$pengguna->photo) }}" width="100" height="100" alt="icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane animated fadeInUpShort show" id="edit-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Edit Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="PATCH"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('PATCH') }}
                                    <input type="hidden" id="id" name="id" value="{{ $pengguna->id }}"/>
                                    <div class="form-row form-inline">
                                        <div class="col-md-8">
                                            <div class="form-group m-0">
                                                <label for="role_id" class="form-control label-input-custom col-md-2">Role<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-6 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="role_id" id="role_id" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        @foreach ($roles as $i)
                                                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group mt-1">
                                                <label for="username" class="form-control label-input-custom col-md-2">Username<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="username" id="username" class="form-control r-0 light s-12 col-md-6" value="{{ $pengguna->user->username }}" autocomplete="off" required/>
                                            </div> 
                                            <hr>
                                            <div class="form-group m-0">
                                                <label for="full_name" class="form-control label-input-custom col-md-2">Nama Lengkap<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="full_name" id="full_name" class="form-control r-0 light s-12 col-md-6" value="{{ $pengguna->full_name }}" autocomplete="off" required/>
                                            </div> 
                                            <div class="form-group m-0" id="nip_display">
                                                <label for="nip" class="form-control label-input-custom col-md-2">NIP<span class="text-danger ml-1" id="nip_required"></span></label>
                                                <input type="number" name="nip" id="nip" class="form-control r-0 light s-12 col-md-6" value="{{ $pengguna->nip }}" autocomplete="off"/>
                                            </div> 
                                            <div class="form-group m-0" id="nip_display">
                                                <label for="nik" class="form-control label-input-custom col-md-2">NIK<span class="text-danger ml-1" id="nik_required"></span></label>
                                                <input type="number" name="nik" id="nik" class="form-control r-0 light s-12 col-md-6" value="{{ $pengguna->nik }}" autocomplete="off"/>
                                            </div> 
                                            <div class="form-group mb-1" id="opd_display">
                                                <label for="opd_id" class="form-control label-input-custom col-md-2">OPD<span class="text-danger ml-1">*</span></label>
                                                <div class="col-md-6 p-0 bg-light">
                                                    <select class="select2 form-control r-0 light s-12" name="opd_id" id="opd_id" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        @foreach ($opds as $i)
                                                            <option value="{{ $i->id }}">{{ $i->n_opd }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="form-control label-input-custom col-md-2">Email<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="email" id="email" class="form-control r-0 light s-12 col-md-6" value="{{ $pengguna->email }}" autocomplete="off" required/>
                                            </div> 
                                            <div class="form-group m-0">
                                                <label for="phone" class="form-control label-input-custom col-md-2">No. Telp<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="phone" id="phone" class="form-control r-0 light s-12 col-md-6" value="{{ $pengguna->phone }}" autocomplete="off" required/>
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
    $('#opd_id').val("{{ $pengguna->opd_id }}");
    $('#opd_id').trigger('change.select2');
    $('#role_id').val("{{ $pengguna->modelHasRole->role->id }}");
    $('#role_id').trigger('change.select2');
    
    $(function() {
        var role_id = $('#role_id').val();
        if(role_id == 7 || role_id == 0) {
            $('#opd_display').hide(); 
            $('#nip_display').hide(); 
            $('#nip').val(''); 
            $('#opd_id').val("0");
            $('#opd_id').trigger('change.select2');
        } else {
            $('#opd_display').show(); 
            $('#nip_display').show(); 
        } 

        if (role_id == 11) {
            $('#nip_required,#nik_required').html('*'); 
            $('#nip,#nik').prop('required', true); 
        } else {
            $('#nip_required,#nik_required').html(''); 
            $('#nip,#nik').prop('required', false);
        }
       
        $('#role_id').change(function(){
            var role_id = $('#role_id').val();
            if(role_id == 7 || role_id == 0) {
                $('#opd_display').hide(); 
                $('#nip_display').hide(); 
                $('#nip').val(''); 
                $('#opd_id').val("");
                $('#opd_id').trigger('change.select2');
            } else {
                $('#opd_display').show(); 
                $('#nip_display').show(); 
            } 

            if (role_id == 11) {
                $('#nip_required,#nik_required').html('*'); 
                $('#nip,#nik').prop('required', true); 
            } else {
                $('#nip_required,#nik_required').html(''); 
                $('#nip,#nik').prop('required', false);
            }
        });
    });

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