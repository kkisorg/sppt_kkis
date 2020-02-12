@extends('layout.base')

@section('title', 'Buat Distribusi Baru')

@section('extra_js')
<script>
    $(document).ready(function() {
        $('#distributiondatetimepicker').datetimepicker({
            sideBySide: true,
            useStrict: true,
        });
        $('#deadlinedatetimepicker').datetimepicker({
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
                    <h3><b>Form Distribusi Baru</b></h3>
                </div>
                <form action="/offline_distribution/insert" role="form" method="POST" class="form-vertical" onsubmit="return validate(this);">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row form-group center-block" >
                            <label for="description"> Nama: </label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                         <div class="row form-group center-block">
                            <label> Waktu Distribusi: </label>
                            <div class='input-group date' id='distributiondatetimepicker'>
                                <input type='text' class="form-control" name="distribution-datetime" id="distribution-datetime" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label> Batas Akhir (Deadline) Pengumpulan Pengumuman: </label>
                            <div class='input-group date' id='deadlinedatetimepicker'>
                                <input type='text' class="form-control" name="deadline-datetime" id="deadline-datetime" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label for="recipient-email"> Daftar Email Penerima Distribusi (dipisahkan oleh koma): </label>
                            <input type="text" name="recipient-email" id="recipient-email" data-role="tagsinput" class="form-control">
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
