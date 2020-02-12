@extends('layout.base')

@section('title', 'Ubah Distribusi')

@section('extra_js')
<script>
    $(document).ready(function() {
        $('#distributiondatetimepicker').datetimepicker({
            useCurrent: false,
            sideBySide: true,
            useStrict: true,
        });
        $('#deadlinedatetimepicker').datetimepicker({
            useCurrent: false,
            sideBySide: true,
            useStrict: true,
        });
    });

    function validate(aForm) {
        // Deadline time must be before publish time
        var deadline_datetime = new Date($('#deadline-datetime').val());
        var distribution_datetime = new Date($('#distribution-datetime').val());
        if (distribution_datetime <= deadline_datetime) {
            alert('Waktu deadline haruslah sebelum waktu distribusi.');
            $('#deadline-datetime').focus();
            return false;
        }

        // Recipient email must not be empty
        if ($('#recipient-email').val() == '') {
            alert('Daftar Penerima Email harus diisi.');
            $('#recipient-email').focus();
            return false;
        }
        return true;
    }
</script>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Ubah Distribusi</b></h3>
                </div>
                <form action="/offline_distribution/update" role="form" method="POST" class="form-vertical" onsubmit="return validate(this);">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="{{ $offline_distribution->id }}">
                    <div class="panel-body">
                        <div class="row form-group center-block" >
                            <label for="description"> Nama: </label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $offline_distribution->name }}" required>
                        </div>
                        <div class="row form-group center-block">
                            <label> Waktu Distribusi: </label>
                            <div class='input-group date' id='distributiondatetimepicker'>
                                <input type='text' class="form-control" name="distribution-datetime" id="distribution-datetime" value="{{ $offline_distribution->distribution_datetime }}" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label> Batas Akhir (Deadline) Pengumpulan Pengumuman: </label>
                            <div class='input-group date' id='deadlinedatetimepicker'>
                                <input type='text' class="form-control" name="deadline-datetime" id="deadline-datetime" value="{{ $offline_distribution->deadline_datetime }}" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label for="recipient-email"> Daftar Email Penerima Distribusi (dipisahkan oleh koma): </label>
                            <input type="text" name="recipient-email" id="recipient-email" data-role="tagsinput" class="form-control" value="{{ $offline_distribution->recipient_email }}">
                        </div>
                        <div class="row form-group center-block">
                            <label for="media-id"> Jenis Media: </label>
                            <select name="media-id" id="media-id" class="form-control">
                                @foreach ($media as $medium)
                                <option value="{{ $medium->id }}" @if ($medium->id === $offline_distribution->offline_media_id) selected @endif>{{ $medium->name }}</option>
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
