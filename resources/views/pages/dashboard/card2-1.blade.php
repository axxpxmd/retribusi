
<div class="card">
    <div class="card-header white">
        <div class="row justify-content-end">
            <div class="col">
                <ul class="nav nav-tabs card-header-tabs nav-material">
                    <li class="nav-item">
                        <a class="nav-link active show" id="w1-tab1" data-toggle="tab" href="#v-pills-w1-tab1">KESELURUHAN DATA</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body no-p">
        <div class="tab-content">
            <div class="tab-pane animated fadeInRightShort show active" id="v-pills-w1-tab1" role="tabpanel" aria-labelledby="v-pills-w1-tab1">
                <div class="row p-3">
                    <div class="col-md-5 pt-2">
                        @include('pages.dashboard.pieChart2')
                    </div>
                    <div class="col-md-7">
                        <div class="card-body pt-0">
                            <h6></h6>
                            <div class="my-3">
                                <div class="float-right">
                                    <a href="#" class="btn-fab btn-fab-sm btn-primary r-5">
                                        <i class="icon-mail-envelope-closed2 p-0"></i>
                                    </a>
                                    <a href="#" class="btn-fab btn-fab-sm btn-success r-5">
                                        <i class="icon-star p-0"></i>
                                    </a>
                                </div>
                                <div class="mr-3 float-left">
                                    <img src="{{ asset('images/template/logo.png') }}" width="50" alt="">
                                </div>
                                <div>
                                    <strong><a href="#" target="blank">{{ Auth::user()->pengguna->opd->n_opd }}</a></strong>
                                </div>
                                <div>
                                    <small>Kota Tangerang Selatan</small>
                                </div>
                                <div class="table-responsive mt-4">
                                    <table class="table table-hover">
                                        <tbody>
                                        <tr class="no-b">
                                            <th></th>
                                            <th>Jenis Pendapatan</th>
                                            <th>Total Bayar</th>
                                            <th>Jumlah</th>
                                        </tr>
                                        @forelse ($jenisPendapatanOpds as $index => $i)
                                        <tr>
                                            <td width="5%" class="text-center">{{  $index + $jenisPendapatanOpds->firstItem() }}</td>
                                            <td width="50%" class="text-uppercase" style="font-size: 13px !important">{{ $i->jenis_pendapatan->jenis_pendapatan }}</td>
                                            <td width="30%">Rp. {{ number_format($i->total_bayar) }}</td>
                                            <td width="15%" class="sc-counter">{{ $i->jumlah }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td></td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        </tr>
                                        @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th>Total Sudah Bayar</th>
                                                <th>Rp. {{ number_format($jenisPendapatanTotalSudahBayar->total_bayar) }}</th>
                                                <th>{{ $jenisPendapatanTotalSudahBayar->jumlah }}</th>
                                            </tr>
                                        </tfoot>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th>Total</th>
                                                <th>Rp. {{ number_format($jenisPendapatanTotal->total_bayar) }}</th>
                                                <th>{{ $jenisPendapatanTotal->jumlah }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div>
                                        {{ $jenisPendapatanOpds->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>