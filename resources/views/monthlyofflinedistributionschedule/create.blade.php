@extends('layout.base')

@section('title', 'Buat Jadwal Distribusi Bulanan Baru')

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
        <div class="col xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Form Jadwal Distribusi Bulanan Baru</b></h3>
                </div>
                <form action="/monthly_offline_distribution_schedule/insert" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row form-group center-block" >
                            <label for="description"> Nama: </label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="row form-group center-block">
                            <label for="default-header"> Default Header: </label>
                            <textarea name="default-header" id="default-header" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label for="default-footer"> Default Footer: </label>
                            <textarea name="default-footer" id="default-footer" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label> Waktu Distribusi: </label>
                            <select name="distribution-weekofmonth" id="distribution-weekofmonth" class="form-control">
                                @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">Minggu ke-{{ $i }}</option>
                                @endfor
                            </select>
                            <select name="distribution-dayofweek" id="distribution-dayofweek" class="form-control">
                                <option value="1">Hari Senin</option>
                                <option value="2">Hari Selasa</option>
                                <option value="3">Hari Rabu</option>
                                <option value="4">Hari Kamis</option>
                                <option value="5">Hari Jumat</option>
                                <option value="6">Hari Sabtu</option>
                                <option value="0">Hari Minggu</option>
                            </select>
                            <div class='input-group date' id='distributiontimepicker'>
                                <input type='text' class="form-control" name="distribution-time" id="distribution-time" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label> Batas Akhir (Deadline) Pengumpulan Pengumuman: </label>
                            <select name="deadline-dayofweek" id="deadline-dayofweek" class="form-control">
                                <option value="1">Hari Senin</option>
                                <option value="2">Hari Selasa</option>
                                <option value="3">Hari Rabu</option>
                                <option value="4">Hari Kamis</option>
                                <option value="5">Hari Jumat</option>
                                <option value="6">Hari Sabtu</option>
                                <option value="0">Hari Minggu</option>
                            </select>
                            <div class='input-group date' id='deadlinetimepicker'>
                                <input type='text' class="form-control" name="deadline-time" id="deadline-time" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label for="media-id"> Jenis Media: </label>
                            <select name="media-id" id="media-id" class="form-control">
                                @foreach ($media as $medium)
                                <option value="{{ $medium->id }}">{{ $medium->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row form-group center-block">
                            <button type="submit" class="btn btn-default"> Buat </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
