<?php
/**
 * Aprovisionamiento completo de Eiken Design en WordPress + WooCommerce + Transbank Webpay Plus.
 */

if (!function_exists('wc_get_product')) {
    echo "WooCommerce aún no está activo.\n";
    return;
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

// 1. Configuración general y moneda CLP
update_option('blogname', 'Eiken Design');
update_option('blogdescription', 'Diseño Publicitario, Gráfica Vehicular & Tienda Oficial');
update_option('woocommerce_currency', 'CLP');
update_option('woocommerce_price_num_decimals', 0);
update_option('woocommerce_thousand_sep', '.');
update_option('woocommerce_decimal_sep', ',');
update_option('woocommerce_default_country', 'CL:BI');
update_option('woocommerce_store_address', 'Calle 2 Sur 106');
update_option('woocommerce_store_city', 'Los Ángeles');
update_option('woocommerce_store_postcode', '4440000');
update_option('woocommerce_enable_myaccount_registration', 'yes');
update_option('woocommerce_enable_checkout_login_reminder', 'no');

// 2. Habilitar pasarela oficial Transbank Webpay Plus REST en modo Integración
update_option('woocommerce_transbank_webpay_plus_rest_settings', [
    'enabled' => 'yes',
    'title' => 'Webpay Plus (Tarjetas de Crédito y Redcompra)',
    'description' => 'Paga de forma 100% segura con tarjetas de Débito (Redcompra), Crédito o Prepago a través de Transbank Webpay Plus.',
    'webpay_rest_environment' => 'TEST',
    'webpay_rest_commerce_code' => '',
    'webpay_rest_api_key' => '',
    'webpay_rest_after_payment_order_status' => 'processing',
]);
update_option('woocommerce_bacs_settings', ['enabled' => 'no']);
update_option('woocommerce_cheque_settings', ['enabled' => 'no']);
update_option('woocommerce_cod_settings', [
    'enabled' => 'yes',
    'title' => 'Pago presencial al retirar en Taller (Los Ángeles)',
    'description' => 'Paga directamente en nuestro taller ubicado en Calle 2 Sur 106, Los Ángeles.',
    'instructions' => 'Te esperamos en Calle 2 Sur 106, Los Ángeles (Lun-Vie 09:00 a 18:00).',
]);

// 3. Crear páginas clásicas de WooCommerce (Cart y Checkout con shortcodes limpios para soporte total con Webpay)
if (class_exists('WC_Install')) {
    WC_Install::create_pages();
}

$cart_page_id = wc_get_page_id('cart');
if ($cart_page_id > 0) {
    wp_update_post([
        'ID' => $cart_page_id,
        'post_title' => 'Carrito de Compras',
        'post_content' => '[woocommerce_cart]',
    ]);
}

$checkout_page_id = wc_get_page_id('checkout');
if ($checkout_page_id > 0) {
    wp_update_post([
        'ID' => $checkout_page_id,
        'post_title' => 'Finalizar Compra',
        'post_content' => '[woocommerce_checkout]',
    ]);
}

$shop_page_id = wc_get_page_id('shop');
if ($shop_page_id > 0) {
    wp_update_post([
        'ID' => $shop_page_id,
        'post_title' => 'Tienda Online',
    ]);
}

// Crear página de Inicio para que "/" muestre la Landing completa y "/?post_type=product" (o página Tienda) muestre la tienda
$home_page = get_page_by_path('inicio-eiken');
if (!$home_page) {
    $home_id = wp_insert_post([
        'post_title' => 'Inicio',
        'post_name' => 'inicio-eiken',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '',
    ]);
} else {
    $home_id = $home_page->ID;
}

update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);

// 4. Crear categorías de Eiken Design
$categories = [
    'Racing' => 'Gráfica de competición, stickers de alto rendimiento e indumentaria motorsport.',
    'Vehicular' => 'Rotulado vehicular, accesorios y personalización automotriz.',
    'Textil' => 'Poleras, polerones e indumentaria corporativa y personalizada.',
];
$cat_ids = [];
foreach ($categories as $cat_name => $cat_desc) {
    $term = term_exists($cat_name, 'product_cat');
    if (!$term) {
        $term = wp_insert_term($cat_name, 'product_cat', ['description' => $cat_desc]);
    }
    if (!is_wp_error($term)) {
        $cat_ids[$cat_name] = is_array($term) ? $term['term_id'] : $term;
    }
}

