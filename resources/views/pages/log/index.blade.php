@extends('layouts.app')
@section('title', '| '.$title.'')
@section('content')
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-documents mr-2"></i>
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
                        <div class="container col-md-8">
                            <div class="row mb-2">
                                <label for="channel_bayar" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Metode Bayar </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="channel_bayar" name="channel_bayar">
                                        <option value="0">Semua</option>
                                        <option value="1">Virtual Account</option>
                                        <option value="5">QRIS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="status" class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Status </label>
                                <div class="col-sm-8">
                                    <select class="select2 form-control r-0 light s-12" id="status" name="status">
                                        <option value="0">Semua</option>
                                        <option value="1">Berhasil</option>
                                        <option value="2">Gagal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-form-label s-12 col-md-2 text-right font-weight-bolder">Tanggal</label>
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="from" id="from" class="form-control r-0 light s-12 mb-5-m" autocomplete="off"/>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="date" placeholder="MM/DD/YYYY" value="{{ $today }}" name="to" id="to" class="form-control r-0 light s-12" autocomplete="off"/>
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
                                <div class="">
                                    <table id="dataTable" class="table display nowrap table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <th>No</th>
                                            <th>No Bayar </th>
                                            <th>NTB</th>
                                            <th>Nomor VA</th>
                                            <th>Invoice Qris</th>
                                            <th>Waktu </th>
                                            <th>Metode Bayar </th>
                                            <th>Status</th>
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
    var table = $('#dataTable').dataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        order: [ 0, 'asc' ],
        pageLength: 25,
        ajax: {
            url: "{{ route('log.index') }}",
            method: 'GET',
            data: function (data) {
                data.from = $('#from').val();
                data.to = $('#to').val();
                data.status = $('#status').val();
                data.channel_bayar = $('#channel_bayar').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, align: 'center', className: 'text-center'},
            {data: 'no_bayar', name: 'no_bayar'},
            {data: 'ntb', name: 'ntb'},
            {data: 'nomor_va', name: 'nomor_va'},
            {data: 'invoice_qris', name: 'invoice_qris'},
            {data: 'waktu', name: 'waktu'},
            {data: 'jenis', name: 'jenis'},
            {data: 'status', name: 'status', className: 'text-center'}
        ]
    });

    pressOnChange();
    function pressOnChange(){
        table.api().ajax.reload();
    }
</script>
@endsection
