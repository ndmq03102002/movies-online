<div class="col-lg-4 col-md-6 col-sm-8">
  <div class="product__sidebar">
    <div class="product__sidebar__view">
      <div class="section-title">
        <h5>Top Views</h5>
      </div>
      
      <div class="filter__gallery">
        @foreach($topview as $mov)
          <a href="{{ route('movie', $mov->slug) }}">
            <div class="product__sidebar__view__item mix day years lazyload" data-setbg="{{ $mov->poster }}" style="background-repeat: no-repeat; background-size: cover; background-position: top center;">
              <div class="ep">{{ $mov->episode->count() }} / {{ $mov->sotap }}</div>
              <div class="view"><i class="fa fa-eye"></i> {{ $mov->count_views }} Views</div>
              <h5 style="color: white; font-weight: bold;">{{ $mov->name }}</h5>
            </div>
          </a>
        @endforeach
      </div>
    </div>
    <div class="product__sidebar__comment">
      <div class="section-title">
        <h5>New Comment</h5>
      </div>

      @foreach($new_comment as $mov)
        <a href="{{ route('movie', $mov->slug) }}">
          <div class="product__sidebar__comment__item">
            <div class="product__sidebar__comment__item__pic">
              @if (filter_var($mov->image, FILTER_VALIDATE_URL)) 
                <img data-src="{{ $mov->image }}" alt="{{ $mov->name }}" class="lazy" width="90" height="130" />
              @else
                <img data-src="{{ asset('uploads/movie/' . $mov->image) }}" alt="{{ $mov->name }}" class="lazy" width="90" height="130" />
              @endif
            </div>
            <div class="product__sidebar__comment__item__text">
              <ul>
                <li>Active</li>
                <li>Movie</li>
              </ul>
              <h5>
                <a href="{{ route('movie', $mov->slug) }}">{{ $mov->name }}</a>
              </h5>
              <span><i class="fa fa-eye"></i> {{ $mov->count_views }} Views</span>
            </div>
          </div>
        </a>
      @endforeach

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          

          // Lazy load images
          const lazyImages = document.querySelectorAll('.lazy');

          const loadImage = (image) => {
              const src = image.getAttribute('data-src');
              if (src) {
                  image.src = src;
                  image.classList.remove('lazy'); // Xóa class lazy sau khi tải xong
              }
          };

          const imgObserver = new IntersectionObserver((entries, observer) => {
              entries.forEach(entry => {
                  if (entry.isIntersecting) {
                      loadImage(entry.target);
                      observer.unobserve(entry.target); // Ngừng theo dõi hình ảnh đã tải
                  }
              });
          });

          lazyImages.forEach(image => {
              imgObserver.observe(image);
          });
        });
      </script>

    </div>
  </div>
</div>
