<style>
    @media screen and (max-width: 768px) {
        body {
            transition: background-color .5s;
        }
    }
    #sidenav {
        height: 100%;
        width: 0;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
        background-color: #111;
        overflow-x: hidden;
        transition: 0.5s;
        padding-top: 60px;
    }
    #sidenav a {
        padding: 8px 8px 8px 32px;
        text-decoration: none;
        font-size: 15px;
        color: #818181;
        display: block;
        transition: 0.3s;
    }
    #sidenav a:hover {
        color: #f1f1f1;
    }
    #sidenav .closebtn {
        position: absolute;
        top: 0;
        right: 25px;
        font-size: 36px;
        margin-left: 50px;
    }
    #main {
        transition: margin-left .5s;
        padding: 16px;
    }
</style>

<script>
    function openNav() {
        document.getElementById("sidenav").style.width = "250px";
    }

    function closeNav() {
        document.getElementById("sidenav").style.width = "0";
    }
</script>

<div id="sidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="/">Menu Utama</a>
    <hr>
    <a href="/announcement_request">Buat/Ubah/Hapus Pengumuman</a>
    <a href="/view_announcement">Lihat Seluruh Pengumuman</a>
    <a href="/view_offline_distribution">Lihat Seluruh Distribusi Offline</a>
    @if (Auth::user()->is_admin)
    <hr>
    <a href="/announcement">Setujui (Approve) Pengumuman</a>
    <a href="/offline_distribution">Buat/Ubah/Hapus Distribusi Offline</a>
    <a href="/media">Buat/Ubah/Hapus Media</a>
    <a href="/account_management">Kelola Akun User</a>
    <hr>
    <a href="/monthly_offline_distribution_schedule">Buat/Ubah/Hapus Jadwal Distribusi Offline Bulanan</a>
    <a href="/email_send_schedule">Lihat/Kelola Jadwal Pengiriman Email</a>
    <a href="/announcement_online_media_publish_schedule">Lihat/Kelola Jadwal Publikasi ke Media Online</a>
    @endif
    <hr>
    <a href="/edit_profile">Ubah Profil</a>
    <a href="/edit_password">Ubah Password</a>
    <a href="/logout">Logout</a>
</div>

<div class="container">
  <span class="btn btn-default" style="font-size:20px; cursor:pointer;" onclick="openNav()">&#9776; Menu</span>
</div>
