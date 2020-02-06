@extends('layout.base')

@section('title', 'Buat Pengumuman Baru')

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
                simpleUpload: {
                    uploadUrl: '{{ URL::to('/')}}/api/image_upload'
                },
                toolbar: {
					items: [
						'heading',
						'|',
						'fontFamily',
						'fontSize',
						'|',
						'bold',
						'italic',
						'underline',
						'strikethrough',
						'subscript',
						'superscript',
						'|',
						'bulletedList',
						'numberedList',
						'|',
						'alignment',
						'|',
						'indent',
						'outdent',
						'|',
						'imageUpload',
						'insertTable',
						'link',
						'mediaEmbed',
						'undo',
						'redo'
					]
				},
				language: 'en',
				image: {
					toolbar: [
						'imageTextAlternative',
						'imageStyle:full',
						'imageStyle:side'
					]
				},
				table: {
					contentToolbar: [
						'tableColumn',
						'tableRow',
						'mergeTableCells'
					]
				},
				licenseKey: '',
            }).then(editor => {
				window.editor = editor;
		    }).catch(error => {console.error(error);});

        $('#eventdatetimepicker').datetimepicker({
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
                    <strong>Form Pengumuman Baru</strong>
                </div>
                <form action="/announcement_request/insert" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row form-group center-block" >
                            <label for="organization-name"> Unit Kegiatan: </label>
                            <input type="text" name="organization-name" id="organization-name" class="form-control" value="{{ $default_organization_name }}" required>
                        </div>
                        <div class="row form-group center-block" >
                            <label for="title"> Judul Pengumuman: </label>
                            <input type="text" name="title" id="title" class="form-control" required>
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
                                    <b>Untuk mengunggah flyer kegiatan, klik Insert Image </b>(gambar pemandangan)
                                </li>
                            </ul>
                            <textarea name="content" id="content" class="form-control" rows="5"></textarea>
                        </div>
                        <hr>
                        <div class="row form-group center-block">
                            <label> Waktu Kegiatan: </label>
                            <ul>
                                <li><b>Isi dengan tanggal/waktu kegiatan.</b> Tetapi, apabila acara memiliki batas waktu (deadline) pendaftaran, isi dengan tanggal/waktu (deadline) batas pendaftaran.</li>
                                <li><b>Apabila pengumuman berisi iklan (contoh: mencari anggota baru), isi dengan salah satu tanggal dalam 3 bulan ke depan.</b> Contoh: Sekarang Januari, maka isi dengan 31 Maret.</li>
                            </ul>
                            <div class='input-group date' id='eventdatetimepicker'>
                                <input type='text' class="form-control" name="event-datetime" id="event-datetime" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label> Durasi Pengumuman: </label>
                            <ul id="duration-ul">
                                <li class="form-group"><label for="duration-35" class="radio-inline"><input type="radio" name="duration" id="duration-35" value="35" checked> Dari 1 bulan sebelum kegiatan sampai hari H </label></li>
                                <li class="form-group"><label for="duration-70" class="radio-inline"><input type="radio" name="duration" id="duration-70" value="70"> Dari 2 bulan sebelum kegiatan sampai hari H </label></li>
                                <li class="form-group"><label for="duration-ad" class="radio-inline"><input type="radio" name="duration" id="duration-ad" value="105"> Dari hari ini sampai 3 bulan kedepan, khusus iklan (contoh: cari anggota baru) </label></li>
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
                                            <label class="checkbox" for="media-{{ $medium->id }}"><input type="checkbox" id="media-{{ $medium->id }}" name="media[]" value="{{ $medium->id }}">{{ $medium-> name }}</label>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
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
