<?php
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="400" viewBox="0 0 1200 400">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#4CAF50;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#2196F3;stop-opacity:1" />
        </linearGradient>
    </defs>
    <rect width="1200" height="400" fill="url(#grad)"/>
    <text x="600" y="200" font-family="Arial" font-size="48" fill="white" text-anchor="middle" font-weight="bold">Welcome to BlueCrate Exports</text>
    <text x="600" y="250" font-family="Arial" font-size="24" fill="white" text-anchor="middle">Quality Electronics for Global Markets</text>
</svg>';
?> 