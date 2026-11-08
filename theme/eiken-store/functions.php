<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Soporte del tema para WooCommerce y características modernas
 */
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption', 'style', 'script']);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('eiken-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap', [], null);
    wp_enqueue_style('eiken-store-style', get_stylesheet_uri(), [], '2.0.0');
});

/**
 * 2. Traducción automática al español de textos de WooCommerce en Carrito y Checkout
 */
add_filter('gettext', function ($translated, $text, $domain) {
    $map = [
        'Add to cart' => 'Agregar al carrito',
        'Read more' => 'Ver producto',
        'Select options' => 'Elegir opciones',
        'View cart' => 'Ver carrito',
        'Checkout' => 'Finalizar compra',
        'Proceed to checkout' => 'Ir a Pagar con Webpay Plus',
        'Update cart' => 'Actualizar carrito',
        'Apply coupon' => 'Aplicar cupón',
        'Coupon code' => 'Código de descuento',
        'Coupon:' => 'Cupón:',
        'Product' => 'Producto',
        'Price' => 'Precio',
        'Quantity' => 'Cantidad',
        'Subtotal' => 'Subtotal',
        'Total' => 'Total a Pagar',
        'Cart totals' => 'Resumen de tu Carrito',
        'Billing details' => 'Datos del Comprador y Entrega',
        'Additional information' => 'Notas del Pedido (Talla, color o indicaciones)',
        'Order notes' => 'Indicaciones especiales para tu pedido',
        'Notes about your order, e.g. special notes for delivery.' => 'Ej: Talla L, color negro, o indicar si retiras en taller.',
        'Your order' => 'Resumen de tu Compra',
        'Place order' => 'Pagar Ahora con Webpay Plus',
        'First name' => 'Nombre',
        'Last name' => 'Apellidos',
        'Company name' => 'Empresa (opcional)',
        'Country / Region' => 'País',
        'Street address' => 'Dirección (Calle y número)',
        'House number and street name' => 'Calle y número (o indicar Retiro en Taller)',
        'Apartment, suite, unit, etc.' => 'Depto / Oficina / Referencia (opcional)',
        'Town / City' => 'Ciudad / Comuna',
        'State / County' => 'Región',
        'Postcode / ZIP' => 'Código Postal (opcional)',
        'Phone' => 'Teléfono / WhatsApp',
        'Email address' => 'Correo electrónico',
        'Your cart is currently empty.' => 'Tu carrito de compras está vacío actualmente.',
        'Return to shop' => 'Volver a la Tienda',
        'Related products' => 'Productos Relacionados',
        'Description' => 'Descripción',
        'Additional information' => 'Especificaciones',
        'Reviews' => 'Valoraciones',
        'Order received' => '¡Pedido Recibido!',
        'Thank you. Your order has been received.' => '¡Gracias por tu compra! Tu pedido ha sido registrado correctamente.',
        'Order number:' => 'N° de Orden:',
        'Date:' => 'Fecha:',
        'Email:' => 'Correo:',
        'Payment method:' => 'Método de Pago:',
        'Order details' => 'Detalle de tu Pedido',
        'Billing address' => 'Datos de Facturación / Entrega',
    ];
    return $map[$text] ?? $map[$translated] ?? $translated;
}, 20, 3);

/**
 * Hacer que el Código Postal no sea obligatorio en Chile para agilizar el Checkout
 */
add_filter('woocommerce_default_address_fields', function ($fields) {
    if (isset($fields['postcode'])) {
        $fields['postcode']['required'] = false;
    }
    if (isset($fields['address_2'])) {
        $fields['address_2']['required'] = false;
    }
    return $fields;
});

/**
 * 3. Custom Post Type: Cotizaciones Eiken (visible en WP-Admin)
 */
