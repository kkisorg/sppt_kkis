
@extends('layout.base')

@section('title', 'Daftar Jadwal Publikasi Media Online')

@section('extra_css')
<style>
    th[name='no-col'] {
        width: 5%;
    }
    th[name='media-col'] {
        width: 15%;
    }
    th[name='title-content-col'] {
        width: 45%;
    }
    th[name='publish-datetime-col'] {
        width: 15%;
    }
    th[name='status-col'] {
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
        <h3><b>Jadwal Pengiriman Publikasi Media Online</b></h3>
    </div>
    <div class="row">
        <table id="announcement-online-media-publish-schedule-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="media-col"> Nama Media </th>
                    <th name="title-content-col"> Isi </th>
                    <th name="publish-datetime-col"> Waktu Publikasi </th>
                    <th name="status-col"> Status </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($announcement_online_media_publish_schedules as $schedule)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $schedule->media->name }}</td>
                    <td>
                        <h3><b> {{ $schedule->title }} </b></h3>
                        {!! $schedule->content !!}
                    </td>
                    <td> {{ $schedule->publish_datetime }} </td>
                    <td> @if (@$schedule->status === 'FAILED')
                    <span class="label label-danger">
                    @elseif (@$schedule->status === 'SUCCESS')
                    <span class="label label-success">
                    @else
                    <span class="label label-warning">
                    @endif
                        {{ $schedule->status }}
                    </span> </td>
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-info" href="/announcement_online_media_publish_schedule/view/{{ $schedule->id }}"> Lihat </a>
                            <a class="list-group-item list-group-item-success" href="/announcement_online_media_publish_schedule/manual_invoke/{{ $schedule->id }}" onclick="return confirm('Apakah Anda yakin mempublikasikan pengumuman ini secara manual?');"> Publikasikan secara manual </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
