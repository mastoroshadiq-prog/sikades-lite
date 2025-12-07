<?php
/**
 * Partial footer - for HTMX requests
 * Only includes JavaScript that needs re-initialization
 */
?>

<script>
// Re-initialize DataTables if present
if (typeof $.fn.DataTable !== 'undefined') {
    $('table.datatable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
}

// Re-initialize Chart.js if charts exist
if (typeof Chart !== 'undefined' && window.initCharts) {
    window.initCharts();
}
</script>
