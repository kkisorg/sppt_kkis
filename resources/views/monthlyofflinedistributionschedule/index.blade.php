
@extends('layout.base')

@section('title', 'Daftar Jadwal Distribusi Bulanan')

@section('extra_css')
<style>
    th[name='no-col'] {
        width: 5%;
    }
    th[name='description-col'] {
        width: 30%;
    }
    th[name='datetime-col'] {
        width: 25%;
    }
    th[name='deadline-col'] {
        width: 25%;
    }
    th[name='media-col'] {
        width: 15%;
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
        <h3><b>Jadwal Distribusi Bulanan</b></h3>
    </div>
    <div class="form-group">
        <a href="/monthly_offline_distribution_schedule/create" class="btn btn-primary" role="button">Buat Jadwal Distribusi Bulanan Baru</a>
    </div>
    <div class="row">
        <table id="monthly-offline-distribution-schedule-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="escription-col"> Deskripsi </th>
                    <th name="datetime-col"> Waktu Distribusi </th>
                    <th name="deadline-col"> Batas Waktu (Deadline) </th>
                    <th name="media-col"> Jenis Media </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($monthly_offline_distribution_schedules as $schedule)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $schedule->name }}</td>
                    <td>Minggu ke-{{ $schedule->distribution_weekofmonth }}, hari {{ $schedule->distribution_dayofweek }}, pukul {{ $schedule->distribution_time }} </td>
                    <td>Hari {{ $schedule->deadline_dayofweek }}, pukul {{ $schedule->deadline_time }} </td>
                    <td>{{ $schedule->media_name }}</td>
                    <td>
                        <div class="list-group">
                            <a class="list-group-item list-group-item-warning" href="/monthly_offline_distribution_schedule/edit/{{ $schedule->id }}"> Ubah </a>
                            <a class="list-group-item list-group-item-danger" href="/monthly_offline_distribution_schedule/delete/{{ $schedule->id }}" onclick="return confirm('Apakah Anda yakin menghapus jadwal distribusi bulanan ini?\nPenghapusan ini tidak dapat dibatalkan.');"> Hapus </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
