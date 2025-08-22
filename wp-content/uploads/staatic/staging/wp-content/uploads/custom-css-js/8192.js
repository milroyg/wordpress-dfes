<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
/* Default comment here */ 
jQuery(document).ready(function() {
    var table = jQuery('#table_1');  // Adjust this to your actual table ID or class
    
    // Check if DataTable is initialized
    if (!$.fn.dataTable.isDataTable(table)) {
        table.DataTable({
            "order": [[5, 'desc']],  // Sort column 5 (index 4) in descending order
            "columnDefs": [
                { "orderable": true, "targets": [5] }  // Ensure column 5 is sortable
            ]
        });
    }
});
</script>
<!-- end Simple Custom CSS and JS -->
