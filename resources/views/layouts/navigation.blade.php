@php
    $dateNow = Carbon\Carbon::now()->format('Y-m-d');
    $opd_id = Auth::user()->pengguna->opd_id;
    $nip = Auth::user()->pengguna->nip;
    
    $dataTTD = App\Models\TransaksiOPD::when($opd_id != null, function ($q) use ($opd_id) {
        return $q->where('id_opd', $opd_id);
    })
        ->when($nip, function ($q) use ($nip) {
            return $q->where('nip_ttd', $nip);
        })
        ->whereIn('status_ttd', [2, 4])
        ->count();
    
    $dataSTRD = App\Models\TransaksiOPD::when($opd_id != null, function ($q) use ($opd_id) {
        return $q->where('id_opd', $opd_id);
    })
        ->where('status_bayar', 0)
        ->where('tgl_skrd_akhir', '<', $dateNow)
        ->count();
@endphp
<ul class="sidebar-menu">
    <li class="header"><strong>MAIN NAVIGATION</strong></li>
    <li>
        <a href="{{ route('home') }}">
            <i class="icon icon-dashboard2 blue-text s-18"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Permission : Role|Permission|Pengguna -->
    @canany(['Role', 'Permission', 'Pengguna'])
        <li class="header light"><strong>MASTER ROLES</strong></li>
    @endcanany
    @can('Permission')
        <li class="no-b">
            <a href="{{ route('master-role.permission.index') }}">
                <i class="icon icon-clipboard-list text-success s-18"></i>
                <span>Permission</span>
            </a>
        </li>
    @endcan
    @can('Role')
        <li>
            <a href="{{ route('master-role.role.index') }}">
                <i class="icon icon-key3 amber-text s-18"></i>
                <span>Role</span>
            </a>
        </li>
    @endcan
    @can('Pengguna')
        <li class="no-b">
            <a href="{{ route('master-role.pengguna.index') }}">
                <i class="icon icon-user-o text-success s-18 mr-1"></i>
                <span>Pengguna</span>
            </a>
        </li>
    @endcan

    <!-- Permission : Jenis Pendapatan|Rincian Pendapatan|OPD -->
    @canany(['Jenis Pendapatan', 'Rincian Pendapatan', 'OPD'])
        <li class="header light"><strong>MASTER DATA</strong></li>
    @endcanany
    @can('Jenis Pendapatan')
        <li class="no-b">
            <a href="{{ route('jenis-pendapatan.index') }}">
                <i class="icon icon-document-list purple-text s-18"></i>
                <span>Jenis Pendapatan</span>
            </a>
        </li>
    @endcan
    @can('Rincian Pendapatan')
        <li class="no-b">
            <a href="{{ route('rincian-jenis.index') }}">
                <i class="icon icon-document-list blue-text s-18"></i>
                <span>Rincian Pendapatan</span>
            </a>
        </li>
    @endcan
    @can('OPD')
        <li class="no-b">
            <a href="{{ route('opd.index') }}">
                <i class="icon icon-document-list amber-text s-18"></i>
                <span>OPD</span>
            </a>
        </li>
    @endcan

    <!-- Permission : Kuota Booking|Data Booking|Cari No Booking -->
    @canany(['Kuota Booking', 'Data Booking', 'Cari No Booking'])
        <li class="header light"><strong>MASTER BOOKING</strong></li>
    @endcanany
    @can('Kuota Booking')
        <li class="no-b">
            <a href="{{ route('booking.kuotaBooking') }}">
                <i class="icon icon-document-list text-primary s-18"></i>
                <span>Kuota Booking</span>
            </a>
        </li>
    @endcan
    @can('Cari No Booking')
        <li class="no-b">
            <a href="{{ route('booking.search') }}">
                <i class="icon icon-document-list text-success s-18"></i>
                <span>Cari No Booking</span>
            </a>
        </li>
    @endcan
    @can('Data Booking')
        <li class="no-b">
            <a href="{{ route('booking.index') }}">
                <i class="icon icon-document-list purple-text s-18"></i>
                <span>Data Booking</span>
            </a>
        </li>
    @endcan
 
    <!-- Permission : Cari No SKRD|Batal SKRD -->
    @canany(['Cari No SKRD', 'Batal SKRD'])
        <li class="header light"><strong>MASTER BATAL SKRD</strong></li>
    @endcanany
    @can('Cari No SKRD')
        <li class="no-b">
            <a href="{{ route('batalSkrd.cari') }}">
                <i class="icon icon-document-list text-primary s-18"></i>
                <span>Cari No SKRD</span>
            </a>
        </li>
    @endcan
    @can('Batal SKRD')
        <li class="no-b">
            <a href="{{ route('batalSkrd.index') }}">
                <i class="icon icon-document-list text-danger s-18"></i>
                <span>Batal SKRD</span>
            </a>
        </li>
    @endcan

    <!-- Permission : Data WP|SKRD|STS|Diskon|Denda|Laporan -->
    @canany(['Data WP', 'SKRD', 'STS', 'Diskon', 'Denda', 'Laporan', 'Tanda Tangan'])
        <li class="header light"><strong>MASTER MENU</strong></li>
    @endcanany
    @can('Data WP')
        <li class="no-b">
            <a href="{{ route('datawp.index') }}">
                <i class="icon icon-document-list purple-text s-18"></i>
                <span>Data WR</span>
            </a>
        </li>
    @endcan
    @can('SKRD')
        <li class="no-b">
            <a href="{{ route('skrd.index') }}">
                <i class="icon icon-document-list text-red s-18"></i>
                <span>SKRD</span>
            </a>
        </li>
    @endcan
    @can('STRD')
        <li class="no-b">
            <a href="{{ route('strd.index') }}">
                <i class="icon icon-document-list green-text s-18"></i>
                <span>STRD</span>
                <span class="float-right mr-4 text-danger font-weight-bold">{{ $dataSTRD }}</span>
            </a>
        </li>
    @endcan
    @can('STS')
        <li class="no-b">
            <a href="{{ route('sts.index') }}">
                <i class="icon icon-document-list blue-text s-18"></i>
                <span>STS</span>
            </a>
        </li>
    @endcan
    @can('Diskon')
        <li class="no-b">
            <a href="{{ route('diskon.index') }}">
                <i class="icon icon-document-list text-success s-18"></i>
                <span>Diskon</span>
            </a>
        </li>
    @endcan
    @can('Denda')
        <li class="no-b">
            <a href="{{ route('denda.index') }}">
                <i class="icon icon-document-list red-text s-18"></i>
                <span>Denda</span>
            </a>
        </li>
    @endcan
    @can('Tanda Tangan')
        <li class="no-b">
            <a href="{{ route('tanda-tangan.index') }}">
                <i class="icon icon-document-list amber-text s-18"></i>
                <span>Tanda Tangan</span>
                <span class="float-right mr-4 text-danger font-weight-bold">{{ $dataTTD }}</span>
            </a>
        </li>
    @endcan
    @can('Laporan')
        <li class="no-b">
            <a href="{{ route('report.index') }}">
                <i class="icon icon-documents black-text s-18"></i>
                <span>Laporan</span>
            </a>
        </li>
    @endcan
    @can('Log')
        <li class="no-b">
            <a href="{{ route('log.index') }}">
                <i class="icon icon-documents text-primary s-18"></i>
                <span>Log</span>
            </a>
        </li>
    @endcan
</ul>
