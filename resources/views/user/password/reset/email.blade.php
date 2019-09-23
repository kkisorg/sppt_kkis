<body>

    <p>Hi {{ $name }},</p>
    <p>
        Kami telah menerima permintaan untuk mengatur ulang (reset) password Anda.
    </p>

    <p>
        Segera atur ulang password Anda dengan mengklik tautan
        <a href="{{ URL::to('/') }}/reset_password/{{ $token }}">ini</a>.
    </p>

    <p>
        Sebagai referensi, Anda telah membuat permintaan untuk mengatur ulang (reset) password
        akun Anda yang terdaftar dengan email <b>{{ $email }}</b>
        pada {{ $create_time }}.
    </p>

    <p>
        Jika bukan Anda yang melakukan pengubahan ini,
        mohon hubungan administrator melalui email
        <a href="mailto:kkis.contact@gmail.com?Subject=Permintaan%20Atur%20Ulang%20Password" target="_top">kkis.contact@gmail.com</a>
    </p>

    <p>
        Salam, <br>
        Humas Intern KKIS
    </p>

    <p>Ini adalah email dibuat secara otomatis oleh <a href="{{ URL::to('/') }}">Sistem Pengelolaan Pengumuman Terpadu KKIS</a>.</p>

</body>
