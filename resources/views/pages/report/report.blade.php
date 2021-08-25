<!DOCTYPE html>
<html lang="en">
<head>
    <title>Report Data {{ $title }}</title>

    <style type="text/css">
		table.d {
            border-collapse: collapse;
            width: 100%
        } 

        table.d tr.d,th.d,td.d{
            table-layout: fixed;
            border: 1px solid black;
            font-size: 12px;
        }

        .text-center{
            text-align: center
        }

        .p-l-5{
            padding-left: 5px;
        }
    </style>
</head>
<body>
    <div>
        <p class="text-center">Report Data {{ $title }}</p>
    </div>

    <table class="d">
        <thead>
            <tr class="d">
                <th class="d">No</th>
                <th class="d">NO SKRD</th>
                <th class="d">Nama Dinas</th>
                <th class="d">Jenis Retribusi</th>
                <th class="d">Tanggal SKRD</th>
                <th class="d">Masa Berlaku</th>
                @if ($jenis == 2)
                <th class="d">Tanggal Bayar</th>
                <th class="d">Status Bayar</th>
                @endif
                <th class="d">ketetapan</th>
            </tr>
        </thead>
        <tbody>
            @forelse  ($data as $index => $i)
                <tr class="d">
                    <td class="d text-center">{{ $index+1 }}</td>
                    <td class="d p-l-5">{{ $i->no_skrd }}</td>
                    <td class="d p-l-5">{{ $i->opd->n_opd }}</td>
                    <td class="d p-l-5">{{ $i->jenis_pendapatan->jenis_pendapatan }}</td>
                    <td class="d p-l-5">{{ $i->tgl_skrd_awal }}</td>
                    <td class="d p-l-5">{{ $i->tgl_skrd_akhir }}</td>
                    @if ($jenis == 2)
                    <td class="d p-l-5">{{ $i->tgl_bayar }}</td>
                    <td class="d p-l-5">{{ $i->status_bayar == 1 ? 'Sudah Bayar' : 'Belum Bayar' }}</td>
                    @endif
                    <td class="d p-l-5" >@currency($i->jumlah_bayar)</td>
                </tr>
            @empty
            <tr class="d">
                @if ($jenis == 2)
                <td class="d text-center" colspan="9">Tidak ada disini.</td>
                @else
                <td class="d text-center" colspan="7">Tidak ada disini.</td>
                @endif
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>