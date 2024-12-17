@extends('admin.dashboard.layout')
@section('content')
<div class="container">
    <h2>Update tập phim mới</h2>
    
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-row">
        <label for="api_source" class="control-label">Chọn nguồn phim:</label>
        <div class="form-check">
            <input type="radio" name="api_source" id="ophim_option" value="ophim" class="form-check-input" checked>
            <label class="form-check-label" for="ophim_option">API Ophim</label>
        </div>
        <div class="form-check">
            <input type="radio" name="api_source" id="kkphim_option" value="kkphim" class="form-check-input">
            <label class="form-check-label" for="kkphim_option">API KKphim</label>
        </div>
    </div>

    <form action="{{ route('updateEpisode') }}" method="POST" id="ophim_form">
        @csrf
        <div class="form-group mt-3">
            <label for="api_url">Page start - end</label>
            <input type="text" class="form-control" id="ophim_api_url" name="start" placeholder="Nhập page start vd: 1 Ophim"  required autofocus>
            <input type="text" class="form-control" id="ophim_api_url" name="end" placeholder="Nhập page end vd: 5 Ophim"  required>
        </div>
        <button type="submit" class="btn btn-primary">Thêm phim từ Ophim</button>
    </form>

    <form action="{{ route('updateEpisodekk') }}" method="POST" id="kkphim_form" style="display: none;">
        @csrf
        <div class="form-group mt-3">
            <label for="api_url">Page start - end</label>
            
            <input type="text" class="form-control" id="ophim_api_url" name="start" placeholder="Nhập page start vd: 1 kkphim"  required autofocus>
            <input type="text" class="form-control" id="ophim_api_url" name="end" placeholder="Nhập page end vd: 5 kkphim"  required>
        </div>
        <button type="submit" class="btn btn-primary">Thêm phim từ KKphim</button>
    </form>
</div>

<script>
    document.getElementById('ophim_option').addEventListener('click', function() {
        document.getElementById('ophim_form').style.display = 'block';
        document.getElementById('kkphim_form').style.display = 'none';
    });
    document.getElementById('kkphim_option').addEventListener('click', function() {
        document.getElementById('ophim_form').style.display = 'none';
        document.getElementById('kkphim_form').style.display = 'block';
    });
</script>
@endsection
