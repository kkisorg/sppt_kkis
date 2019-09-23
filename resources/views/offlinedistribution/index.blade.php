
@extends('layout.base')

@section('title', 'Daftar Distribusi')

@section('extra_css')
<style>
    th[name='no-col'] {
        width: 5%;
    }
    th[name='description-col'] {
        width: 15%;
    }
    th[name='datetime-col'] {
        width: 20%;
    }
    th[name='content-col'] {
        width: 40%;
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
                    <th name="datetime-col"> Waktu </th>
                    <th name="content-col"> Isi Pengumuman </th>
                    <th name="media-col"> Jenis Media </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($offline_distributions as $distribution)
                <tr>
                    <td rowspan="3">{{ $loop->iteration }}</td>
                    <td rowspan="3">{{ $distribution->name }}</td>
                    <td><b>Waktu Distribusi: </b><br>{{ $distribution->distribution_datetime }}</td>
                    <td rowspan="2">{{ $distribution->announcement_titles }}</td>
                    <td rowspan="3">{{ $distribution->media_name }}</td>
                    <td rowspan="3">
                        <div class="list-group">
                            <a class="list-group-item list-group-item-info" href="/offline_distribution/view/{{ $distribution->id }}"> Lihat </a>
                            <a class="list-group-item list-group-item-warning" href="/offline_distribution/edit/{{ $distribution->id }}"> Ubah </a>
                            <a class="list-group-item list-group-item-danger" href="/offline_distribution/delete/{{ $distribution->id }}" onclick="return confirm('Apakah Anda yakin menghapus distribusi ini?\nPenghapusan ini tidak dapat dibatalkan.');"> Hapus </a>
                            @if (@$distribution->status === 'FINAL')
                            <a class="list-group-item list-group-item-success" href="/offline_distribution/share/{{ $distribution->id }}" onclick="return confirm('Apakah Anda yakin mengirim isi pengumuman dalam distribusi ini melalui email?');"> Bagikan melalui email </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><b>Batas Waktu (Deadline): </b><br>{{ $distribution->deadline_datetime }}</td>
                </tr>
                <tr>
                    <td class="hidden-xs">
                        <b>Status: </b><br>
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
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-warning" href="/offline_distribution/edit_content/{{ $distribution->id }}"> Ubah Pengumuman Dalam Distribusi </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