add_action('init', function () {
    register_post_type('eiken_quote', [
        'labels' => [
            'name' => 'Cotizaciones Eiken',
            'singular_name' => 'Cotización',
            'menu_name' => 'Cotizaciones Eiken',
            'all_items' => 'Todas las Cotizaciones',
            'add_new_item' => 'Nueva Cotización',
            'edit_item' => 'Ver / Editar Cotización',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-clipboard',
        'menu_position' => 26,
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);
});

add_filter('manage_eiken_quote_posts_columns', function ($columns) {
    return [
        'cb' => $columns['cb'],
        'title' => 'Cliente / Solicitud',
        'service' => 'Servicio Solicitado',
        'contact' => 'Contacto',
        'status' => 'Estado',
        'date' => 'Fecha',
    ];
});

add_action('manage_eiken_quote_posts_custom_column', function ($column, $post_id) {
    if ($column === 'service') {
        echo esc_html(get_post_meta($post_id, '_eiken_service', true) ?: 'Personalizado');
    } elseif ($column === 'contact') {
        $email = get_post_meta($post_id, '_eiken_email', true);
        $phone = get_post_meta($post_id, '_eiken_phone', true);
        echo esc_html($email) . '<br><small>' . esc_html($phone) . '</small>';
    } elseif ($column === 'status') {
        $st = get_post_meta($post_id, '_eiken_status', true) ?: 'Pendiente';
        echo '<span style="background:#ffedd5;color:#c2410c;padding:3px 10px;border-radius:99px;font-weight:700;font-size:12px;">' . esc_html($st) . '</span>';
    }
}, 10, 2);

/**
 * 4. Endpoints AJAX para Carrito Lateral (Cart Drawer) y Modal de Cotizaciones
 */
function eiken_build_cart_payload() {
    if (!function_exists('WC') || !WC()->cart) {
        return ['count' => 0, 'subtotal' => '$0', 'items' => []];
    }
    $items = [];
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        if (!$product) continue;
        $img_url = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
        if (!$img_url) {
            $img_url = get_template_directory_uri() . '/logo.png';
        }
        $items[] = [
            'key' => $cart_item_key,
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'qty' => (int) $cart_item['quantity'],
            'price' => (int) $product->get_price(),
            'priceFormatted' => '$' . number_format((float) $product->get_price(), 0, ',', '.'),
            'lineTotalFormatted' => '$' . number_format((float) $cart_item['line_total'], 0, ',', '.'),
            'image' => $img_url,
            'size' => $cart_item['eiken_size'] ?? '',
            'maxStock' => $product->get_stock_quantity() ?: 99,
        ];
    }
    return [
        'count' => (int) WC()->cart->get_cart_contents_count(),
        'subtotal' => '$' . number_format((float) WC()->cart->get_subtotal(), 0, ',', '.'),
        'items' => $items,
        'cartUrl' => wc_get_cart_url(),
        'checkoutUrl' => wc_get_checkout_url(),
    ];
}

add_action('wp_ajax_eiken_get_cart', 'eiken_ajax_get_cart');
add_action('wp_ajax_nopriv_eiken_get_cart', 'eiken_ajax_get_cart');
function eiken_ajax_get_cart() {
    wp_send_json_success(eiken_build_cart_payload());
}

add_action('wp_ajax_eiken_add_to_cart', 'eiken_ajax_add_to_cart');
add_action('wp_ajax_nopriv_eiken_add_to_cart', 'eiken_ajax_add_to_cart');
function eiken_ajax_add_to_cart() {
    $product_id = intval($_POST['product_id'] ?? 0);
    $qty = max(1, intval($_POST['quantity'] ?? 1));
    $size = sanitize_text_field($_POST['size'] ?? '');
    if ($product_id <= 0 || !function_exists('WC')) {
        wp_send_json_error(['message' => 'Producto inválido']);
    }
    $cart_item_data = [];
    if ($size !== '') {
        $cart_item_data['eiken_size'] = $size;
    }
    $added = WC()->cart->add_to_cart($product_id, $qty, 0, [], $cart_item_data);
    if (!$added) {
        wp_send_json_error(['message' => 'No se pudo agregar al carrito (verifica el stock disponible).']);
    }
    wp_send_json_success(eiken_build_cart_payload());
}

add_action('wp_ajax_eiken_update_cart_qty', 'eiken_ajax_update_cart_qty');
add_action('wp_ajax_nopriv_eiken_update_cart_qty', 'eiken_ajax_update_cart_qty');
function eiken_ajax_update_cart_qty() {
    $key = sanitize_text_field($_POST['cart_key'] ?? '');
    $qty = max(0, intval($_POST['quantity'] ?? 0));
    if ($key && function_exists('WC')) {
        WC()->cart->set_quantity($key, $qty, true);
    }
    wp_send_json_success(eiken_build_cart_payload());
}

// Mostrar la talla elegida en el Carrito y Checkout de WooCommerce
add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {
    if (!empty($cart_item['eiken_size'])) {
        $item_data[] = [
            'key' => 'Talla / Formato',
            'value' => wc_clean($cart_item['eiken_size']),
        ];
    }
    return $item_data;
}, 10, 2);

