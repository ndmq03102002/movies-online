
    <!-- Mainly scripts -->
    
    <script src="backend/js/bootstrap.min.js"></script>
    <script src="backend/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="backend/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    
    <script src="backend/js/inspinia.js"></script>
    <script src="backend/js/plugins/pace/pace.min.js"></>
    <script src="backend/js/plugins/flot/jquery.flot.js"></script>
    <script src="backend/js/plugins/peity/jquery.peity.min.js"></script>
    <script src="backend/js/demo/peity-demo.js"></script>
    <script src="backend/js/inspinia.js"></script>
    <script src="backend/js/plugins/pace/pace.min.js"></script>
    <script src="backend/js/plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="backend/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="backend/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="backend/js/plugins/easypiechart/jquery.easypiechart.js"></script>
    <script src="backend/js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="backend/js/demo/sparkline-demo.js"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.6/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#table-movie');
    </script>
    <script>
        function ChangeToSlug() {
            let slug = document.getElementById("slug").value.toLowerCase();

            // Chuyển các ký tự có dấu thành không dấu
            slug = slug.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            // Thay thế các ký tự đặc biệt như 'đ' thành 'd'
            slug = slug.replace(/đ/g, 'd');

            // Xóa các ký tự không phải chữ cái, số, khoảng trắng và dấu gạch ngang
            slug = slug.replace(/[^a-z0-9\s-]/g, '');

            // Thay thế nhiều khoảng trắng hoặc dấu gạch ngang liên tiếp bằng một dấu gạch ngang duy nhất
            slug = slug.replace(/\s+/g, '-').replace(/-+/g, '-');

            // Xóa dấu gạch ngang ở đầu và cuối chuỗi
            slug = slug.replace(/^-+|-+$/g, '');

            // Gán giá trị slug vào textbox có id "convert_slug"
            document.getElementById('convert_slug').value = slug;
        }

    </script>

    
    
    