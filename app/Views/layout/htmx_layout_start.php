<?php
/**
 * HTMX Layout Helper
 * 
 * Include this at the top of your views to automatically handle
 * HTMX partial rendering vs full page rendering.
 * 
 * Usage in view:
 * <?= $this->include('layout/htmx_layout_start') ?>
 * ... your content ...
 * <?= $this->include('layout/htmx_layout_end') ?>
 */

// Determine which layout to use
$isHtmx = $isHtmxRequest ?? false;

if (!$isHtmx) {
    // Full page - include header and sidebar
    echo view('layout/header', get_defined_vars());
    echo view('layout/sidebar', get_defined_vars());
}
?>
