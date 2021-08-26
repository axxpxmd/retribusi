@extends('layouts.app')
@section('title', '| Dashboard  ')
@section('content')
@php
    $opd_id = Auth::user()->pengguna->opd_id;
@endphp
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-dashboard"></i> 
                        Dashboard
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-1">
            @if ($opd_id == 0 || $opd_id == 99999)
                @include('pages.dashboard.card1')
            @endif
            @if ($opd_id != 0 && $opd_id != 99999)
                @include('pages.dashboard.card2')
            @endif
            @if ($opd_id == 0 || $opd_id == 99999)
            <div class="row pl-0 col-md-12">
                <div class="col-md-8">
                    @include('pages.dashboard.chart')
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            pie chart
                        </div>
                    </div>
                </div>
            </div>
            @endif
            </div>
        </div>
    </div>
</div>
@endsection
