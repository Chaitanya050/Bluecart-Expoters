<?php
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg xmlns="http://www.w3.org/2000/svg" width="500" height="400" viewBox="0 0 500 400">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#4CAF50;stop-opacity:0.9" />
            <stop offset="100%" style="stop-color:#2196F3;stop-opacity:0.9" />
        </linearGradient>
        <style>
            .small-text { font: bold 14px Arial; fill: white; }
            .icon { font: 24px Arial; fill: white; }
        </style>
    </defs>
    
    <!-- Background -->
    <rect width="500" height="400" fill="url(#grad)" rx="15"/>
    
    <!-- Decorative Elements -->
    <g opacity="0.1" fill="white">
        <circle cx="50" cy="50" r="30"/>
        <circle cx="450" cy="350" r="40"/>
        <rect x="400" y="50" width="50" height="50" rx="10"/>
        <rect x="50" y="300" width="40" height="40" rx="8"/>
    </g>
    
    <!-- Device Icons -->
    <g transform="translate(150,100)">
        <!-- Laptop -->
        <rect x="0" y="0" width="200" height="120" fill="white" opacity="0.9" rx="10"/>
        <rect x="20" y="10" width="160" height="90" fill="#333" rx="5"/>
        <text x="100" y="55" class="small-text" text-anchor="middle">BlueCrate</text>
        
        <!-- Phone -->
        <rect x="250" y="20" width="60" height="100" fill="white" opacity="0.9" rx="10"/>
        <rect x="255" y="25" width="50" height="90" fill="#333" rx="5"/>
        <circle cx="280" cy="105" r="8" fill="white" opacity="0.9"/>
        
        <!-- Tablet -->
        <rect x="50" y="150" width="150" height="100" fill="white" opacity="0.9" rx="10"/>
        <rect x="55" y="155" width="140" height="90" fill="#333" rx="5"/>
    </g>
    
    <!-- Icons -->
    <g fill="white" opacity="0.8">
        <text x="50" y="350" class="icon">📱</text>
        <text x="100" y="350" class="icon">💻</text>
        <text x="150" y="350" class="icon">🎧</text>
        <text x="200" y="350" class="icon">⌚</text>
        <text x="250" y="350" class="icon">🖥️</text>
    </g>
</svg>';
?> 