@extends('admin.dashboard.layout')
@section('content')
<table class="table table-striped table-bordered" id="table-movie">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th class="text-center">Name</th>
        <th class="text-center">Slug</th>
        <th class="text-center">Description</th>
        <th class="text-center">Active/Inactive</th>
        <th class="text-center">Manager</th>
    </tr>
    </thead>
    <tbody>
    @if(isset($years) && $years->isNotEmpty())
        @foreach($years as $cate)
    <tr>
        <td>
            <input type="checkbox" value="{{ $cate->id }}" class="input-checkbox checkBoxItem">
        </td>
        <td>
            {{ $cate->name }}
        </td>
        <td>
            {{-- {{ $category->parent->name ?? ''}} --}}
            {{ $cate->slug }}
        </td>
        <td>
            {{ $cate->description }}
        </td>
        <td>
            
            {{ $cate->status == 1 ? 'Hiển thị' : 'Không hiển thị' }}
        </td>
        <td class="text-center">
            <a href="{{ route('year.edit', $cate->id) }}" class="btn btn-success" ><i class="fa fa-edit"></i></a>
            <form action="{{ route('year.destroy', $cate->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
            
        </td>
    </tr>
        @endforeach
    @endif
    </tbody>
</table>
@endsection