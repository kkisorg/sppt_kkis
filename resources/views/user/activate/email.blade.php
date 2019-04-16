<body>

    <p>Hi {{ $name }},</p>

    <p>Selamat datang dalam Sistem Pengelolaan Pengumuman Terpadu KKIS.</p>

    <p>
        Kami sangat senang bahwa Anda telah membuat akun sistem pengumuman
        satu atap di KKIS ini. Kami harap sistem ini dapat mempermudah publikasi
        maupun promosi kegiatan Anda untuk semua media pengumuman yang ada,
        mulai dari pengumuman Misa, buletin dan sosial media seperti Facebook.
    </p>

    <p>
        Sebagai referensi, Anda telah membuat akun menggunakan email <b>{{ $email }}</b>
        pada {{ $create_time }}.
    </p>

    <p>
        Segera aktifkan akun Anda dengan mengklik tautan
        <a href="{{ URL::to('/') }}/activate_account/{{ $token }}">ini</a>.
    </p>

    <p>
        Salam, <br>
        Humas Intern KKIS
    </p>

</body>
