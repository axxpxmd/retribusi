<div class="col-md-4">
    <div class="white">
        <div class="card">
            <div class="card-header bg-primary text-white b-b-light">
                <div class="row justify-content-end">
                    <div class="col">
                        <ul id="myTab4" role="tablist" class="nav nav-tabs card-header-tabs nav-material nav-material-white float-right">
                            <li class="nav-item">
                                <a class="nav-link active show" id="tab1" data-toggle="tab" href="#v-pills-tab1" role="tab" aria-controls="tab1" aria-expanded="true" aria-selected="true">Hari Ini</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab2" data-toggle="tab" href="#v-pills-tab2" role="tab" aria-controls="tab2" aria-selected="false">Bulan Ini</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body no-p">
                <div class="tab-content">
                    <div class="tab-pane animated fadeIn show active" id="v-pills-tab1" role="tabpanel" aria-labelledby="v-pills-tab1">
                        <div class="bg-primary text-white lighten-2">
                            <div class="pt-5 pb-2 pl-5 pr-5">
                                <div class="float-left">
                                    <h5 class="font-weight-normal s-14">SKRD</h5>
                                    <span class="s-48 font-weight-lighter text-primary">
                                        {{ $todaysskrd }}
                                    </span>
                                </div>
                                <div class="float-right">
                                    <h5 class="font-weight-normal s-14">STS</h5>
                                    <span class="s-48 font-weight-lighter text-primary">
                                        {{ $todayssts }}
                                    </span>
                                </div>
                            </div>
                            <canvas width="378" 
                                    height="30" 
                                    data-chart="spark"     
                                    data-chart-type="line"
                                    data-dataset="[[28,530,200,430]]" 
                                    data-labels="['a','b','c','d']"
                                    data-dataset-options="[{ borderColor:  'rgba(54, 162, 235, 1)', backgroundColor: 'rgba(54, 162, 235,1)' }]">
                            </canvas>
                        </div>
                        <div class="slimScroll b-b" data-height="268">
                            <div class="table-responsive p-1">
                                <table class="table table-hover earning-box">
                                    <thead class="no-b">
                                        <tr>
                                            <th width="20%">NO SKRD</th>
                                            <th width="35%">Nama</th>
                                            <th width="20%">Status Bayar</th>
                                            <th width="25%">Jumlah Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($todays->take(10) as $index => $i)
                                        <tr>
                                            <td>{{ $i->no_skrd }}</td>
                                            <td>{{ $i->nm_wajib_pajak }}</td>
                                            <td>{{ $i->status_bayar == 1 ? 'Sudah' : 'Belum' }}</td>
                                            <td>@currency($i->jumlah_bayar)</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane animated fadeIn" id="v-pills-tab2" role="tabpanel" aria-labelledby="v-pills-tab2">
                        <div class="bg-primary text-white lighten-2">
                            <div class="pt-5 pb-2 pl-5 pr-5">
                                <div class="float-left">
                                    <h5 class="font-weight-normal s-14">SKRD</h5>
                                    <span class="s-48 font-weight-lighter text-primary">
                                        {{ $monthsskrd }}
                                    </span>
                                </div>
                                <div class="float-right">
                                    <h5 class="font-weight-normal s-14">STS</h5>
                                    <span class="s-48 font-weight-lighter text-primary">
                                        {{ $monthssts }}
                                    </span>
                                </div>
                            </div>
                            <canvas width="378" 
                                    height="30" 
                                    data-chart="spark"     
                                    data-chart-type="line"
                                    data-dataset="[[28,530,200,430]]" 
                                    data-labels="['a','b','c','d']"
                                    data-dataset-options="[{ borderColor:  'rgba(54, 162, 235, 1)', backgroundColor: 'rgba(54, 162, 235,1)' }]">
                            </canvas>
                        </div>
                        <div class="slimScroll b-b" data-height="268">
                            <div class="table-responsive p-1">
                                <table class="table table-hover earning-box">
                                    <thead class="no-b">
                                        <tr>
                                            <th width="20%">NO SKRD</th>
                                            <th width="35%">Nama</th>
                                            <th width="20%">Status Bayar</th>
                                            <th width="25%">Jumlah Bayar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($months->take(10) as $index => $i)
                                        <tr>
                                            <td>{{ $i->no_skrd }}</td>
                                            <td>{{ $i->nm_wajib_pajak }}</td>
                                            <td>{{ $i->status_bayar == 1 ? 'Sudah' : 'Belum' }}</td>
                                            <td>@currency($i->jumlah_bayar)</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>