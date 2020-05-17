<body>

    <p>Hi Admin,</p>

    <p>Terdapat beberapa permintaan pengumuman baru.</p>

    @if (count($new_announcement_request_titles) > 0)
    <p>
        <b>Pengumuman Baru:</b>
        <ul>
            @foreach ($new_announcement_request_titles as $title)
            <li>{{ $title }}</li>
            @endforeach
        </ul>
    </p>
    @endif

    @if (count($revised_announcement_request_titles) > 0)
    <p>
        <b>Pengumuman Baru:</b>
        <ul>
            @foreach ($revised_announcement_request_titles as $title)
            <li>{{ $title }}</li>
            @endforeach
        </ul>
    </p>
    @endif

    <p>
        Segera proses permintaan tersebut mengklik tautan
        <a href="{{ URL::to('/') }}/announcement">ini</a>.
    </p>

    <p>
        Salam, <br>
        Humas Intern KKIS
    </p>

    <p>
        Ini adalah email dibuat secara otomatis oleh
        <a href="{{ URL::to('/') }}">Sistem Pengelolaan Pengumuman Terpadu KKIS</a>.
    </p>

</body>
