@extends('layout.base', ['hide_menu' => true])

@section('title', 'Menu Utama')

@section('extra_css')
<style>
    div.hr {
      margin-top: 20px;
      margin-bottom: 20px;
      border: 0;
      border-top: 1px solid #eee;
      text-align: center;
      height: 0px;
      line-height: 0px;
    }
    .hr-title {
      background-color: #fff;
    }
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Menu User</b></h3>
                </div>
                <div class="panel-body">
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/announcement_request">
                                Buat/Ubah/Hapus Pengumuman
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/view_announcement">
                                Lihat Seluruh Pengumuman
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/view_offline_distribution">
                                Lihat Seluruh Distribusi Offline <br> ({{$offline_media_name}})
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class='hr'>
                                <span class='hr-title'> Login sebagai: <b>{{ $user->name }}</b> </span>
                            </div>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/edit_profile">
                                Ubah Profil
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/edit_password">
                                Ubah Password
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/logout">
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($user->is_admin)
        <div class="col xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Menu Admin</b></h3>
                </div>
                <div class="panel-body">
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/announcement">
                                Setujui (Approve) Pengumuman
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/offline_distribution">
                                Buat/Ubah/Hapus Distribusi Offline
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/media">
                                Buat/Ubah/Hapus Media
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col xs-12 col-sm-4 col-md-4 col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Menu Super Admin</b></h3>
                </div>
                <div class="panel-body">
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/monthly_offline_distribution_schedule">
                                Buat/Ubah/Hapus Jadwal Distribusi Offline Bulanan
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/email_send_schedule">
                                Lihat/Kelola Jadwal Pengiriman Email
                            </a>
                        </div>
                    </div>
                    <div class="row form-group center-block">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <a class="btn btn-default btn-block" href="/announcement_online_media_publish_schedule">
                                Lihat/Kelola Jadwal Publikasi ke Media Online
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
