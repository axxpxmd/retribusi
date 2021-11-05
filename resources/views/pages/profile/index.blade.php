@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-user-circle-o mr-2"></i>
                        {{ $title }}
                    </h4>
                </div>
            </div>
            <div class="row justify-content-between">
                <ul role="tablist" class="nav nav-material nav-material-white responsive-tab">
                    <li class="nav-item">
                        <a class="nav-link active show" id="tab1" data-toggle="tab" href="#profile" role="tab"><i class="icon icon-home2"></i>My Profile</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" id="tab2" data-toggle="tab" href="#edit-profile" role="tab"><i class="icon icon-pencil"></i>Edit Profile</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profile.editPassword', Auth::user()->id) }}"><i class="icon-key4 mr-2"></i>Ganti Password</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="profile">
                <div class="row">
                    <div class="col-md-6 container">
                        <div class="card mt-3">
                            <h6 class="card-header font-weight-bold">Data Pengguna</h6>
                            <div class="card-body">
                                <img class="mx-auto d-block rounded-circle img-circular" src="{{ asset('images/ava/default.png') }}" width="100" height="100" alt="Foto Profil">
                                <p class="text-center mt-2 font-weight-bold text-uppercase text-black-50">{{ $data->full_name }} <i class="icon-verified_user text-primary"></i> </p>
                                <p class="text-center" style="margin-top: -25px !important">NIP. {{ $data->nip }}</p>
                                <div class="col-md-12">
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Username :</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->user->username }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Role :</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->modelHasRole->role->name }}</label>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>OPD :</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->opd != null ? $data->opd->n_opd : '-' }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>Email :</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->email }}</label>
                                    </div>
                                    <div class="row">
                                        <label class="col-md-2 text-right s-12"><strong>No Telfon :</strong></label>
                                        <label class="col-md-10 s-12">{{ $data->phone }}</label>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-danger"><i class="mr-2 icon-power-off"></i>Logout</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane animated fadeInUpShort" id="edit-profile" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <div id="alert"></div>
                        <div class="card mt-3">
                            <h6 class="card-header"><strong>Edit Data</strong></h6>
                            <div class="card-body">
                                <form class="needs-validation" method="GET" action="" enctype="multipart/form-data" novalidate>
                                    {{ method_field('GET') }}
                                    <div class="form-row form-inline">
                                        <div class="col-md-12">
                                            <div class="form-group m-0">
                                                <label for="full_name" class="form-control label-input-custom col-md-2">Nama Lengkap<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="full_name" id="full_name" value="{{ $data->full_name }}" class="form-control r-0 light s-12 col-md-3" autocomplete="off" required/>
                                            </div>
                                            <div class="form-group m-0">
                                                <label for="nip" class="form-control label-input-custom col-md-2">NIK<span class="text-danger ml-1">*</span></label>
                                                <input type="number" name="nip" id="nip" value="{{ $data->nip }}" class="form-control r-0 light s-12 col-md-3" autocomplete="off" required/>
                                            </div>
                                            <div class="form-group m-0">
                                                <label for="email" class="form-control label-input-custom col-md-2">Email<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="email" id="email" value="{{ $data->email }}" class="form-control r-0 light s-12 col-md-3" autocomplete="off" required/>
                                            </div>
                                            <div class="form-group m-0">
                                                <label for="phone" class="form-control label-input-custom col-md-2">No Telp<span class="text-danger ml-1">*</span></label>
                                                <input type="text" name="phone" id="phone" value="{{ $data->phone }}" class="form-control r-0 light s-12 col-md-3" autocomplete="off" required/>
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
    // 
</script>
@endsection
