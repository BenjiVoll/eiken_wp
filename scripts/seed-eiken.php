<?php
/**
 * Script de aprovisionamiento automático para Eiken Design en WooCommerce.
 * Configura moneda CLP, páginas de tienda y carga los productos reales de Eiken Design.
 */

if (!function_exists('wc_get_product')) {
    echo "WooCommerce aún no está activo.\n";
    return;
}

// 1. Configurar moneda CLP (Peso Chileno sin decimales)
update_option('woocommerce_currency', 'CLP');
update_option('woocommerce_price_num_decimals', 0);
update_option('woocommerce_thousand_sep', '.');
update_option('woocommerce_decimal_sep', ',');
update_option('woocommerce_default_country', 'CL');
update_option('blogname', 'Eiken Design Store');
update_option('blogdescription', 'Diseño Publicitario, Gráfica Vehicular y Tienda Oficial');

// 2. Asegurar páginas de WooCommerce y fijar Tienda como portada
if (class_exists('WC_Install')) {
    WC_Install::create_pages();
}
$shop_page_id = wc_get_page_id('shop');
if ($shop_page_id > 0) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $shop_page_id);
}

// 3. Crear categorías de Eiken Design
$categories = ['Racing', 'Vehicular', 'Textil'];
$cat_ids = [];
foreach ($categories as $cat_name) {
    $term = term_exists($cat_name, 'product_cat');
    if (!$term) {
        $term = wp_insert_term($cat_name, 'product_cat');
    }
    if (!is_wp_error($term)) {
        $cat_ids[$cat_name] = is_array($term) ? $term['term_id'] : $term;
    }
}

// 4. Productos reales de Eiken Design
$products_data = [
    [
        'name' => 'Calcomanía Monster Energy',
        'description' => 'Sticker multiuso de alta adherencia y resistencia UV.',
        'price' => 15000,
        'stock' => 49,
        'category' => 'Racing',
    ],
    [
        'name' => 'Polera estampada gaming',
        'description' => 'Polera personalizada en algodón premium con estampado de alta durabilidad.',
        'price' => 12000,
        'stock' => 25,
        'category' => 'Vehicular',
    ],
    [
        'name' => 'Polera estampada Honda',
        'description' => 'Personalización de polera con logo de Honda Motorsport.',
        'price' => 20000,
        'stock' => 49,
        'category' => 'Textil',
    ],
    [
        'name' => 'Polerón Personalizado F1',
        'description' => 'Polerón personalizado estilo Fórmula 1 con acabado térmico.',
        'price' => 35000,
        'stock' => 49,
        'category' => 'Racing',
    ],
];

foreach ($products_data as $item) {
    $existing = get_page_by_title($item['name'], OBJECT, 'product');
    if ($existing) {
        continue;
    }

    $product = new WC_Product_Simple();
    $product->set_name($item['name']);
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

    $product->save();
}

echo "=> Catálogo de Eiken Design configurado en WooCommerce exitosamente.\n";
