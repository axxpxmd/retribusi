@php
    $opd_id = Auth::user()->pengguna->opd_id;

    $dataTTD = App\Models\TransaksiOPD::when($opd_id != null, function($q) use($opd_id){
        return $q->where('id_opd', $opd_id);
    })->whereIn('status_ttd', [2,4])->count();
@endphp
<div class="has-sidebar-left ">
    <div class="sticky">
        <div class="navbar navbar-expand navbar-dark d-flex justify-content-between bd-navbar blue accent-3">
            <div class="relative">
                <div class="d-flex">
                    <div>
                        <a href="#" data-toggle="push-menu" class="paper-nav-toggle pp-nav-toggle">
                            <i></i>
                        </a>
                    </div>
                    <div class="row m-t-12">
                        <li type="none" class="mr-1 ml-2 fs-13 text-white">
                            <i class="icon icon-calendar-check-o"></i>
                            <a id="hari"></a>
                            ,
                            <a id="tanggal"></a>
                            <a id="bulan"></a>
                            <a id="tahun"></a>
                            /
                        </li>
                        <li type="none" class="fs-13 text-white">
                            <a id="jam"></a>
                            :
                            <a id="menit"></a>
                            :
                            <a id="detik"></a>
                        </li>
                    </div>
                </div>
            </div>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    @can('Tanda Tangan')
                    <li class="dropdown custom-dropdown notifications-menu">
                        <a href="#" class="nav-link" data-toggle="dropdown" aria-expanded="false">
                            <i class="icon-notifications "></i>
                            <span class="badge text-white font-weight-bold badge-mini">{{ $dataTTD ? $dataTTD : 0 }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="header">{{ $dataTTD ? 'Terdapat ' . $dataTTD . ' SKRD belum di TTD' : 'Tidak ada notifikasi' }}</li>
                            <li>
                                @if ($dataTTD)
                                <ul class="menu">
                                    <li>
                                        <a href="{{ route('tanda-tangan.index', ['belum_ttd' => 1]) }}">
                                            <i class="icon icon-data_usage text-success"></i> Lihat semua.
                                        </a>
                                    </li>
                                </ul>
                                @endif
                            </li>
                            <li class="footer p-2 text-center"><a href="#">{{ $dataTTD ? '' : 'Kosong' }}</a></li>
                        </ul>
                    </li>
                    @endcan
                    <li class="dropdown custom-dropdown user user-menu ">
                        <a href="#" class="nav-link" data-toggle="dropdown">
                            <img height="30" width="30" style="margin-top: -10px" class="rounded-circle img-circular" src="{{ asset('images/ava/'.Auth::user()->pengguna->photo) }}" alt="User Image">
                            <i class="icon-more_vert"></i>
                        </a>
                        <div class="dropdown-menu p-4 dropdown-menu-right" style="width:255px">
                            <div class="box justify-content-between">
                                <div class="col">
                                    <a href="{{ route('profile.index') }}">
                                        <i class="icon-user amber-text lighten-2 avatar r-5"></i>
                                        <div class="pt-1">Profile</div>
                                    </a>
                                </div>
                                <div class="col">
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="list-group-item list-group-item-action mt-2"><i class="mr-2 icon-power-off text-danger"></i>Logout</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    // Hours
    window.setTimeout("waktu()", 1000);

    function addZero(i) {
        if (i < 10) {
            i = "0" + i;
        }
        return i;
    }

    function waktu() {
        var waktu = new Date();
        setTimeout("waktu()", 1000);
        document.getElementById("jam").innerHTML = addZero(waktu.getHours());
        document.getElementById("menit").innerHTML = addZero(waktu.getMinutes());
        document.getElementById("detik").innerHTML = addZero(waktu.getSeconds());
    }

    // Day
    arrHari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu"]
    Hari = new Date().getDay();
    document.getElementById("hari").innerHTML = arrHari[Hari];

    // Date
    Tanggal = new Date().getDate();
    document.getElementById("tanggal").innerHTML = Tanggal;

    // Month
    arrbulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    Bulan = new Date().getMonth();
    document.getElementById("bulan").innerHTML = arrbulan[Bulan];

    // Year
    Tahun = new Date().getFullYear();
    document.getElementById("tahun").innerHTML = Tahun;

</script>