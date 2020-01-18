
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

@section('extra_js')
<script>
    $(document).ready(function() {
        $('#datetimepicker').datetimepicker({
            sideBySide: true,
            useStrict: true,
        });
    });
</script>
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
    <hr>
    <div class="row">
        <h3><b>Eksekusi Jadwal Distribusi Bulanan Secara Manual (Execute Task Manually)</b></h3>
    </div>
    <div class="row">
        <div class="col xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default">
                <form action="/monthly_offline_distribution_schedule/manual_invoke" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row form-group center-block">
                            <label> Waktu (waktu Anda seolah-olah sedang menjalankan task ini): </label>
                            <div class='input-group date' id='datetimepicker'>
                                <input type='text' class="form-control" name="datetime" id="datetime" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <button type="submit" class="btn btn-default"> Jalankan </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
