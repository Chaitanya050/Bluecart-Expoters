<?php
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="60" viewBox="0 0 200 60">
    <style>
        .logo-text { font-family: Arial, sans-serif; font-weight: bold; }
        .main-text { font-size: 24px; }
        .sub-text { font-size: 12px; }
    </style>
    <rect x="10" y="10" width="40" height="40" rx="5" fill="#4CAF50"/>
    <path d="M20 30h20v15h-20z" fill="#2196F3"/>
    <text x="60" y="35" class="logo-text main-text" fill="#333">BlueCrate</text>
    <text x="60" y="48" class="logo-text sub-text" fill="#666">EXPORTS</text>
</svg>';
?> 