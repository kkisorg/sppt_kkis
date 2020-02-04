
@extends('layout.base')

@section('title', 'Daftar Jadwal Pengiriman Email')

@section('extra_css')
<style>
    th[name='no-col'] {
        width: 5%;
    }
    th[name='email-class-col'] {
        width: 15%;
    }
    th[name='request-parameter-col'] {
        width: 45%;
    }
    th[name='send-datetime-col'] {
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
        <h3><b> Daftar Jadwal Pengiriman Email</b></h3>
    </div>
    <div class="row">
        <table id="email-send-schedule-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="email-class-col"> Email Class </th>
                    <th name="request-parameter-col"> Request Parameter </th>
                    <th name="send-datetime-col"> Waktu Kirim </th>
                    <th name="status-col"> Status </th>
                    <th name="manage-col"> Kelola </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($email_send_schedules as $schedule)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $schedule->email_class }}</td>
                    <td><pre> {{ $schedule->request_parameter }}</pre></td>
                    <td> {{ $schedule->send_datetime }} </td>
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
                            <a class="list-group-item list-group-item-info" href="/email_send_schedule/view/{{ $schedule->id }}"> Lihat </a>
                            <a class="list-group-item list-group-item-success" href="/email_send_schedule/manual_invoke/{{ $schedule->id }}" onclick="return confirm('Apakah Anda yakin mengirim email ini secara manual?');"> Kirim secara manual </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
