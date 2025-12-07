<?php
/**
 * HTMX Layout End Helper
 * 
 * Closes the layout by including appropriate footer
 */

$isHtmx = $isHtmxRequest ?? false;

if (!$isHtmx) {
    // Full page - include footer
    echo view('layout/footer', get_defined_vars());
} else {
    // Partial - include minimal JS reinit
    echo view('layout/partial_footer', get_defined_vars());
}
?>
