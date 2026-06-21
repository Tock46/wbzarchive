<?php
session_start();
require '/var/www/vhosts/szslibrary.com/httpdocs/included/header.php';
require '/var/www/vhosts/szslibrary.com/httpdocs/included/topbar.php'; // Top Login Button
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>SZS Library - Tracks</title>
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="./style.css" />

<!-- jQuery -->
<script src="/js/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="/js/jquery.dataTables.min.js"></script>
<!-- Buttons extension JS -->
<script src="/js/dataTables.buttons.min.js"></script>
<script src="/js/buttons.colVis.min.js"></script>
<!-- Required for export buttons -->
<script src="/js/buttons.html5.min.js"></script>
<script src="/js/jszip.min.js"></script>
<!-- DataTables CSS -->
<link rel="stylesheet" href="/css/jquery.dataTables.min.css" />
<!-- Buttons extension CSS -->
<link rel="stylesheet" href="/css/buttons.dataTables.min.css" />
<!-- Responsive extension JS + CSS -->
<script src="/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="/css/responsive.dataTables.css" />

</head>
<body>
<div id="container">
	<a href='/'><h1>SZS Library</a> - <a href='/?type=distribution'>Distributions</a></h1>

<table id="trackTable" class="display" style="width:100%">
</table>
<?php
if (!isset($_GET['type'])){
?>


<script>
$(document).ready(function () {

    // Initialize DataTable
    var table = $('#trackTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 20,
		lengthMenu: [10, 25, 50, 100, 200, 500],
        responsive: {
            details: {
                renderer: function(api, rowIdx, columns){
                    var data = $.map(columns, function(col){
                        if(col.hidden && col.data) {
                            return '<div>'+col.title+': '+col.data+'</div>';
                        }
                        return '';
                    }).join('');
                    return data ? $('<div/>').append(data) : false;
                }
            }
        },
        ajax: {
            url: 'fetch_tracks.php',
            type: 'POST',
            data: function (d) {
                const urlParams = new URLSearchParams(window.location.search);
                d.clan = urlParams.get('clan') || '';
                d.id = urlParams.get('id') || '';
                d.family = urlParams.get('family') || '';
                d.author = urlParams.get('author') || '';
            }
        },
        columns: [
            { data: 'id_first', title: 'ID', responsivePriority: 1 },
            { data: 'trackname', title: 'Name', responsivePriority: 1 },
            { data: 'track_version', title: 'Version', responsivePriority: 4 },
            { data: 'track_author', title: 'Author', responsivePriority: 3 },
            { data: 'track_type', title: 'Type', responsivePriority: 8 },
            { data: 'track_family', title: 'Family', responsivePriority: 5 },
            { data: 'track_clan', title: 'Clan', responsivePriority: 7 },
            { data: 'track_created', title: 'Created', responsivePriority: 6 },
            { data: 'track_sha1', title: 'SHA1', className: 'none', responsivePriority: 100 },
            { data: 'sha1_aliases', title: 'SHA1 Alias', className: 'none', responsivePriority: 100 }
            <?php if (isset($_SESSION['loggedin'])): ?>
                ,{ data: 'edit', title: 'Edit', responsivePriority: 1 }
            <?php endif; ?>
        ],
        columnDefs: [
            { targets: 0, width: "60px", className: "min-60"},
            { targets: 1, width: "250px", className: "min-250" },
            { targets: 7, width: "50px", className: "min-50" },
        ],
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'colvis', text: 'Toggle Columns' },
            { extend: 'pageLength' },
            { extend: 'copyHtml5', text: 'Copy' },
            { extend: 'csvHtml5', text: 'Export CSV' },
            { extend: 'excelHtml5', text: 'Export Excel' }
        ]
    });

    // Function to safely close all open child rows
    function closeAllChildRows() {
        table.rows().every(function () {
            if (this.child.isShown()) {
                this.child.hide();
            }
        });
    }

    // Clan filter
    $('#trackTable').on('click', '.filter-clan', function (e) {
        e.preventDefault();
        var clan = $(this).data('clan');
        closeAllChildRows();
        table.column(6).search(clan, false, false).draw();
    });
	
    // id filter
    $('#trackTable').on('click', '.filter-id', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        closeAllChildRows();
        table.column(0).search(id, false, false).draw();
    });

    // Family filter
    $('#trackTable').on('click', '.filter-family', function (e) {
        e.preventDefault();
        var family = $(this).data('family');
        closeAllChildRows();
        table.column(5).search(family, false, false).draw();
    });

    // Author filter
    $('#trackTable').on('click', '.filter-author', function (e) {
        e.preventDefault();
        var author = $(this).data('author');
        closeAllChildRows();
        table.column(3).search(author, false, false).draw();
    });

    // Go to Page logic
    $('#goToPage').on('change', function () {
        var page = parseInt($(this).val(), 10) - 1; // DataTables uses 0-based index
        if (!isNaN(page) && page >= 0 && page < table.page.info().pages) {
            closeAllChildRows();
            table.page(page).draw('page');
        }
    });

    // Ensure Responsive recalculates on search/redraw
    table.on('search.dt draw.dt', function () {
        table.responsive.recalc();
    });

});
</script>
<?php
	}
