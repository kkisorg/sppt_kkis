@extends('layout.base')

@section('title', 'Lihat Distribusi Offline')

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Lihat Distribusi Offline</b></h3>
                </div>
                <div class="panel-body">
                    <div class="panel-group" id="offline-distribution-list" role="tablist" aria-multiselectable="true">
                        @foreach($present_offline_distributions as $distribution)
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading{{ $loop->iteration }}">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#offline-distribution-list" href="#collapse{{ $loop->iteration }}" aria-expanded="false" aria-controls="collapse{{ $loop->iteration }}">
                                        {{ $distribution->name }} (Klik Disini Untuk Melihat)
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse{{ $loop->iteration }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $loop->iteration }}">
                                <div class="panel-body">
                                    <div> {!! $distribution->header !!} </div>
                                    <div> {!! $distribution->content !!} </div>
                                    <div> {!! $distribution->footer !!} </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
