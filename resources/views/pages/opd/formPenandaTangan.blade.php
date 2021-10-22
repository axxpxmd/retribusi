@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left bg-light">
    <header class="blue accent-3 relative nav-sticky">
         <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-document-list mr-2"></i>
                        {{ $title }} | {{ $opd->n_opd }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul class="nav nav-material nav-material-white responsive-tab" id="v-pegawai-tab" role="tablist">
                    <li>
                        <a class="nav-link" href="{{ route('opd.index') }}"><i class="icon icon-arrow_back"></i>Kembali</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid my-3">
        <div id="alert"></div>
        <div class="card">
            <div class="card-body">
                <div id="formPermission">
                    <div class="row">
                        <div class="col-6">
                            <form class="needs-validation" id="form" method="POST" novalidate>
                                {{ method_field('POST') }}
                                <input type="hidden" id="id" name="id" value="{{ $opd->id }}"/>
                                <div class="form-row form-inline">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="penanda_tangans" class="col-form-label col-md-3">Penanda Tangan :</label>
                                            <div class="col-md-9 p-0">
                                                <select name="penanda_tangans[]" id="penanda_tangan" placeholder="" class="select2 form-control r-0 light s-12" multiple="multiple" required>
                                                    @foreach($penanda_tangans as $key=>$i)
                                                    <option value="{{ $i->user_id }}">{{ $i->full_name }} ( {{ $i->nik }} )</option>
                                                    @endforeach
                                                <select>
                                            </div>
                                        </div>
                                        <div class="form-group mt-2">
                                            <div class="col-md-3"></div>
                                            <button type="submit" class="btn btn-primary btn-sm" id="action2"><i class="icon-save mr-2"></i>Simpan</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-6 mt-2">
                            <strong>List Penanda Tangan:</strong>
                            <ol id="viewPermission" class=""></ol>
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
    getPermissions();
    $('#form').on('submit', function (e) {
        if ($(this)[0].checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
        }
        else{
            $('#alert').html('');
            $('#action').attr('disabled', true);
            $.post("{{ route($route.'storePenandaTangan') }}", $(this).serialize(), function(data){
                $('#alert').html("<div role='alert' class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>Success!</strong> " + data.message + "</div>");
                getPermissions();
                location.reload();
            }, "JSON").fail(function(data){
                err = ''; respon = data.responseJSON;
                $.each(respon.errors, function( index, value ) {
                    err += "<li>" + value +"</li>";
                });
            }).always(function(){
                $('#action').removeAttr('disabled');
            });
            return false;
        }
        $(this).addClass('was-validated');
    });

    function getPermissions(){
        $('#viewPermission').html("Loading...");
        urlPermission = "{{ route($route.'getPenandaTangan', ':id') }}".replace(':id', $('#id').val());
        $.get(urlPermission, function(data){
            $('#viewPermission').html("");
            if(data.length > 0){
                $.each(data, function(index, value){
                    val = "'" + value.id + "'";
                    $('#viewPermission').append('<li>' + value.full_name + ' [ '+value.nik+' ] <a href="#" onclick="removePermission(' + val + ')" class="text-danger" title="Hapus Data"><i class="icon-remove"></i></a></li>');
                });
            }else{
                $('#viewPermission').html("<em>Penanda Tangan kosong.</em>");
            }
        });
    }

    function removePermission(name){
        $.confirm({
            title: '',
            content: 'Apakah Anda yakin akan menghapus data ini?',
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
                        $.post("{{ route($route.'destroyPenandaTangan', ':name') }}".replace(':name', name), {'_method' : 'DELETE', 'id' : $('#id').val()}, function(data){
                            getPermissions();
                            location.reload();
                        }, "JSON").fail(function(){
                            reload();
                        });
                    }
                },
                cancel: function(){
                    console.log('the user clicked cancel');
                }
            }
        });
    }
</script>
@endsection
