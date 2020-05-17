@extends('layout.base')

@section('title', 'Ubah Pengumuman Dalam Distribusi')

@section('extra_js')
<script>
    $(document).ready(function() {
        ClassicEditor.create(document.querySelector('#header'), {
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
            window.header_editor = editor;
        }).catch(error => {console.error(error);});
        ClassicEditor.create(document.querySelector('#content'), {
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
        ClassicEditor.create(document.querySelector('#footer'), {
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
            window.footer_editor = editor;
        }).catch(error => {console.error(error);});

        @foreach($offline_distribution->announcement as $announcement)
        $('#copy-{{ $announcement->id }}').click(function() {
            var original_content = window['content_editor'].getData();
            // Append with title and then content.
            var appended_title = $("#announcement_title_{{ $announcement->id }}").html();
            var appended_content = $("#announcement_content_{{ $announcement->id }}").html();
            var updated_content = original_content + '<br />' + appended_title + '<br />' + appended_content;
            window['content_editor'].setData(updated_content);
            return false;
        });
        @endforeach
    });

    function validate(aForm) {
        // Header must be filled
        aData = header_editor.getData();
        if (aData == '') {
            alert('Header harus diisi.');
            header_editor.editing.view.focus();
            return false;
        }

        // Content must be filled
        aData = content_editor.getData();
        if (aData == '') {
            alert('Isi harus diisi.');
            content_editor.editing.view.focus();
            return false;
        }

        // Footer must be filled
        aData = footer_editor.getData();
        if (aData == '') {
            alert('Footer harus diisi.');
            footer_editor.editing.view.focus();
            return false;
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
                    <h3><b>Ubah Pengumuman Dalam Distribusi</b></h3>
                </div>
                <form action="/offline_distribution/update_content" role="form" method="POST" class="form-vertical" onsubmit="return validate(this);">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="{{ $offline_distribution->id }}">
                    <div class="panel-body">
                        <div><h4><b>{{ $offline_distribution->name }}</b></h4></div>
                        <div><h5><b>Waktu Distribusi: </b>{{ $offline_distribution->distribution_datetime }}</h5></div>
                        <div><h5><b>Batas Waktu (Deadline): </b>{{ $offline_distribution->deadline_datetime }}</h5></div>
                        <div><h5><b>Media: </b>{{ $offline_distribution->media_name }}</h5></div>
                        <hr>
                        <div><h4><b>Daftar Pengumuman (Untuk Referensi): </b></h4></div>
                        <ol>
                            @foreach ($offline_distribution->announcement as $announcement)
                            <li>
                                <div>
                                    <h5 id="announcement_title_{{ $announcement->id }}"><b>{{ $announcement->title }}</b></h5>
                                </div>
                                <div id="announcement_content_{{ $announcement->id }}">{!! $announcement->pivot->content !!}</div>
                                <a id="copy-{{ $announcement->id }}" class="btn btn-default btn-xs" role="button">Salin Pengumuman di atas ke Bagian Isi Distribusi Offline</a></label>

                            </li>
                            @endforeach
                        </ol>
                        <hr>
                        <div class="row form-group center-block">
                            <label for="header"> Header: </label>
                            <textarea name="header" id="header" class="form-control" rows="5">{{ $offline_distribution->header }}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label for="footer"> Isi: </label>
                            <textarea name="content" id="content" class="form-control" rows="5">{{ $offline_distribution->content }}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label for="footer"> Footer: </label>
                            <textarea name="footer" id="footer" class="form-control" rows="5">{{ $offline_distribution->footer }}</textarea>
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
