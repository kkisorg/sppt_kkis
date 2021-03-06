@extends('layout.base')

@section('title', 'Ubah Pengumuman')

@section('extra_css')
<style>
    #duration-ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    a {
        margin-left: 10px;
        margin-right: 10px;
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
				window.content_editor = editor;
		    }).catch(error => {console.error(error);});
        @foreach($media as $medium)
        ClassicEditor.create(
            document.querySelector('#content-{{ $medium->id }}'), {
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
				window.editor_{{ $medium->id }} = editor;
		    }).catch(error => {console.error(error);});

            $('#copy-{{ $medium->id }}').click(function() {
                window['editor_{{ $medium->id }}'].setData(window['content_editor'].getData());
                return false;
            });
        @endforeach
        $('#eventdatetimepicker').datetimepicker({
            sideBySide: true,
            useStrict: true,
        });


    });

    function validate(aForm) {
        // Content must be filled
        aData = content_editor.getData();
        if (aData == '') {
            alert('Isi Pengumuman harus diisi.');
            content_editor.editing.view.focus();
            return false;
        }

        // At least one media must be ticked
        if ($(':checkbox:checked').length == 0) {
            alert('Minimum satu media harus dipilih.');
            return false;
        };

        // Content of the selected media must be filled
        var aSelected = $(':checkbox:checked');
        for (var i = 0; i < aSelected.length; i++) {
            var editor = window['editor_' + aSelected[i].value];
            aData = editor.getData();
            if (aData == '') {
                alert('Isi Pengumuman harus diisi.');
                editor.editing.view.focus();
                return false;
            }
        }

        return true;
    }
</script>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Permintaan Revisi Pengumuman (Untuk Referensi)</strong>
                </div>
                <div class="panel-body">
                    <div><b> {{ $announcement_request->organization_name }} mengajukan permintaan pengumuman sebagai berikut. </b></div>
                    <hr>
                    <div><b> {{ $announcement_request->title }} </b></div>
                    <div> {!! $announcement_request->content !!} </div>
                    <div class="form-group"></div>
                    <div><b>Waktu:</b> {{ $announcement_request->event_datetime_human_readable }} </div>
                    <div><b>Durasi Pengumuman:</b> Dari {{ $announcement_request->duration }} sebelum kegiatan hingga hari H </div>
                    <div><b> Media Distribusi: </b> {{ $announcement_request->media }}</div>
                    <hr>
                    <div><b> Dengan referensi di atas, dimohon untuk menyetujui permintaan pengumuman ini dengan mengisi form di bawah ini.</b></div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Ubah Pengumuman</strong>
                </div>
                <form action="/announcement/update" role="form" method="POST" class="form-vertical"  onsubmit="return validate(this);">
                    {{ csrf_field() }}
                    <input type="hidden" name="announcement-id" id="announcement-id" value="{{ $announcement->id }}">
                    <input type="hidden" name="revision-no" id="revision-no" value="{{ $announcement_request->revision_no }}">
                    <div class="panel-body">
                        <div class="row form-group center-block" >
                            <label for="organization-name"> Unit Kegiatan: </label>
                            <input type="text" name="organization-name" id="organization-name" class="form-control" value="{{ $announcement->organization_name }}" required>
                        </div>
                        <div class="row form-group center-block" >
                            <label for="title"> Judul Pengumuman: </label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ $announcement->title }}" required>
                        </div>
                        <div class="row form-group center-block">
                            <label> Isi Pengumuman: </label>
                            <ul>
                                <li>
                                    <b>Isi Pengumuman beserta flyer dimasukkan di kolom dibawah ini.</b>
                                </li>
                                <li>
                                    <b>Jangan lupa untuk memasukkan informasi secara detil.</b>
                                    Informasi yang biasa diperlukan: nama (atau tema) kegiatan, deskripsi kegiatan,
                                    tempat/waktu kegiatan, pembicara, biaya pendaftaran, link pendaftaran dan contact person.
                                </li>
                                <li>
                                    <b>Untuk mengunggah flyer kegiatan, klik Insert Image </b>(gambar pemandangan)
                                </li>
                            </ul>
                            <textarea name="content" id="content" class="form-control" rows="5">{{ $announcement->content }}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label> Isi Pengumuman Tiap Media: </label>
                            <ul>
                                <li>
                                    <b>Untuk setiap media yang akan digunakan, media harus dicentang dan kolom deskripsi harus diisi dengan isi pengumuman beserta flyer.</b>
                                </li>
                                <li>
                                    <b>Isi pengumuman harus sesuai dengan gaya/style media tersebut.</b>
                                </li>
                            </ul>
                        </div>
                        @foreach($media as $medium)
                        <div class="row form-group center-block">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <label class="checkbox" for="content-{{ $medium->id }}"><input type="checkbox" id="media-{{ $medium->id }}" name="media[]" value="{{ $medium->id }}" @if (in_array($medium->id, $announcement->media()->pluck('id')->toArray())) checked @endif>{{ $medium-> name }}<a id="copy-{{ $medium->id }}" class="btn btn-default btn-xs" role="button">Salin (Copy) dari Deskripsi</a></label>
                                <textarea name="content-{{ $medium->id }}" id="content-{{ $medium->id }}" class="form-control" rows="5">@if (array_key_exists($medium->id, $announcement->media_content)) {{ $announcement->media_content[$medium->id] }} @endif</textarea>
                            </div>
                        </div>
                        @endforeach
                        <hr>
                        <div class="row form-group center-block">
                            <label> Waktu Kegiatan: </label>
                            <ul>
                                <li><b>Isi dengan tanggal/waktu kegiatan.</b> Tetapi, apabila acara memiliki batas waktu (deadline) pendaftaran, isi dengan tanggal/waktu (deadline) batas pendaftaran.</li>
                                <li><b>Apabila pengumuman berisi iklan (contoh: mencari anggota baru), isi dengan salah satu tanggal dalam 3 bulan ke depan.</b> Contoh: Sekarang Januari, maka isi dengan 31 Maret.</li>
                            </ul>
                            <div class='input-group date' id='eventdatetimepicker'>
                                <input type='text' class="form-control" name="event-datetime" id="event-datetime" value="{{ $announcement->event_datetime }}" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label> Durasi Pengumuman: </label>
                            <ul id="duration-ul">
                                <li class="form-group"><label for="duration-35" class="radio-inline"><input type="radio" name="duration" id="duration-35" value="35" @if ($announcement->duration == 35) checked @endif> Dari 1 bulan sebelum kegiatan sampai hari H </label></li>
                                <li class="form-group"><label for="duration-70" class="radio-inline"><input type="radio" name="duration" id="duration-70" value="70" @if ($announcement->duration == 70) checked @endif> Dari 2 bulan sebelum kegiatan sampai hari H </label></li>
                                <li class="form-group"><label for="duration-ad" class="radio-inline"><input type="radio" name="duration" id="duration-ad" value="105" @if ($announcement->duration == 105) checked @endif> Dari hari ini sampai 3 bulan kedepan, khusus iklan (contoh: cari anggota baru) </label></li>
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