add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    if (!empty($values['eiken_size'])) {
        $item->add_meta_data('Talla / Formato', $values['eiken_size'], true);
    }
}, 10, 4);

// Guardar Cotización vía AJAX
add_action('wp_ajax_eiken_submit_quote', 'eiken_ajax_submit_quote');
add_action('wp_ajax_nopriv_eiken_submit_quote', 'eiken_ajax_submit_quote');
function eiken_ajax_submit_quote() {
    $name = sanitize_text_field($_POST['clientName'] ?? '');
    $email = sanitize_email($_POST['clientEmail'] ?? '');
    $phone = sanitize_text_field($_POST['clientPhone'] ?? '');
    $company = sanitize_text_field($_POST['company'] ?? '');
    $service = sanitize_text_field($_POST['serviceName'] ?? 'Proyecto Personalizado');
    $description = sanitize_textarea_field($_POST['description'] ?? '');

    if (!$name || !$email || !$description) {
        wp_send_json_error(['message' => 'Por favor completa tu nombre, correo y descripción del proyecto.']);
    }

    $post_id = wp_insert_post([
        'post_type' => 'eiken_quote',
        'post_title' => $name . ($company ? " ($company)" : '') . ' — ' . $service,
        'post_content' => $description,
        'post_status' => 'publish',
    ]);

    if ($post_id) {
        update_post_meta($post_id, '_eiken_email', $email);
        update_post_meta($post_id, '_eiken_phone', $phone);
        update_post_meta($post_id, '_eiken_company', $company);
        update_post_meta($post_id, '_eiken_service', $service);
        update_post_meta($post_id, '_eiken_status', 'Pendiente');
    }

    wp_send_json_success(['message' => '¡Cotización enviada! Quedó registrada en el panel de administración de WordPress.']);
}

/**
 * 5. Catálogo de Servicios y Portafolio de Eiken Design
 */
