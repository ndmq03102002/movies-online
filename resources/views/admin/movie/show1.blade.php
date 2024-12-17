<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin phim</title>
</head>
<body>
    <h1>Thông tin phim</h1>
    <p><strong>Name:</strong> {{ $data['name'] }}</p>
    <p><strong>Category:</strong> {{ $data['type'] }}</p>
    <p><strong>Nội dung:</strong> {{ $data['content'] }}</p>
    <p><strong>Trailer:</strong> {{ $data['trailer_url'] }}</p>
    <p><strong>Thời gian:</strong> {{ $data['time'] }}</p>
    <p><strong>Tổng số tập:</strong> {{ $data['episode_total'] }}</p>
    <p><strong>Chất lượng:</strong> {{ $data['quality'] }}</p>
    <p><strong>Ngôn ngữ:</strong> {{ $data['lang'] }}</p>
    <p><strong>Năm phát hành:</strong> {{ $data['year'] }}</p>
    <p><strong>Thể loại:</strong> {{ implode(', ', $data['categories']) }}</p>
    <p><strong>Quốc gia:</strong> {{ implode(', ', $data['countries']) }}</p>
    <p><strong>Linkphim:</strong> {{ $data['link_embed'] }}</p>
</body>
</html>
