@extends('layout.base')

@section('title', 'Ubah Jadwal Distribusi Bulanan Baru')

@section('extra_js')
<script>
    $(document).ready(function() {
        ClassicEditor.create(document.querySelector('#default-header'), {
            simpleUpload: {uploadUrl: '{{ URL::to('/')}}/api/image_upload'}
        }).catch(error => {console.error(error);});
        ClassicEditor.create(document.querySelector('#default-footer'), {
            simpleUpload: {uploadUrl: '{{ URL::to('/')}}/api/image_upload'}
        }).catch(error => {console.error(error);});
        $('#distributiontimepicker').datetimepicker({
            format: 'LT',
            useStrict: true,
        });
        $('#deadlinetimepicker').datetimepicker({
            format: 'LT',
            useStrict: true,
        });
    });
</script>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Ubah Jadwal Distribusi Bulanan</b></h3>
                </div>
                <form action="/monthly_offline_distribution_schedule/update" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="{{ $monthly_offline_distribution_schedule->id }}">
                    <div class="panel-body">
                        <div class="row form-group center-block" >
                            <label for="description"> Nama: </label>
                            <input type="text" name="name" id="name" class="form-control" value="{{$monthly_offline_distribution_schedule->name}}" required>
                        </div>
                        <div class="row form-group center-block">
                            <label for="default-header"> Default Header: </label>
                            <textarea name="default-header" id="default-header" class="form-control" rows="5">{{$monthly_offline_distribution_schedule->default_header}}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label for="default-footer"> Default Footer: </label>
                            <textarea name="default-footer" id="default-footer" class="form-control" rows="5">{{$monthly_offline_distribution_schedule->default_footer}}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label> Waktu Distribusi: </label>
                            <select name="distribution-weekofmonth" id="distribution-weekofmonth" class="form-control">
                                @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @if ($monthly_offline_distribution_schedule->distribution_weekofmonth == $i) selected @endif>Minggu ke-{{ $i }}</option>
                                @endfor
                            </select>
                            <select name="distribution-dayofweek" id="distribution-dayofweek" class="form-control">
                                <option value="1" @if ($monthly_offline_distribution_schedule->distribution_dayofweek == 1) selected @endif>Hari Senin</option>
                                <option value="2" @if ($monthly_offline_distribution_schedule->distribution_dayofweek == 2) selected @endif>Hari Selasa</option>
                                <option value="3" @if ($monthly_offline_distribution_schedule->distribution_dayofweek == 3) selected @endif>Hari Rabu</option>
                                <option value="4" @if ($monthly_offline_distribution_schedule->distribution_dayofweek == 4) selected @endif>Hari Kamis</option>
                                <option value="5" @if ($monthly_offline_distribution_schedule->distribution_dayofweek == 5) selected @endif>Hari Jumat</option>
                                <option value="6" @if ($monthly_offline_distribution_schedule->distribution_dayofweek == 6) selected @endif>Hari Sabtu</option>
                                <option value="0" @if ($monthly_offline_distribution_schedule->distribution_dayofweek == 0) selected @endif>Hari Minggu</option>
                            </select>
                            <div class='input-group date' id='distributiontimepicker'>
                                <input type='text' class="form-control" name="distribution-time" id="distribution-time" value="{{ $monthly_offline_distribution_schedule->distribution_time }}" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label> Batas Akhir (Deadline) Pengumpulan Pengumuman: </label>
                            <select name="deadline-dayofweek" id="deadline-dayofweek" class="form-control">
                                <option value="1" @if ($monthly_offline_distribution_schedule->deadline_dayofweek == 1) selected @endif>Hari Senin</option>
                                <option value="2" @if ($monthly_offline_distribution_schedule->deadline_dayofweek == 2) selected @endif>Hari Selasa</option>
                                <option value="3" @if ($monthly_offline_distribution_schedule->deadline_dayofweek == 3) selected @endif>Hari Rabu</option>
                                <option value="4" @if ($monthly_offline_distribution_schedule->deadline_dayofweek == 4) selected @endif>Hari Kamis</option>
                                <option value="5" @if ($monthly_offline_distribution_schedule->deadline_dayofweek == 5) selected @endif>Hari Jumat</option>
                                <option value="6" @if ($monthly_offline_distribution_schedule->deadline_dayofweek == 6) selected @endif>Hari Sabtu</option>
                                <option value="0" @if ($monthly_offline_distribution_schedule->deadline_dayofweek == 0) selected @endif>Hari Minggu</option>
                            </select>
                            <div class='input-group date' id='deadlinetimepicker'>
                                <input type='text' class="form-control" name="deadline-time" id="deadline-time" value="{{ $monthly_offline_distribution_schedule->deadline_time }}" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label for="recipient-email"> Daftar Email Penerima Distribusi (dipisahkan oleh koma): </label>
                            <input type="text" name="recipient-email" id="recipient-email" data-role="tagsinput" class="form-control" value="{{$monthly_offline_distribution_schedule->recipient_email}}" required>
                        </div>
                        <div class="row form-group center-block">
                            <label for="media-id"> Jenis Media: </label>
                            <select name="media-id" id="media-id" class="form-control">
                                @foreach ($media as $medium)
                                <option value="{{ $medium->id }}" @if ($medium->id === $monthly_offline_distribution_schedule->offline_media_id) selected @endif>{{ $medium->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row form-group center-block">
                            <button type="submit" class="btn btn-default"> Ubah </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
