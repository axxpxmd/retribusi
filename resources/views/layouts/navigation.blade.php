@php
    $opd_id = Auth::user()->pengguna->opd_id;
@endphp
<ul class="sidebar-menu">
    <li class="header"><strong>MAIN NAVIGATION</strong></li>
    <li>
        <a href="{{ route('home') }}">
            <i class="icon icon-dashboard2 blue-text s-18"></i> 
            <span>Dashboard</span>
        </a>
    </li>
    @if ($opd_id == 0)
    <li class="header light"><strong>MASTER DATA</strong></li>
    <li class="no-b">
        <a href="{{ route('pengguna.index') }}">
            <i class="icon icon-user-o text-success s-18"></i> 
            <span>Pengguna</span>
        </a>
    </li>
    <li class="no-b">
        <a href="{{ route('jenis-pendapatan.index') }}">
            <i class="icon icon-document purple-text s-18"></i> 
            <span>Jenis Pendapatan</span>
        </a>
    </li>
    <li class="no-b">
        <a href="{{ route('rincian-jenis.index') }}">
            <i class="icon icon-document blue-text s-18"></i> 
            <span>Rincian Pendapatan</span>
        </a>
    </li>
    <li class="no-b">
        <a href="{{ route('opd.index') }}">
            <i class="icon icon-document amber-text s-18"></i> 
            <span>OPD</span>
        </a>
    </li>
    @endif
    <li class="header light"><strong>MENU</strong></li>
    @if ($opd_id == 0 || $opd_id != 99999)
    <li class="no-b">
        <a href="{{ route('skrd.index') }}">
            <i class="icon icon-document text-red s-18"></i> 
            <span>SKRD</span>
        </a>
    </li>
    <li class="no-b">
        <a href="{{ route('sts.index') }}">
            <i class="icon icon-document blue-text s-18"></i> 
            <span>STS</span>
        </a>
    </li>
    <li class="no-b">
        <a href="{{ route('diskon.index') }}">
            <i class="icon icon-document text-success s-18"></i> 
            <span>Diskon</span>
        </a>
    </li>
    <li class="no-b">
        <a href="{{ route('denda.index') }}">
            <i class="icon icon-document red-text s-18"></i> 
            <span>Denda</span>
        </a>
    </li>
    @endif
    <li class="no-b">
        <a href="{{ route('report.index') }}">
            <i class="icon icon-documents black-text s-18"></i> 
            <span>Laporan</span>
        </a>
    </li>
</ul>
