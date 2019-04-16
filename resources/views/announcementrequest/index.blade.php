@extends('layout.base')

@section('title', 'Daftar Pengumuman')

@section('extra_css')
<style>
    th[name='manage-col'] {
        width: 10%;
    }
    th[name='no-col'] {
        width: 5%;
    }
    th[name='organization-name-col'] {
        width: 10%;
    }
    th[name='title-col'] {
        width: 20%;
    }
    th[name='description-col'] {
        width: 55%;
    }
    img {
        max-width: 100%;
    }
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <a href="/announcement_request/create/" class="btn btn-primary" role="button">Buat Pengumuman Baru</a>
        <table id="announcements-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="organization-name-col"> Unit Kegiatan </th>
                    <th name="title-col"> Judul Pengumuman </th>
                    <th name="description-col" class="hidden-xs"> Isi Pengumuman </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($present_announcements as $announcement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $announcement->organization_name }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td class="hidden-xs" name="manage-col">{!! $announcement->content !!}</td>
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-info" href="/announcement_request/view/{{ $announcement->id }}"> Lihat </a>
                            <a class="list-group-item list-group-item-warning" href="/announcement_request/edit/{{ $announcement->id }}"> Ubah </a>
                            <a class="list-group-item list-group-item-danger" href="/announcement_request/delete/{{ $announcement->id }}" onclick="return confirm('Apakah Anda yakin menghapus pengumuman ini?\nPenghapusan ini tidak dapat dibatalkan.');"> Hapus </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
