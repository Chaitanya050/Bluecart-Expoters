<?php
/**
 * Admin Utility Functions
 */

/**
 * Format currency with appropriate symbol
 * @param float $amount Amount to format
 * @return string Formatted currency
 */
function formatCurrency($amount) {
    return number_format($amount, 2) . ' ' . CURRENCY_SYMBOL;
}

/**
 * Format date in a readable format
 * @param string $date Date in Y-m-d format
 * @return string Formatted date
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Format time in a readable format
 * @param string $time Time in H:i:s format
 * @return string Formatted time
 */
function formatTime($time) {
    return date('h:i A', strtotime($time));
}

/**
 * Get order status label with color
 * @param string $status Order status
 * @return string Status label HTML
 */
function getStatusLabel($status) {
    $status_classes = [
        'pending' => 'warning',
        'processing' => 'info',
        'shipped' => 'primary',
        'delivered' => 'success',
        'cancelled' => 'danger',
        'returned' => 'secondary'
    ];
    
    $status_text = ucfirst($status);
    $status_class = $status_classes[$status] ?? 'secondary';
    
    return "<span class='badge bg-$status_class'>$status_text</span>";
}

/**
 * Get product stock status label
 * @param int $stock_quantity Current stock quantity
 * @param int $low_stock_threshold Low stock threshold
 * @return string Stock status label HTML
 */
function getStockStatusLabel($stock_quantity, $low_stock_threshold) {
    if ($stock_quantity <= 0) {
        return "<span class='badge bg-danger'>Out of Stock</span>";
    } elseif ($stock_quantity <= $low_stock_threshold) {
        return "<span class='badge bg-warning'>Low Stock</span>";
    }
    return "<span class='badge bg-success'>In Stock</span>";
}

/**
 * Generate pagination links
 * @param int $total_items Total number of items
 * @param int $items_per_page Items per page
 * @param int $current_page Current page number
 * @param string $base_url Base URL for pagination
 * @return string Pagination HTML
 */
function generatePagination($total_items, $items_per_page, $current_page, $base_url) {
    $total_pages = ceil($total_items / $items_per_page);
    if ($total_pages <= 1) return '';

    $pagination = '<nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">';

    // Previous page
    if ($current_page > 1) {
        $prev = $current_page - 1;
        $pagination .= "<li class='page-item'>
            <a class='page-link' href='$base_url&page=$prev'>Previous</a>
        </li>";
    }

    // Page numbers
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            $pagination .= "<li class='page-item active'>
                <span class='page-link'>$i</span>
            </li>";
        } else {
            $pagination .= "<li class='page-item'>
                <a class='page-link' href='$base_url&page=$i'>$i</a>
            </li>";
        }
    }

    // Next page
    if ($current_page < $total_pages) {
        $next = $current_page + 1;
        $pagination .= "<li class='page-item'>
            <a class='page-link' href='$base_url&page=$next'>Next</a>
        </li>";
    }

    $pagination .= '</ul></nav>';
    return $pagination;
}

/**
 * Generate product image URL
 * @param string $image_filename Image filename
 * @return string Complete image URL
 */
function getProductImageUrl($image_filename) {
    if (empty($image_filename)) {
        return '../assets/images/default-product.png';
    }
    return '../uploads/products/' . $image_filename;
}

/**
 * Generate order status options for dropdown
 * @return string HTML options for status dropdown
 */
function getOrderStatusOptions($selected = null) {
    $statuses = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
        'returned' => 'Returned'
    ];

    $options = '';
    foreach ($statuses as $value => $text) {
        $selected_attr = ($selected === $value) ? 'selected' : '';
        $options .= "<option value='$value' $selected_attr>$text</option>";
    }
    return $options;
}

/**
 * Generate product category options for dropdown
 * @param PDO $pdo Database connection
 * @param int $selected Selected category ID
 * @return string HTML options for category dropdown
 */
function getCategoryOptions($pdo, $selected = null) {
    $options = '';
    try {
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
        while ($row = $stmt->fetch()) {
            $selected_attr = ($selected === $row['id']) ? 'selected' : '';
            $options .= "<option value='{$row['id']}' $selected_attr>{$row['name']}</option>";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
    }
    return $options;
}
