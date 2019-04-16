{{-- For message provided by HTTPResponse --}}
@if(isset($success_message))
    <div class="alert alert-success alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success! </strong> {!! $success_message !!}
    </div>
@endif
@if(isset($warning_message))
    <div class="alert alert-warning alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Warning! </strong> {!! $warning_message !!}
    </div>
@endif
@if(isset($error_message))
    <div class="alert alert-danger alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error! </strong> {!! $error_message !!}
    </div>
@endif

{{-- For message provided by HTTPRedirect --}}
@if(session('success_message') !== null)
    <div class="alert alert-success alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success! </strong> {!! session('success_message') !!}
    </div>
@endif
@if(session('warning_message') !== null)
    <div class="alert alert-warning alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Warning! </strong> {!! session('warning_message') !!}
    </div>
@endif
@if(session('error_message') !== null)
    <div class="alert alert-danger alert-dismissable">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Error! </strong> {!! session('error_message') !!}
    </div>
@endif
