
@extends('layout.base')

@section('title', 'Daftar Distribusi')

@section('extra_css')
<style>
    th[name='no-col'] {
        width: 5%;
    }
    th[name='description-col'] {
        width: 20%;
    }
    th[name='distribution-datetime-col'] {
        width: 20%;
    }
    th[name='deadline-datetime-col'] {
        width: 20%;
    }
    th[name='status-col'] {
        width: 15%;
    }
    th[name='media-col'] {
        width: 10%;
    }
    th[name='manage-col'] {
        white-space: nowrap;
        width: 10%;
    }
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <h3><b>Distribusi Offline</b></h3>
    </div>
    <div class="form-group">
        <a href="/offline_distribution/create" class="btn btn-primary" role="button">Buat Distribusi Offline Baru</a>
    </div>
    <div class="row">
        <table id="offline-distribution-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="description-col"> Deskripsi </th>
                    <th name="distribution-datetime-col"> Waktu Distribusi </th>
                    <th name="deadline-datetime-col"> Batas Waktu (Deadline) </th>
                    <th name="status-col" class="hidden-xs"> Status </th>
                    <th name="media-col"> Jenis Media </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($offline_distributions as $distribution)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $distribution->name }}</td>
                    <td>{{ $distribution->distribution_datetime }} </td>
                    <td>{{ $distribution->deadline_datetime }} </td>
                    <td class="hidden-xs">
                        @if (@$distribution->status === 'FINAL')
                        <span class="label label-danger">
                        @elseif (@$distribution->status === 'MENERIMA PENGUMUMAN')
                        <span class="label label-success">
                        @else
                        <span class="label label-warning">
                        @endif
                            {{ $distribution->status }}
                        </span>
                    </td>
                    <td>{{ $distribution->media_name }}</td>
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-info" href="/offline_distribution/view/{{ $distribution->id }}"> Lihat </a>
                            <a class="list-group-item list-group-item-warning" href="/offline_distribution/edit/{{ $distribution->id }}"> Ubah </a>
                            <a class="list-group-item list-group-item-danger" href="/offline_distribution/delete/{{ $distribution->id }}" onclick="return confirm('Apakah Anda yakin menghapus distribusi ini?\nPenghapusan ini tidak dapat dibatalkan.');"> Hapus </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
