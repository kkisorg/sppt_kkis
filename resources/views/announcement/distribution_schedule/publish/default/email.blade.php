<body>

    <p>Hi Admin,</p>

    <p>
        Publikasi ke media online <b>{{ $media_name }}</b> belum dapat dilakukan secara otomatis.
        Oleh karena itu, dimohon melakukan publikasi pengumuman berikut secara manual.
        Dimohon untuk juga mencantumkan gambar pada attachment (jika ada).
    </p>

    <div><b> {{ $title }} </b></div>

    <div> {!! $content !!} </div>

    <p>
        Salam, <br>
        Humas Intern KKIS
    </p>

    <p>
        Ini adalah email dibuat secara otomatis oleh
        <a href="{{ URL::to('/') }}">Sistem Pengelolaan Pengumuman Terpadu KKIS</a>.
    </p>

</body>
