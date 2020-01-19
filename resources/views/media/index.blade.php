@extends('layout.base')

@section('title', 'Daftar Media')

@section('extra_css')
<style>
    th[name='manage-col'] {
        white-space: nowrap;
        width: 15%;
    }
    th[name='no-col'] {
        width: 10%;
    }
    th[name='title-col'] {
        width: 25%;
    }
    th[name='is-online-col'] {
        width: 10%;
    }
    th[name='text-col'] {
        width: 15%;
    }
    th[name='image-col'] {
        width: 15%;
    }
    th[name='is-active-col'] {
        width: 10%;
    }
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="form-group">
                <a href="/media/create" class="btn btn-primary" role="button">Buat Media Baru</a>
            </div>
            <table id="media-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th name="no-col"> No.</th>
                        <th name="title-col"> Nama </th>
                        <th name="title-col" class="hidden-xs"> Jenis </th>
                        <th name="text-col" class="hidden-xs"> Teks </th>
                        <th name="image-col" class="hidden-xs"> Gambar/Flyer </th>
                        <th name="is-active-col"> Aktif </th>
                        <th name="manage-col"> Kelola </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($media as $medium)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $medium->name }}</td>
                        <td class="hidden-xs">
                            @if ($medium->is_online) ONLINE
                            @else OFFLINE @endif
                        </td>
                        <td class="hidden-xs">
                            @if ($medium->text === 'REQUIRED') WAJIB
                            @elseif ($medium->text === 'OPTIONAL') OPSIONAL
                            @else TIDAK ADA @endif
                        </td>
                        <td class="hidden-xs">
                            @if ($medium->image === 'REQUIRED') WAJIB
                            @elseif ($medium->image === 'OPTIONAL') OPSIONAL
                            @else TIDAK ADA @endif
                        </td>
                        <td>@if ($medium->is_active) &#x2714; @else &#x2718; @endif</td>
                        <td>
                            <div class="list-group">
                                <a class="list-group-item list-group-item-warning" href="/media/edit/{{ $medium->id }}"> Ubah </a>
                                <a class="list-group-item list-group-item-danger" href="/media/delete/{{ $medium->id }}" onclick="return confirm('Apakah Anda yakin menghapus media ini?\nPenghapusan ini tidak dapat dibatalkan.');"> Hapus </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
