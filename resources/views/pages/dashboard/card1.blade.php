<div class="card no-b mb-3">
    <div class="card-body p-0">
        <div class="lightSlider" data-item="6" data-item-xl="4" data-item-md="2" data-item-sm="1" data-pause="5000" data-pager="false" data-auto="true" data-loop="true">
            <div class="p-5 bg-primary text-white">
                <h5 class="font-weight-normal s-14" style="margin-bottom: 20px" title="Total Retribusi Seluruh Dinas">Total Retribusi</h5>
                <span class="s-48 font-weight-lighter text-primary">{{ $totalRetribusi }}</span>
            </div>
            <?php $no = 0;?>
            @foreach ($totalRetribusiOPD as $i)
            <?php $no++ ;?>
            @if ($no % 2 == 0)
                <div class="p-5 bg-light">
                    <h5 class="font-weight-normal text-black s-14" title="{{ $i->n_opd }}" style="margin-bottom: 20px">{{ $i->initial }}</h5>
                    <span class="s-48 font-weight-lighter text-primary">{{ $i->transaksi_opd_count }}</span>
                </div>
                @else
                <div class="p-5">
                    <h5 class="font-weight-normal text-black s-14" title="{{ $i->n_opd }}" style="margin-bottom: 20px">{{ $i->initial }}</h5>
                    <span class="s-48 font-weight-lighter amber-text">{{ $i->transaksi_opd_count }}</span>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
