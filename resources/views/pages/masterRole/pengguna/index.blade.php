@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-user-o mr-2"></i>
                        {{ $title }}
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
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content my-3" id="pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="semua-data" role="tabpanel">
                <div class="card no-b mb-2">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="opd_filter" class="col-form-label s-12 col-md-4 text-right font-weight-bold">OPD : </label>
                            <div class="col-sm-4">
                                <select name="opd_filter" id="opd_filter" class="select2 form-control r-0 light s-12" onchange="selectOnChange()">
                                    <option value="0">Semua</option>
                                    @foreach ($opds as $i)
                                        <option value="{{ $i->id }}">{{ $i->n_opd }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" style="margin-top: -8px !important">
                            <label for="role_filter" class="col-form-label s-12 col-md-4 text-right font-weight-bold">Role : </label>
                            <div class="col-sm-4">
                                <select name="role_filter" id="role_filter" class="select2 form-control r-0 light s-12" onchange="selectOnChange()">
                                    <option value="0">Semua</option>
                                    @foreach ($roles as $i)
                                        <option value="{{ $i->id }}">{{ $i->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card no-b">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <th width="5%">No</th>
                                            <th width="19%">Nama</th>
                                            <th width="30%">OPD</th>
                                            <th width="7%">Role</th>
                                            <th width="9%">No Telp</th>
                                            <th width="10%">Nama Login</th>
                                            {{-- <th width="10%">Foto</th> --}}
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
            <div class="tab-pane animated fadeInUpShort" id="tambah-data" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card">
                            <h6 class="card-header"><strong>Tambah Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" id="form" method="POST"  enctype="multipart/form-data" novalidate>
                                    {{ method_field('POST') }}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">Role<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="select2 form-control bg s-12" id="role_id" name="role_id" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        @foreach ($roles as $i)
                                                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">Username<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="username" id="username" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">Password<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="password" name="password" id="password" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">Nama Lengkap<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="full_name" id="full_name" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2" id="nip_display">
                                                <label class="col-form-label s-12 col-sm-4 text-right">NIP<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="nip" id="nip" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2" id="nip_display">
                                                <label class="col-form-label s-12 col-sm-4 text-right">NIK<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="nik" id="nik" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2" id="opd_display">
                                                <label class="col-form-label s-12 col-sm-4 text-right">OPD<span class="text-danger ml-1">*</span></label>
                                                <div class="col-sm-8">
                                                    <select class="select2 form-control bg s-12" id="opd_id" name="opd_id" autocomplete="off">
                                                        <option value="">Pilih</option>
                                                        @foreach ($opds as $i)
                                                            <option value="{{ $i->id }}">{{ $i->n_opd }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">Email</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="email" id="email" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label class="col-form-label s-12 col-sm-4 text-right">No Telp</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="phone" id="phone" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2" id="url_callback_display">
                                                <label class="col-form-label s-12 col-sm-4 text-right">URL Callback</label>
                                                <div class="col-sm-8">
                                                    <input type="text" name="url_callback" id="url_callback" class="form-control r-0 s-12" autocomplete="off" required/>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <label class="col-sm-4"></label>
                                                <div class="col-md-8">
                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="icon-save mr-2"></i>Simpan</button>
                                                    <a class="btn btn-sm" onclick="add()" id="reset">Reset</a>
                                                </div>
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
@include('pages.masterRole.pengguna.show')
@endsection
@section('script')
<script type="text/javascript">
    $(function() {
        $('#opd_display').hide();
        $('#nip_display').hide();
        $('#url_callback_display').hide();

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

            if (role_id == 12) {
                $('#url_callback_display').show();
                $('#url_callback').prop('required', true);
                $('#nip_display').hide();
            }else{
                $('#url_callback_display').hide();
                $('#url_callback').prop('required', false);
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

    var table = $('#dataTable').dataTable({
        processing: true,
        serverSide: true,
        order: [ 0, 'asc' ],
        ajax: {
            url: "{{ route($route.'api') }}",
            method: 'POST',
            data: function (data) {
                data.opd_id = $('#opd_filter').val();
                data.role_id = $('#role_filter').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'full_name', name: 'full_name'},
            {data: 'opd', name: 'opd', orderable: false, searchable: false},
            {data: 'role', name: 'role',  orderable: false, searchable: false},
            {data: 'phone', name: 'phone'},
            {data: 'user_id', name: 'user_id'},
            // {data: 'photo', name: 'photo'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
        ]
    });

    function selectOnChange(){
        $('#dataTable').DataTable().ajax.reload();
    }

    function add(){
        save_method = "add";
        $('#form').trigger('reset');
        $('input[name=_method]').val('POST');
        $('#reset').show();
        $('#opd_id').val(0);
        $('#opd_id').trigger('change.select2');
        $('#username').focus();
        $('#role_id').val("");
        $('#role_id').trigger('change.select2');
    }

    function show(id) {
        $('#myModal').modal('show');
        $.get("{{ route($route.'show', ':id') }}".replace(':id', id), function(data){
            $('#namaLogin').html(data.username);
            $('#fullName').html(data.full_name);
            $('#email_').html(data.email);
            $('#noTelp').html(data.phone);
            var path = "{{ $path }}" + data.photo;
            $('#photo_').attr({'src': path});
        }, "JSON").fail(function(){
            reload();
        });
    }

    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            url = "{{ route($route.'store') }}",
            $.ajax({
                url : url,
                type : 'POST',
                data: new FormData(($(this)[0])),
                contentType: false,
                processData: false,
                success : function(data) {
                    $('#alert').html("<div role='alert' class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Success!</strong> " + data.message + "</div>");
                    table.api().ajax.reload();
                    add();
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
                }
            });
            return false;
        }
        $(this).addClass('was-validated');
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
                            table.api().ajax.reload();
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
