

{{-- <div class="col-lg-8"> 
    <div class="anime__details__review">
        <div class="section-title">
            <h5>Reviews</h5>
        </div>
        @foreach($comments->take(5) as $comment)
        <div class="anime__review__item">
            <div class="anime__review__item__pic">
                <img src="fontend/img/anime/review-1.jpg" alt="">
            </div>
            <div class="anime__review__item__text">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h6>{{ $comment->user->username }} - 
                        <span>{{ $comment->created_at->diffForHumans() }}</span>
                    </h6>

                    @if(Auth::id() == $comment->user_id)
                    <div style="position: relative;">
                        <button class="btn btn-success edit-comment-btn" data-comment-id="{{ $comment->id }}"><i class="fa fa-edit"></i></button>
                        
                        <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <p id="comment-content-{{ $comment->id }}">{{ $comment->content }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="anime__details__form">
        <div class="section-title">
            <h5>Your Comment</h5>
        </div>
    <p>{{$comment}}</p>
        <!-- Nếu có comment cần chỉnh sửa thì hiển thị form với comment cũ -->
        @if(isset($comment))
        <form  action="{{ route('comment.store', $episode_id) }}" method="POST"> 
            @csrf
            <textarea id="comment-textarea" name="content" placeholder="Your Comment"></textarea>
            <button type="submit" id="submit-comment-btn"><i class="fa fa-location-arrow"></i> Review</button>
        </form>
        @else
        <!-- Form cho comment mới -->
        
        <form id="comment-form" method="POST">
            @csrf
            <textarea action="{{ route('comment.update', $episode_id) }}"  id="comment-textarea" name="content" placeholder="Your Comment"></textarea>
            <button type="submit" id="submit-comment-btn"><i class="fa fa-location-arrow"></i> Update </button>
        </form>
        @endif
    </div>
</div> --}}

{{-- 
<script>
    $(document).ready(function() {
        // Bắt sự kiện khi người dùng bấm nút chỉnh sửa
        $('.edit-comment-btn').on('click', function() {
            var commentId = $(this).data('comment-id');

            // Gửi AJAX request để lấy nội dung comment
            $.ajax({
                url: '/comments/' + commentId + '/edit', // Thay URL bằng route('comment.edit')
                type: 'GET',
                success: function(response) {
                    // Đổ dữ liệu comment cũ vào form
                    $('#comment-textarea').val(response.content);
                    $('#comment-form').attr('action', '{{ route('comment.update', '') }}/' + commentId);
                    // $('#submit-comment-btn').text('Update');

                    // Cuộn xuống form và focus vào textarea sau khi tải xong dữ liệu
                    var commentForm = document.getElementById('comment-form');
                    if (commentForm) {
                        commentForm.scrollIntoView({ behavior: 'smooth' });

                        // Tự động focus vào textarea để nhập comment
                        var textarea = commentForm.querySelector('textarea');
                        if (textarea) {
                            textarea.focus();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    alert('Đã xảy ra lỗi khi lấy comment.');
                }
            });
        });

        // Bắt sự kiện khi submit form (cập nhật comment)
        $('#comment-form').on('submit', function(e) {
            e.preventDefault();

            var formAction = $(this).attr('action');
            var formData = $(this).serialize(); // Lấy dữ liệu từ form

            // Gửi AJAX request để cập nhật comment
            $.ajax({
                url: formAction,
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Cập nhật nội dung comment trong view mà không cần tải lại trang
                    $('#comment-content-' + response.id).text(response.content);
                    $('#comment-textarea').val('');
                    $('#submit-comment-btn').text('Review');
                },
                error: function(xhr, status, error) {
                    alert('Đã xảy ra lỗi khi cập nhật comment.');
                }
            });
        });
    });

</script>  --}}


<div class="col-lg-8"> 
    <div class="anime__details__review">
        <div class="section-title">
            <h5>Reviews</h5>
        </div>
        @foreach($comments->take(5) as $comment)
                @php
                    $avatar = $comment->user->profile->avatar ?? 'fontend/img/anime/review-1.jpg'; // Avatar mặc định nếu không có
                @endphp
        <div class="anime__review__item">
            <div class="anime__review__item__pic">
                <img src="{{$avatar}}" alt="">
            </div>
            <div class="anime__review__item__text">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h6>{{ $comment->user->username }} - 
                        <span>{{ $comment->updated_at->diffForHumans() }}</span>
                    </h6>

                    @if(Auth::id() == $comment->user_id)
                    <div style="position: relative;">
                        <button class="btn btn-success edit-comment-btn" data-comment-id="{{ $comment->id }}"><i class="fa fa-edit"></i></button>
                        <form action="{{ route('comment.destroy', $comment->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <p id="comment-content-{{ $comment->id }}">{{ $comment->content }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="anime__details__form">
        <div class="section-title">
            <h5>Your Comment</h5>
        </div>
        @php
            if ($config['method'] == 'movie'  || $config['method'] == 'watch_trailer') {
                $id = ['movie_id' => $movie->id, 'episode_id' => null];
                
            } elseif ($config['method'] == 'watch') {
                $id = ['movie_id' => $movie->id, 'episode_id' => $episode_id];
            }
        @endphp
        <form id="comment-form" method="POST" action="{{ route('comment.store', $id) }}">
            @csrf
            <textarea id="comment-textarea" name="content" placeholder="Your Comment"></textarea>
            <button type="submit" id="submit-comment-btn"><i class="fa fa-location-arrow"></i> Review</button>
        </form>

        <!-- Nếu có comment cần chỉnh sửa thì hiển thị form với comment cũ -->
        @if(isset($editComment))
            <script>
                $(document).ready(function() {
                    $('#comment-textarea').val('{{ $editComment->content }}');
                    $('#comment-form').attr('action', '{{ route('comment.update', $editComment->id) }}');
                });
            </script>
        @endif
    </div>
</div>

<script>
    $(document).ready(function() {
        // Bắt sự kiện khi người dùng bấm nút chỉnh sửa
        $('.edit-comment-btn').on('click', function() {
            var commentId = $(this).data('comment-id');
            var commentContent = $('#comment-content-' + commentId).text().trim();

            // Đổ dữ liệu comment cũ vào form
            $('#comment-textarea').val(commentContent);
            $('#comment-form').attr('action', '{{ route('comment.update', '') }}/' + commentId);

            // Cuộn xuống form và focus vào textarea
            var commentForm = document.getElementById('comment-form');
            if (commentForm) {
                commentForm.scrollIntoView({ behavior: 'smooth' });
                $('#comment-textarea').focus();
            }
        });
    });
</script>

    
    