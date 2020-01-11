<body>

    <div> {!! $content !!} </div>

    @if ($mention_app_name_in_body)
    <p>
        Ini adalah email dibuat secara otomatis oleh
        <a href="{{ URL::to('/') }}">Sistem Pengelolaan Pengumuman Terpadu KKIS</a>.
    </p>
    @endif

</body>
