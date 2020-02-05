@extends('layout.base')

@section('title', 'Ubah Pengumuman')

@section('extra_css')
<style>
#duration-ul, #media-ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
</style>
@endsection

@section('extra_js')
<script>
    $(document).ready(function() {
        ClassicEditor.create(
            document.querySelector('#content'), {
                simpleUpload: {uploadUrl: '{{ URL::to('/')}}/api/image_upload'}
            }).catch(error => {console.error(error);});

        $('#eventdatetimepicker').datetimepicker({
            useCurrent: false,
            sideBySide: true,
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
                    <strong>Ubah Pengumuman</strong>
                </div>
                <form action="/announcement_request/update" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <input type="hidden" name="id" id="id" value="{{ $announcement_request->id }}">
                        <div class="row form-group center-block" >
                            <label for="organization-name"> Unit Kegiatan: </label>
                            <input type="text" name="organization-name" id="organization-name" class="form-control" value="{{ $announcement_request->organization_name }}" required>
                        </div>
                        <div class="row form-group center-block" >
                            <label for="title"> Judul Pengumuman: </label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ $announcement_request->title }}" required>
                        </div>
                        <div class="row form-group center-block">
                            <label> Isi Pengumuman: </label>
                            <ul>
                                <li><b>Isi Pengumuman beserta flyer dimasukkan di kolom dibawah ini.</b></li>
                                <li>
                                    <b>Jangan lupa untuk memasukkan informasi secara detil.</b>
                                    Informasi yang biasa diperlukan: nama (atau tema) kegiatan, deskripsi kegiatan,
                                    tempat/waktu kegiatan, pembicara, biaya pendaftaran, link pendaftaran dan contact person.
                                </li>
                                <li>
                                    <b>Untuk mengunggah flyer kegiatan, klik Insert Image </b>(gambar pemandangan, icon ke-6 dari kiri)
                                </li>
                            </ul>
                            <textarea name="content" id="content" class="form-control" rows="5">{{ $announcement_request->content }}</textarea>
                        </div>
                        <hr>
                        <div class="row form-group center-block">
                            <label> Waktu Kegiatan: </label>
                            <ul>
                                <li><b>Isi dengan tanggal/waktu kegiatan.</b> Tetapi, apabila acara memiliki batas waktu (deadline) pendaftaran, isi dengan tanggal/waktu (deadline) batas pendaftaran.</li>
                                <li><b>Apabila pengumuman berisi iklan (contoh: mencari anggota baru), isi dengan salah satu tanggal dalam 3 bulan ke depan.</b> Contoh: Sekarang Januari, maka isi dengan 31 Maret.</li>
                            </ul>
                            <div class='input-group date' id='eventdatetimepicker'>
                                <input type='text' class="form-control" name="event-datetime" id="event-datetime" value="{{ $announcement_request->event_datetime }}" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label> Durasi Pengumuman: </label>
                            <ul id="duration-ul">
                                <li class="form-group"><label for="duration-35" class="radio-inline"><input type="radio" name="duration" id="duration-35" value="35" @if ($announcement_request->duration == 35) checked @endif> Dari 1 bulan sebelum kegiatan sampai hari H </label></li>
                                <li class="form-group"><label for="duration-70" class="radio-inline"><input type="radio" name="duration" id="duration-70" value="70" @if ($announcement_request->duration == 70) checked @endif> Dari 2 bulan sebelum kegiatan sampai hari H </label></li>
                                <li class="form-group"><label for="duration-ad" class="radio-inline"><input type="radio" name="duration" id="duration-ad" value="105" @if ($announcement_request->duration == 105) checked @endif> Dari hari ini sampai 3 bulan kedepan, khusus iklan (contoh: cari anggota baru) </label></li>
                            </ul>
                        </div>
                        <hr>
                        <div class="row form-group center-block">
                            <label> Media Distribusi Pengumuman: </label>
                            <ul>
                                <li><b>Gunakan media yang sesuai dengan jenis pengumuman/kegiatan Anda.</b></li>
                                <li><b>Gunakan media secukupnya</b> (jangan pilih semua media jika memang kurang sesuai).</li>
                            </ul>
                            <ul id="media-ul">
                                @foreach($media as $medium)
                                <li class="checkbox">
                                    <div class="form-group">
                                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                            <label class="checkbox" for="media-{{ $medium->id }}"><input type="checkbox" id="media-{{ $medium->id }}" name="media[]" value="{{ $medium->id }}" @if (in_array($medium->id, $announcement_request->media()->pluck('id')->toArray())) checked @endif>{{ $medium-> name }}</label>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
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
