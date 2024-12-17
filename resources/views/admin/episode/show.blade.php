<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin phim</title>
</head>
<body>
    <h1>Thông tin phim</h1>
    @foreach ($movieDetails as $dt)
        <p><strong>Name:</strong> {{ $dt['name'] }}</p>
        <p><strong>Category:</strong> {{ $dt['slug'] }}</p>
        <ul>
            @foreach ($dt['episodes'] as $episode)
                <li>
                    <strong>Episode Name:</strong> {{ $episode['episode'] }}<br>
                    <strong>Link Embed:</strong> <a href="{{ $episode['link_embed'] }}">{{ $episode['link_embed'] }}</a><br>
                </li>
            @endforeach
        </ul>
        <hr>
    @endforeach
</body>
</html>