// 5. Cargar productos reales de Eiken Design con imágenes reales
$products_data = [
    [
        'name' => 'Calcomanía Monster Energy',
        'description' => 'Sticker multiuso impreso en vinilo de alta adherencia con laminado protector UV y resistencia al agua y lavado a presión.',
        'price' => 15000,
        'stock' => 49,
        'category' => 'Racing',
        'sku' => 'EIK-STK-004',
        'sizes' => ['Estándar (30x20 cm)', 'Pack 3 Unidades'],
        'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773959186/eiken_uploads/producto-1773948386475.webp',
    ],
    [
        'name' => 'Polera Estampada Gaming',
        'description' => 'Polera personalizada 100% algodón premium de alto gramaje con estampado DTF textil de alta definición y durabilidad.',
        'price' => 12000,
        'stock' => 25,
        'category' => 'Vehicular',
        'sku' => 'EIK-POL-001',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958852/eiken_uploads/servicio-1773948051745.jpg',
    ],
    [
        'name' => 'Polera Estampada Honda',
        'description' => 'Personalización de polera deportiva con logo oficial Honda Racing en vinilo textil de alta resistencia.',
        'price' => 20000,
        'stock' => 49,
        'category' => 'Textil',
        'sku' => 'EIK-POL-005',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773959347/eiken_uploads/producto-1773948546920.webp',
    ],
    [
        'name' => 'Polerón Personalizado F1',
        'description' => 'Polerón canguro personalizado estilo Fórmula 1 en franela cardada térmica con estampados en pecho, espalda y mangas.',
        'price' => 35000,
        'stock' => 49,
        'category' => 'Racing',
        'sku' => 'EIK-HDY-006',
        'sizes' => ['S', 'M', 'L', 'XL'],
        'image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773959234/eiken_uploads/producto-1773948434667.webp',
    ],
];

// Limpiar duplicados antiguos si existen con minúscula
$all_products = get_posts(['post_type' => 'product', 'numberposts' => -1, 'post_status' => 'any']);
$existing_by_sku = [];
foreach ($all_products as $p) {
    $sku = get_post_meta($p->ID, '_sku', true);
    if (!$sku) {
        wp_delete_post($p->ID, true);
    } else {
        $existing_by_sku[$sku] = $p->ID;
    }
}

foreach ($products_data as $item) {
    if (isset($existing_by_sku[$item['sku']])) {
        $product_id = $existing_by_sku[$item['sku']];
        $product = wc_get_product($product_id);
    } else {
        $product = new WC_Product_Simple();
    }

    $product->set_name($item['name']);
    $product->set_sku($item['sku']);
    $product->set_description($item['description']);
    $product->set_short_description($item['description']);
    $product->set_regular_price((string) $item['price']);
    $product->set_manage_stock(true);
    $product->set_stock_quantity($item['stock']);
    $product->set_stock_status('instock');
    $product->set_status('publish');

    if (isset($cat_ids[$item['category']])) {
        $product->set_category_ids([$cat_ids[$item['category']]]);
    }

    // Guardar atributo de Talla / Formato para mostrarlo en la tienda y ficha
    if (!empty($item['sizes'])) {
        $attr = new WC_Product_Attribute();
        $attr->set_name('Talla / Formato');
        $attr->set_options($item['sizes']);
        $attr->set_position(0);
        $attr->set_visible(true);
        $attr->set_variation(false);
        $product->set_attributes([$attr]);
    }

    $product_id = $product->save();

    if (!empty($item['image']) && !has_post_thumbnail($product_id)) {
        $attach_id = media_sideload_image($item['image'], $product_id, $item['name'], 'id');
        if (!is_wp_error($attach_id)) {
            set_post_thumbnail($product_id, $attach_id);
        }
    }
}

// 6. Sembrar Cotizaciones Simuladas en WordPress (CPT: eiken_quote)
$existing_quotes = get_posts([
    'post_type' => 'eiken_quote',
    'numberposts' => -1,
    'post_status' => 'any',
]);
foreach ($existing_quotes as $old_q) {
    wp_delete_post($old_q->ID, true);
}

