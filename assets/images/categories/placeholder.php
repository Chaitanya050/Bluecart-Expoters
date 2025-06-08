<?php
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="200" viewBox="0 0 400 200">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#4CAF50;stop-opacity:0.8" />
            <stop offset="100%" style="stop-color:#2196F3;stop-opacity:0.9" />
        </linearGradient>
    </defs>
    <rect width="400" height="200" fill="url(#grad)" rx="10"/>
    <g fill="white" opacity="0.9">
        <rect x="150" y="60" width="100" height="80" rx="5"/>
        <rect x="170" y="80" width="60" height="40" rx="3"/>
    </g>
    <text x="200" y="170" font-family="Arial" font-size="16" fill="white" text-anchor="middle">Category Name</text>
</svg>';
?> 