<?php
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">
    <rect width="300" height="300" fill="#f5f5f5"/>
    <path d="M100 100h100v100h-100z" fill="#e0e0e0"/>
    <text x="150" y="150" font-family="Arial" font-size="14" fill="#666" text-anchor="middle">Product Image</text>
    <text x="150" y="170" font-family="Arial" font-size="12" fill="#999" text-anchor="middle">300 x 300</text>
</svg>';
?> 