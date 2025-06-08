<?php
header('Content-Type: image/svg+xml');

// Get initials from query string or use default
$initials = isset($_GET['initials']) ? substr(strtoupper($_GET['initials']), 0, 2) : 'U';
$colors = ['#4CAF50', '#2196F3', '#FFC107', '#9C27B0', '#F44336'];
$color = $colors[array_rand($colors)];

echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:' . $color . ';stop-opacity:1" />
            <stop offset="100%" style="stop-color:' . $color . ';stop-opacity:0.8" />
        </linearGradient>
    </defs>
    <circle cx="100" cy="100" r="98" fill="url(#grad)"/>
    <text x="100" y="120" font-family="Arial" font-size="80" fill="white" text-anchor="middle" font-weight="bold">' . htmlspecialchars($initials) . '</text>
</svg>';
?> 