@extends('admin.dashboard.layout')
@section('content')

@php
    if ($config['method'] == 'create') {
        $url = route('genre.store');
        $title = 'Thêm mới thể loại';
        $genre = null; // Đảm bảo biến $genre không được sử dụng trong chế độ tạo mới
        $method = 'POST';
    } else {
        $url = route('genre.update', $genre->id);
        $title = 'Cập nhật thể loại';
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
                    <div class="panel-title">Thông tin thể loại</div>
                    <div class="panel-description">
                        <p>Nhập thông tin của thể loại mới</p>
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
                                    <label for="name" class="control-label text-left">Name <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        id="slug"
                                        value="{{ old('name', $genre->name ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập tên danh mục"
                                        autocomplete="off"
                                        onkeyup="ChangeToSlug()"
                                        autofocus
                                    >
                                    @if($errors->has('name'))
                                        <p class="error-message">* {{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="name" class="control-label text-left">Slug </label>
                                    <input 
                                        type="text"
                                        name="slug"
                                        id="convert_slug"
                                        value="{{ old('name', $genre->slug ?? '') }}"
                                        class="form-control"
                                        placeholder="Nhập dữ liệu..."
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="description" class="control-label text-left">Description</label>
                                    <textarea 
                                        type="text"
                                        name="description"
                                        id="description"
                                        
                                        class="form-control"
                                        placeholder="Nhập mô tả danh mục"
                                        autocomplete="off"
                                        stlye = "resize: none;"
                                        rows = "6"
                                    >{{ old('description', $genre->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="status" class="control-label text-left">Active</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ isset($genre) && $genre->status == '1' ? 'selected' : '' }}>Hiển thị</option>
                                        <option value="0" {{ isset($genre) && $genre->status == '0' ? 'selected' : '' }}>Không</option>
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