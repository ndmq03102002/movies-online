@extends('admin.dashboard.layout')
@section('content')
<div class="table-responsive">
    <table class="table table-striped table-bordered" id="table-movie">
        <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th class="text-center">Name</th>
            {{-- <th class="text-center">Image</th> --}}
            <th class="text-center">Episode</th>
            <th class="text-center">Link phim</th>
            <th class="text-center">Thuộc phim</th>
            <th class="text-center">Nguồn phim</th>
            <th class="text-center">Status</th>
            <th class="text-center">Manager</th>
        </tr>
        </thead>
        <tbody>
        @if(isset($episodes) && $episodes->isNotEmpty())
            @foreach($episodes as $ep)
        <tr>
            <td>
                <input type="checkbox" value="{{ $ep->id }}" class="input-checkbox checkBoxItem">
            </td>
            <td>
                {{ $ep->movie->name }}
            </td>
            {{-- <td>
                
                @if (filter_var($ep->movie->image, FILTER_VALIDATE_URL)) 
                    <img src="{{ $ep->movie->image }}" alt="{{ $ep->movie->name }}" width="100" />
                @else
                    <img src="{{ asset('uploads/movie/' . $ep->movie->image) }}" alt="{{ $ep->movie->name }}" width="100" />
                @endif
            </td> --}}
            <td>
                {{ $ep->episode }}
            </td>
            
            
            <td>
                {{ $ep->linkphim }}
                {{-- <style type="text/css">
                    .iframe_phim iframe {
                        width: 100%;
                        height: 650;
                    }
                </style>
                <div class="iframe_phim">
                  
                    <iframe src="{{ $ep->linkphim }}" frameborder="0" allowfullscreen></iframe>
                </div> --}}
            </td>
            <td>
                {{ $ep->movie->thuocphim == 1 ? 'Phim lẻ' : 'Phim bộ' }}
            </td>
            <td>
                {{ $ep->source }}
            </td>
            <td>
                {{ $ep->status == 1 ? 'Hiển thị' : 'Không hiển thị' }}
            </td>
            <td class="text-center">
                <a href="{{ route('episode.edit', $ep->id) }}" class="btn btn-success" ><i class="fa fa-edit"></i></a>
                <form action="{{ route('episode.destroy', $ep->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
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
</div>
@endsection
