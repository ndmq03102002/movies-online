<script src="fontend/js/jquery-3.3.1.min.js"></script>
<script src="fontend/js/jquery.slicknav.js"></script>
<script src="fontend/js/bootstrap.min.js" ></script>
<script src="fontend/js/player.js" ></script>
<script src="fontend/js/jquery.nice-select.min.js" ></script>
<script src="fontend/js/mixitup.min.js" ></script>
<script src="fontend/js/owl.carousel.min.js"></script>
<script src="fontend/js/main.js"></script>
<script>
    function toggleSearch() {
    // Lấy phần tử có class .search-model
    const searchModel = document.querySelector('.search-model');
    
    // Kiểm tra nếu modal đang hiển thị hay không
    if (searchModel.classList.contains('active')) {
        // Nếu đang hiển thị, loại bỏ class 'active' để ẩn modal
        searchModel.classList.remove('active');
    } else {
        // Nếu chưa hiển thị, thêm class 'active' để hiện modal
        searchModel.classList.add('active');
    }
}

// Đóng modal khi nhấn nút close
document.querySelector('.search-close-switch').addEventListener('click', function() {
    document.querySelector('.search-model').classList.remove('active');
});

</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lazyElements = document.querySelectorAll('.lazyload');
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const bgImage = img.getAttribute('data-setbg');
                    img.style.backgroundImage = `url(${bgImage})`;
                    img.classList.remove('lazyload');
                    observer.unobserve(img);
                }
            });
        });
    
        lazyElements.forEach(element => {
            observer.observe(element);
        });
    });
    
</script>

