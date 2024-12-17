@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('episode.store');
        $title = 'Thêm mới tập phim';
        $episode = null; // Đảm bảo biến $category không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('episode.update', $episode->id);
        $title = 'Cập nhật tập phim';
        $method = 'PUT';
    }
@endphp

@include('admin.dashboard.component.breadcrumb', ['title' => $title])
    <form action="{{$url}}" method="POST">
    @csrf
    @method($method)
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
                <div class="panel-head">
                    <div class="panel-title">Thông tin tập phim</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của tập phim mới</p>
                        <p>Lưu ý: Những trường đánh dấu <span class="text-danger">(*)</span> là bắt buộc</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name" class="control-label text-left">Name </label>
                                    <input 
                                        type="hiden"
                                        name="name"
                                        id="name"
                                        value = "{{isset($episode) ? $episode->movie->name : $movies->name}}";
                                        class="form-control"
                                        readonly
                                    >
                                </div>
                                <input 
                                    type="hidden"
                                    name="movie_id"
                                    id="movie_id"
                                    value="{{ isset($episode) ? $episode->movie->id : $movies->id }}"  {{-- Gửi ID của phim --}}
                                >
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name" class="control-label text-left">Link phim <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="link"
                                        id="link"
                                        value="{{ old('link', $episode->linkphim ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập link phim"
                                        autocomplete="off"
                                        autofocus
                                    >
                                    @error('episode')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @if(isset($episode) ? $episode->movie->thuocphim == 0 : $movies->thuocphim == 0)
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name" class="control-label text-left">Tập phim <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="episode"
                                        id="episode"
                                        value="{{ old('episode', $episode->episode ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập số tập phim"
                                        autocomplete="off"
                                    >
                                    @error('episode')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @else
                                <input 
                                    type="hidden"
                                    name="episode"
                                    id="episode"
                                    value="1"
                                >
                            @endif
                            
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="status" class="control-label text-left">Active</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ isset($episode) && $episode->status == '1' ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ isset($episode) && $episode->status == '0' ? 'selected' : '' }}>Không</option>
                                    </select>                                      
                                                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>


@endsection