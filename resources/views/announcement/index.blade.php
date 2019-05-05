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
    th[name='title-col'] {
        width: 30%;
    }
    th[name='description-col'] {
        width: 45%;
    }
    img {
        max-width: 100%;
    }
    hr {
      margin-top: 20px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #eee;
      text-align: center;
      height: 0px;
      line-height: 0px;
      background-color: #fff;
    }
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <h4>Daftar Pengumuman yang Siap Diedarkan</h4>
        <table id="announcements-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="title-col"> Judul Pengumuman </th>
                    <th name="description-col" class="hidden-xs"> Isi Pengumuman </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($approved_announcements as $announcement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td class="hidden-xs" name="manage-col">{!! $announcement->content !!}</td>
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-info" href="/announcement/view/{{ $announcement->id }}"> Lihat </a>
                            <a class="list-group-item list-group-item-warning" href="/announcement/edit/{{ $announcement->announcement_request_id }}"> Ubah </a>
                            <a class="list-group-item list-group-item-danger" href="/announcement/delete/{{ $announcement->id }}" onclick="return confirm('Apakah Anda yakin menghapus pengumuman ini?\nPenghapusan ini tidak dapat dibatalkan.');"> Hapus </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <h4>Daftar Permintaan Pengumuman Baru yang Menunggu Persetujuan</h4>
        <table id="pending-approval-announcements-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="title-col"> Judul Pengumuman </th>
                    <th name="description-col" class="hidden-xs"> Isi Pengumuman </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($new_announcement_requests as $announcement_request)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $announcement_request->title }}</td>
                    <td class="hidden-xs" name="manage-col">{!! $announcement_request->content !!}</td>
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-info" href="/announcement/create/{{ $announcement_request->id }}"> Lihat, Ubah dan Setujui </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <h4>Daftar Permintaan Pengumuman yang Telah Direvisi dan Menunggu Persetujuan</h4>
        <table id="pending-approval-revision-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="title-col"> Judul Pengumuman </th>
                    <th name="description-col" class="hidden-xs"> Isi Pengumuman </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($revised_announcement_requests as $announcement)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td class="hidden-xs" name="manage-col">{!! $announcement->content !!}</td>
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-info" href="/announcement/edit/{{ $announcement->announcement_request_id }}"> Lihat, Ubah dan Setujui </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
