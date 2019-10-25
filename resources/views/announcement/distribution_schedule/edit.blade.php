@extends('layout.base')

@section('title', 'Pengaturan Jadwal Edar Pengumuman')

@section('extra_js')
<script>
    $(document).ready(function() {
        @foreach($online_media as $medium)
        @for ($i = 1; $i <= 3; $i++)

        // Enable datetime picker
        $('#onlinepublishdatetimepicker-{{ $medium->id }}-{{ $i }}').datetimepicker({
            sideBySide: true,
            useStrict: true,
        });

        // Disable textarea of publish time if published
        if(moment($('#online-publish-datetime-{{ $medium->id }}-{{ $i }}').val(), 'MM/DD/YYYY HH:mm AA').isBefore(moment())) {
            $('#online-publish-datetime-{{ $medium->id }}-{{ $i }}').prop('disabled', true);
        }

        @endfor
        @endforeach

    });
</script>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Pengaturan Jadwal Edar Pengumuman</b></h3>
                </div>
                <div class="panel-body">
                    <div><h4><b>Isi Pengumuman: </b></h4></div>
                    <div><h5><b> {{ $announcement->organization_name}} - {{ $announcement->title }} </b></h5></div>
                    <div> {!! $announcement->content !!} </div>
                    <hr>
                    <form action="/announcement/update_distribution_schedule" role="form" method="POST" class="form-vertical">
                        {{ csrf_field() }}
                        <input type="hidden" name="announcement-id" id="announcement-id" value="{{ $announcement->id }}">
                        <div><h4><b> Daftar Distribusi Offline Yang Terhubung: </b></h4></div>
                        <ul>
                            <li>Daftar distribusi offline di bawah ini mungkin sudah tercentang secara otomatis oleh sistem.</li>
                            <li>Pengumuman ini <b>hanya</b> akan diedarkan dalam daftar distribusi offline yang tercentang.</li>
                        </ul>
                        @foreach ($present_offline_distributions as $offline_distribution)
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="offline-distribution[]" value="{{ $offline_distribution->id }}" @if (in_array($offline_distribution->id, $announcement->offline_distribution_ids)) checked @endif>
                                {{ $offline_distribution->name }}
                            </label>
                        </div>
                        @endforeach
                        <hr>
                        <div><h4><b> Jadwal Publikasi Melalui Media Online: </b></h4></div>
                        <ul>
                            <li>Untuk setiap media online, <b>sistem hanya mengizinkan maksimal 3 kali publikasi per pengumuman</b>.</li>
                            <li><b>Kolom yang berwarna abu-abu berarti pengumuman telah terpublikasi</b>. Oleh karena itu, perubahan terhadap kolom ini tidak diperbolehkan.</li>
                            <li><b>Kosongkan kolom apabila slot tersebut tidak digunakan untuk publikasi</b>.</li>
                            <li>Untuk membantu mengurangi kemungkinan masalah/error pada sistem, <b>diharap untuk hanya melakukan publikasi 15 menit setelah sekarang</b>.</li>
                        </ul>
                        @foreach($online_media as $medium)
                        <div><h5><b> {{ $medium->name }} </b></h5></div>
                        @for ($i = 1; $i <= 3; $i++)
                        <div class='input-group date' id='onlinepublishdatetimepicker-{{ $medium->id }}-{{ $i }}'>
                            <input type='text' class="form-control" name="online-publish-datetime-{{ $medium->id }}-{{ $i }}" id="online-publish-datetime-{{ $medium->id }}-{{ $i }}" value="{{{ $announcement->online_media_publish_schedules[$medium->id][$i] }}}">
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        @endfor
                        @endforeach
                        <div class="row form-group center-block">
                            <button type="submit" class="btn btn-default"> Ubah/Atur </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
