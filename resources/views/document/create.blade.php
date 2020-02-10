@extends('layout.base')

@section('title', 'Buat Dokumen Baru')

@section('extra_css')
<style>
#type-ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
</style>
@endsection

@section('extra_js')
<script>
    $(document).ready(function() {
        $('#publishdatetimepicker').datetimepicker({
            format: 'MM/DD/YYYY',
            useStrict: true,
        });
    });

    var _validFileExtensions = ["pdf"];
    function Validate(oForm) {
        var arrInputs = oForm.getElementsByTagName("input");
        for (var i = 0; i < arrInputs.length; i++) {
            var oInput = arrInputs[i];
            if (oInput.type == "file") {
                var sFileName = oInput.value;
                if (sFileName.length > 0) {
                    var blnValid = false;
                    for (var j = 0; j < _validFileExtensions.length; j++) {
                        var sCurExtension = _validFileExtensions[j];
                        if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                            blnValid = true;
                            break;
                        }
                    }

                    if (!blnValid) {
                        if ((sFileName.indexOf("\\") != -1) || (sFileName.indexOf("\/") != -1)) {
                            if (sFileName.lastIndexOf("\\") > sFileName.lastIndexOf("\/") != -1) {
                                sFileName = sFileName.substr(sFileName.lastIndexOf("\\") + 1);
                            } else {
                                sFileName = sFileName.substr(sFileName.lastIndexOf("\/") + 1);
                            }
                        }
                        alert("ERROR: " + sFileName + " is invalid. Only " + _validFileExtensions.join(", ") + " are supported.");
                        return false;
                    }
                }
            }
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
                    <h3><b>Buat Dokumen Baru</b></h3>
                </div>
                <form action="/document/insert" role="form" method="POST" class="form-vertical" enctype="multipart/form-data" onsubmit="return Validate(this);">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row form-group center-block">
                            <label> Tipe Dokumen: </label>
                            <ul id="type-ul">
                                <li class="form-group"><label for="document-type-bulletin" class="radio-inline"><input type="radio" name="document-type" id="document-type-bulletin" value="bulletin" checked> Buletin </label></li>
                            </ul>
                        </div>
                        <div class="row form-group center-block">
                            <label for="name"> Nama Dokumen: </label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="row form-group center-block">
                            <label> Waktu Publikasi Dokumen: </label>
                            <div class='input-group date' id='publishdatetimepicker'>
                                <input type='text' class="form-control" name="publish-datetime" id="publish-datetime" required>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row form-group center-block">
                            <label for="document"> Dokumen (PDF): </label>
                            <input type="file" name="document" required>
                        </div>
                        <div class="row form-group center-block">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default"> Buat </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