?>
<?php
if (isset($_GET['type'])){
?>
<script>
$(document).ready(function () {

    // Initialize DataTable
    var table = $('#trackTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 20,
        responsive: {
            details: {
                renderer: function(api, rowIdx, columns){
                    var data = $.map(columns, function(col){
                        if(col.hidden && col.data) {
                            return '<div>'+col.title+': '+col.data+'</div>';
                        }
                        return '';
                    }).join('');
                    return data ? $('<div/>').append(data) : false;
                }
            }
        },
        ajax: {
            url: 'fetch_distrib.php',
            type: 'POST',
            data: function (d) {
                const urlParams = new URLSearchParams(window.location.search);
            }
        },
        columns: [
            { data: 'dist_id', title: 'ID', responsivePriority: 1 },
            { data: 'dist_name', title: 'Name', responsivePriority: 1 },
            { data: 'dist_version', title: 'Version', responsivePriority: 4 },
            { data: 'dist_author', title: 'Author', responsivePriority: 3 },
            { data: 'dist_release', title: 'Created', responsivePriority: 4 }
            <?php if (isset($_SESSION['loggedin'])): ?>
                ,{ data: 'edit', title: 'Edit', responsivePriority: 1 }
            <?php endif; ?>
        ],
        columnDefs: [
        ],
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'colvis', text: 'Toggle Columns' },
            { extend: 'pageLength' },
            { extend: 'copyHtml5', text: 'Copy' },
            { extend: 'csvHtml5', text: 'Export CSV' },
            { extend: 'excelHtml5', text: 'Export Excel' }
        ]
    });

    // Function to safely close all open child rows
    function closeAllChildRows() {
        table.rows().every(function () {
            if (this.child.isShown()) {
                this.child.hide();
            }
        });
    }

    // Go to Page logic
    $('#goToPage').on('change', function () {
        var page = parseInt($(this).val(), 10) - 1; // DataTables uses 0-based index
        if (!isNaN(page) && page >= 0 && page < table.page.info().pages) {
            closeAllChildRows();
            table.page(page).draw('page');
        }
    });

    // Ensure Responsive recalculates on search/redraw
    table.on('search.dt draw.dt', function () {
        table.responsive.recalc();
    });

});
</script>
<?php
	}
?>

<div id="goToPageWrapper" style="padding-bottom: 10px;">
  <label>Go to page: <input type="number" id="goToPage" min="1" style="width: 60px;" /></label>
</div>
</div>

<?php
require '/var/www/vhosts/szslibrary.com/httpdocs/included/footer.php'; // Bottom Footer
?>
</body>
</html>