$simulated_quotes = [
    [
        'client_name' => 'Matías Valdés Rojas',
        'company' => 'Transportes y Logística Biobío SpA',
        'email' => 'mvaldes@logistica-biobio.cl',
        'phone' => '+56 9 8412 3390',
        'service' => 'Rotulado de Flota (4 Furgones Mercedes-Benz Sprinter)',
        'category' => 'Vehicular',
        'status' => 'Cotizada',
        'amount' => 1520000,
        'delivery_date' => '2026-11-24',
        'ref_image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773959121/eiken_uploads/servicio-1773948321211.jpg',
        'description' => "Necesitamos rotular 4 furgones Mercedes-Benz Sprinter L2H2 blancos con nuestra nueva imagen corporativa.\nRequerimos laterales completos, puertas traseras con cinta reflectante normada DOT-C2 y logos en capó.\nDisponibilidad para ingresar de a 2 furgones por semana en su taller de Los Ángeles.",
        'reply_notes' => "✅ PROPUESTA TÉCNICA EIKEN DESIGN:\n• Material: Vinilo fundido polimérico Avery Dennison MPI 1105 + Laminado UV DOL 1060 brillante.\n• Seguridad vial: Cinta reflectante prismática 3M Serie 983 normativa Ministerio de Transportes.\n• Desglose: $380.000 IVA incl. por vehículo x 4 unidades = $1.520.000 CLP total.\n• Tiempo de ejecución: 2 días hábiles por par de furgones en nuestro taller climatizado (Calle 2 Sur 106, Los Ángeles).\n• Garantía: 3 años contra decoloración y desprendimiento.",
    ],
    [
        'client_name' => 'Cristóbal Soto Aravena',
        'company' => 'Team Soto Motorsport — Rally Mobil',
        'email' => 'csoto@sotomotorsport.cl',
        'phone' => '+56 9 7621 9045',
        'service' => 'Gráfica de Competición Integral + Kit Repuestos Rally',
        'category' => 'Racing',
        'status' => 'Aprobado',
        'amount' => 890000,
        'delivery_date' => '2026-11-18',
        'ref_image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958701/eiken_uploads/proyecto-1773947901392.jpg',
        'description' => "Requerimos diseño 3D e instalación de livery completo para Peugeot 208 Rally4 antes de la fecha del campeonato en Biobío.\nColores corporativos negro carbono, rojo flúor y detalles en plata metalizado, incluyendo portanúmeros oficiales FIA y nombres de piloto/navegante.",
        'reply_notes' => "✅ PROPUESTA APROBADA Y EN PRODUCCIÓN:\n• Diseño y renderizado 3D a escala real aprobado por el equipo.\n• Vinilo de alto impacto Avery Dennison Supreme Wrapping Film + acentos flúor de alta visibilidad.\n• Incluye 1 kit extra de parachoques delantero y faldones laterales pre-cortados para asistencia rápida en parque cerrado.",
    ],
    [
        'client_name' => 'Carolina Méndez Fuentes',
        'company' => 'Constructora Nahuelbuta Ltda.',
        'email' => 'cmendez@constructoranahuelbuta.cl',
        'phone' => '+56 9 9120 4481',
        'service' => 'Wrap Vehicular Completo Camionetas + Identidad Corporativa',
        'category' => 'Corporativo',
        'status' => 'En Revisión',
        'amount' => 1350000,
        'delivery_date' => '2026-11-28',
        'ref_image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958735/eiken_uploads/proyecto-1773947935415.jpg',
        'description' => "Solicito cotización para 3 camionetas Toyota Hilux doble cabina 2025.\nBuscamos proteger la pintura original con vinilo gris mate militar más gráficas laterales en naranjo reflectante e identificación numérica para faena forestal.",
        'reply_notes' => "📋 EVALUACIÓN TÉCNICA PRELIMINAR:\n• Se contempla vinilo fundido acabado Matte Charcoal Metallic con protección contra gravilla en zonas bajas.\n• Estamos validando el metraje exacto de reflectante grado ingeniería para enviar confirmación final.",
    ],
    [
        'client_name' => 'Diego Riquelme Lagos',
        'company' => 'Club Karting Los Ángeles',
        'email' => 'diego.riquelme@kartingla.cl',
        'phone' => '+56 9 6334 1180',
        'service' => 'Indumentaria Personalizada Motorsport (15 Polerones + 15 Poleras)',
        'category' => 'Textil',
        'status' => 'Pendiente',
        'amount' => 0,
        'delivery_date' => '2026-12-02',
        'ref_image' => 'https://res.cloudinary.com/dogyutkjx/image/upload/v1773958820/eiken_uploads/proyecto-1773948020417.jpg',
        'description' => "Hola equipo Eiken, queremos cotizar 15 packs de polerón canguro + polera técnica para nuestros pilotos y mecánicos.\nCada prenda lleva el logo del club en el pecho, auspiciadores en mangas y nombre personalizado en la espalda.",
        'reply_notes' => '',
    ],
];

$first_quote_id = 0;
foreach ($simulated_quotes as $q) {
    $qid = wp_insert_post([
        'post_type' => 'eiken_quote',
        'post_title' => $q['client_name'] . ' — ' . $q['company'],
        'post_content' => $q['description'],
        'post_status' => 'publish',
    ]);
    if ($qid) {
        if ($first_quote_id === 0) {
            $first_quote_id = $qid;
        }
        update_post_meta($qid, '_eiken_client_name', $q['client_name']);
        update_post_meta($qid, '_eiken_company', $q['company']);
        update_post_meta($qid, '_eiken_email', $q['email']);
        update_post_meta($qid, '_eiken_phone', $q['phone']);
        update_post_meta($qid, '_eiken_service', $q['service']);
        update_post_meta($qid, '_eiken_category', $q['category']);
        update_post_meta($qid, '_eiken_status', $q['status']);
        update_post_meta($qid, '_eiken_quoted_amount', $q['amount']);
        update_post_meta($qid, '_eiken_delivery_date', $q['delivery_date']);
        update_post_meta($qid, '_eiken_ref_image', $q['ref_image']);
        update_post_meta($qid, '_eiken_description', $q['description']);
        update_post_meta($qid, '_eiken_reply_notes', $q['reply_notes']);
    }
}

echo "=> Tienda WooCommerce + Webpay Plus + 4 Cotizaciones Simuladas listas (ID principal: {$first_quote_id}).\n";