function eiken_get_services_data() {
    return [
        [
            'id' => 1,
            'name' => 'Wrap Vehicular Completo',
            'category' => 'Vehicular',
            'description' => 'Diseño e instalación de wrap completo para vehículos comerciales y particulares con vinilos fundidos de alta gama.',
            'price' => 450000,
            'popular' => true,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773959147/eiken_uploads/servicio-1773948347488.jpg',
            'features' => ['Vinilo Avery Dennison / 3M', 'Instalación certificada libre de burbujas'],
        ],
        [
            'id' => 2,
            'name' => 'Gráfica de Competición',
            'category' => 'Racing',
            'description' => 'Diseño especializado y rotulado de alto impacto para autos de competición, pista y rally.',
            'price' => 850000,
            'popular' => true,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958880/eiken_uploads/servicio-1773948079551.jpg',
            'features' => ['Laminado de alta resistencia al roce', 'Diseño 3D previo a medida'],
        ],
        [
            'id' => 4,
            'name' => 'Rotulado de Flota',
            'category' => 'Vehicular',
            'description' => 'Diseño y aplicación estandarizada para flotas empresariales, furgones y camiones de reparto.',
            'price' => 380000,
            'popular' => false,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773959121/eiken_uploads/servicio-1773948321211.jpg',
            'features' => ['Cumplimiento de manual corporativo', 'Alta durabilidad a la intemperie'],
        ],
        [
            'id' => 5,
            'name' => 'Gráfica Rally Nacional',
            'category' => 'Racing',
            'description' => 'Desarrollo gráfico integral para equipos de rally nacional e internacional bajo normativa oficial.',
            'price' => 720000,
            'popular' => false,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958893/eiken_uploads/servicio-1773948092515.jpg',
            'features' => ['Kit de repuestos rápidos por carrera', 'Colores flúor y metalizados'],
        ],
        [
            'id' => 3,
            'name' => 'Identidad Corporativa',
            'category' => 'Corporativo',
            'description' => 'Diseño completo de identidad visual empresarial, letreros publicitarios y gráfica para puntos de venta.',
            'price' => 250000,
            'popular' => false,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958990/eiken_uploads/servicio-1773948190253.jpg',
            'features' => ['Manual de marca y vectorización', 'Archivos listos para impresión'],
        ],
        [
            'id' => 7,
            'name' => 'Cascos de Competición',
            'category' => 'Racing',
            'description' => 'Gráfica personalizada de precisión para cascos de automovilismo, karting y mountain bike.',
            'price' => 95000,
            'popular' => false,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958839/eiken_uploads/servicio-1773948038991.jpg',
            'features' => ['Moldeado térmico sin pliegues', 'Acabado brillante o mate'],
        ],
        [
            'id' => 8,
            'name' => 'Instalación Premium',
            'category' => 'Vehicular',
            'description' => 'Servicio técnico garantizado de aplicación de vinilos, láminas de protección y gráfica vehicular.',
            'price' => 150000,
            'popular' => false,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773959018/eiken_uploads/servicio-1773948217895.jpg',
            'features' => ['Taller climatizado en Los Ángeles', 'Garantía de instalación'],
        ],
        [
            'id' => 6,
            'name' => 'Estampado Textil Corporativo',
            'category' => 'Textil',
            'description' => 'Estampados corporativos, indumentaria de equipos de competición y ropa personalizada.',
            'price' => 45000,
            'popular' => false,
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958852/eiken_uploads/servicio-1773948051745.jpg',
            'features' => ['Tecnología DTF y vinilo textil', 'Alta resistencia a lavados'],
        ],
    ];
}

function eiken_get_projects_data() {
    return [
        [
            'id' => 2,
            'title' => 'Gráfica Rally Mobil — Team Chile',
            'category' => 'Racing',
            'division' => 'Racing Design',
            'description' => 'Diseño especializado e instalación integral para 3 autos de competición del campeonato Rally Mobil.',
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958701/eiken_uploads/proyecto-1773947901392.jpg',
        ],
        [
            'id' => 5,
            'title' => 'Flota Express — 40 Vehículos de Reparto',
            'category' => 'Vehicular',
            'division' => 'Truck Design',
            'description' => 'Wrap corporativo completo y rotulación reflectante de seguridad para flota logística.',
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958804/eiken_uploads/proyecto-1773948004203.jpg',
        ],
        [
            'id' => 3,
            'title' => 'Identidad Visual & Aplicaciones Vehiculares',
            'category' => 'Corporativo',
            'division' => 'Eiken Design',
            'description' => 'Desarrollo completo de identidad gráfica corporativa y estandarización de flota.',
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958735/eiken_uploads/proyecto-1773947935415.jpg',
        ],
        [
            'id' => 4,
            'title' => 'Equipamiento Gráfico Carros de Emergencia',
            'category' => 'Vehicular',
            'division' => 'Truck Design',
            'description' => 'Diseño e instalación de gráficas de alta visibilidad y material reflectante normado.',
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958748/eiken_uploads/proyecto-1773947948301.jpg',
        ],
        [
            'id' => 1,
            'title' => 'Wrap Completo Flota de Transporte Pesado',
            'category' => 'Vehicular',
            'division' => 'Truck Design',
            'description' => 'Rotulación integral de alta durabilidad para 25 camiones de carga interurbana.',
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958650/eiken_uploads/proyecto-1773947849508.jpg',
        ],
        [
            'id' => 7,
            'title' => 'Indumentaria Personalizada Motorsport',
            'category' => 'Textil',
            'division' => 'Eiken Design',
            'description' => 'Línea de poleras y polerones técnicos estampados para equipo de asistencia.',
            'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958820/eiken_uploads/proyecto-1773948020417.jpg',
        ],
    ];
}
