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
    wp_enqueue_style('eiken-store-style', get_stylesheet_uri(), [], '2.1.0');
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
 * 3. Custom Post Type: Cotizaciones Eiken (Gestión completa en WP-Admin + Vista de Propuesta Cliente)
 */
add_action('init', function () {
    register_post_type('eiken_quote', [
        'labels' => [
            'name' => 'Cotizaciones Eiken',
            'singular_name' => 'Cotización',
            'menu_name' => 'Cotizaciones Eiken',
            'all_items' => 'Todas las Cotizaciones',
            'add_new' => 'Nueva Cotización',
            'add_new_item' => 'Crear Nueva Cotización',
            'edit_item' => 'Gestionar Cotización',
            'search_items' => 'Buscar Cotizaciones',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-clipboard',
        'menu_position' => 26,
        'supports' => ['title'],
    ]);
});

// Cabecera visual con KPIs arriba del listado de Cotizaciones en WP-Admin
add_action('all_admin_notices', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'eiken_quote' || $screen->base !== 'edit') {
        return;
    }
    $quotes = get_posts(['post_type' => 'eiken_quote', 'numberposts' => -1, 'post_status' => 'publish']);
    $total = count($quotes);
    $pending = 0;
    $approved = 0;
    $total_clp = 0;
    foreach ($quotes as $q) {
        $st = get_post_meta($q->ID, '_eiken_status', true) ?: 'Pendiente';
        $amt = (int) get_post_meta($q->ID, '_eiken_quoted_amount', true);
        if ($st === 'Pendiente' || $st === 'En Revisión') $pending++;
        if ($st === 'Aprobado') $approved++;
        $total_clp += $amt;
    }
    ?>
    <div style="margin: 20px 20px 10px 0; background: linear-gradient(135deg, #090d16 0%, #1e293b 100%); color: #fff; border-radius: 16px; padding: 24px 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); border: 1px solid #334155;">
      <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px; margin-bottom:20px;">
        <div>
          <span style="display:inline-block; background:rgba(255,61,0,0.2); color:#fdba74; border:1px solid rgba(255,61,0,0.4); padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; text-transform:uppercase;">Módulo de Cotizaciones a Medida</span>
          <h2 style="color:#fff; margin:6px 0 0; font-size:22px; font-weight:800;">Gestión de Cotizaciones — Eiken Design</h2>
        </div>
        <a href="<?php echo esc_url(home_url('/#servicios')); ?>" target="_blank" style="background:linear-gradient(135deg,#ea580c,#dc2626); color:#fff; text-decoration:none; padding:10px 18px; border-radius:10px; font-weight:700; font-size:13px;">
          + Simular Nueva Cotización desde la Web ↗
        </a>
      </div>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px;">
        <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); padding:14px 18px; border-radius:12px;">
          <div style="font-size:11px; color:#94a3b8; text-transform:uppercase; font-weight:700;">Total Solicitudes</div>
          <div style="font-size:26px; font-weight:900; color:#fff; margin-top:4px;"><?php echo intval($total); ?></div>
        </div>
        <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); padding:14px 18px; border-radius:12px;">
          <div style="font-size:11px; color:#fdba74; text-transform:uppercase; font-weight:700;">Por Atender / En Revisión</div>
          <div style="font-size:26px; font-weight:900; color:#fb923c; margin-top:4px;"><?php echo intval($pending); ?></div>
        </div>
        <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); padding:14px 18px; border-radius:12px;">
          <div style="font-size:11px; color:#6ee7b7; text-transform:uppercase; font-weight:700;">Cotizaciones Aprobadas</div>
          <div style="font-size:26px; font-weight:900; color:#34d399; margin-top:4px;"><?php echo intval($approved); ?></div>
        </div>
        <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); padding:14px 18px; border-radius:12px;">
          <div style="font-size:11px; color:#94a3b8; text-transform:uppercase; font-weight:700;">Volumen Cotizado (CLP)</div>
          <div style="font-size:26px; font-weight:900; color:#fff; margin-top:4px;">$<?php echo number_format($total_clp, 0, ',', '.'); ?></div>
        </div>
      </div>
    </div>
    <?php
});

add_filter('manage_eiken_quote_posts_columns', function ($columns) {
    return [
        'cb' => $columns['cb'],
        'quote_code' => 'Código',
        'title' => 'Cliente / Empresa',
        'service' => 'Servicio & Categoría',
        'contact' => 'Contacto Directo',
        'amount' => 'Monto Cotizado',
        'status' => 'Estado',
        'actions_col' => 'Acciones / Vista Cliente',
        'date' => 'Fecha',
    ];
});

add_action('manage_eiken_quote_posts_custom_column', function ($column, $post_id) {
    if ($column === 'quote_code') {
        echo '<strong style="color:#ea580c;font-family:monospace;font-size:13px;">#COT-' . str_pad($post_id, 3, '0', STR_PAD_LEFT) . '</strong>';
    } elseif ($column === 'service') {
        $srv = get_post_meta($post_id, '_eiken_service', true) ?: 'Proyecto Personalizado';
        $cat = get_post_meta($post_id, '_eiken_category', true) ?: 'Vehicular';
        echo '<strong>' . esc_html($srv) . '</strong><br><span style="color:#64748b;font-size:11px;">Categoría: ' . esc_html($cat) . '</span>';
    } elseif ($column === 'contact') {
        $email = get_post_meta($post_id, '_eiken_email', true);
        $phone = get_post_meta($post_id, '_eiken_phone', true);
        echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a><br><span style="color:#475569;font-size:12px;">📞 ' . esc_html($phone) . '</span>';
    } elseif ($column === 'amount') {
        $amt = (int) get_post_meta($post_id, '_eiken_quoted_amount', true);
        if ($amt > 0) {
            echo '<strong style="font-size:14px;color:#0f172a;">$' . number_format($amt, 0, ',', '.') . '</strong>';
        } else {
            echo '<span style="color:#94a3b8;font-style:italic;">Por cotizar</span>';
        }
    } elseif ($column === 'status') {
        $st = get_post_meta($post_id, '_eiken_status', true) ?: 'Pendiente';
        $colors = [
            'Pendiente' => 'background:#fef3c7;color:#b45309;border:1px solid #fde68a;',
            'En Revisión' => 'background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;',
            'Cotizada' => 'background:#ffedd5;color:#c2410c;border:1px solid #fed7aa;',
            'Aprobado' => 'background:#d1fae5;color:#047857;border:1px solid #a7f3d0;',
            'Rechazado' => 'background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;',
        ];
        $style = $colors[$st] ?? $colors['Pendiente'];
        echo '<span style="' . $style . 'padding:4px 10px;border-radius:99px;font-weight:700;font-size:11px;display:inline-block;">' . esc_html($st) . '</span>';
    } elseif ($column === 'actions_col') {
        $edit_url = get_edit_post_link($post_id);
        $preview_url = home_url('/?eiken_quote_view=' . $post_id);
        echo '<div style="display:flex;gap:6px;flex-wrap:wrap;">';
        echo '<a href="' . esc_url($edit_url) . '" class="button button-small" style="background:#0f172a;color:#fff;border:none;border-radius:6px;">Gestionar / Cotizar</a>';
        echo '<a href="' . esc_url($preview_url) . '" target="_blank" class="button button-small" style="background:#ea580c;color:#fff;border:none;border-radius:6px;">👁️ Ver como Cliente ↗</a>';
        echo '</div>';
    }
}, 10, 2);

// Meta Box completo en la ficha de edición de la Cotización
add_action('add_meta_boxes', function () {
    add_meta_box(
        'eiken_quote_details_box',
        'Ficha Técnica de Cotización & Propuesta Comercial — Eiken Design',
        'eiken_render_quote_meta_box',
        'eiken_quote',
        'normal',
        'high'
    );
});

function eiken_render_quote_meta_box($post) {
    $client_name = get_post_meta($post->ID, '_eiken_client_name', true) ?: $post->post_title;
    $company = get_post_meta($post->ID, '_eiken_company', true) ?: 'Particular';
    $email = get_post_meta($post->ID, '_eiken_email', true);
    $phone = get_post_meta($post->ID, '_eiken_phone', true);
    $service = get_post_meta($post->ID, '_eiken_service', true) ?: 'Wrap Vehicular Completo';
    $category = get_post_meta($post->ID, '_eiken_category', true) ?: 'Vehicular';
    $delivery_date = get_post_meta($post->ID, '_eiken_delivery_date', true) ?: '2026-11-20';
    $status = get_post_meta($post->ID, '_eiken_status', true) ?: 'Pendiente';
    $amount = get_post_meta($post->ID, '_eiken_quoted_amount', true) ?: '';
    $reply_notes = get_post_meta($post->ID, '_eiken_reply_notes', true) ?: '';
    $ref_image = get_post_meta($post->ID, '_eiken_ref_image', true) ?: '';
    $description = $post->post_content ?: get_post_meta($post->ID, '_eiken_description', true);
    $preview_url = home_url('/?eiken_quote_view=' . $post->ID);
    wp_nonce_field('eiken_save_quote_meta', 'eiken_quote_nonce');
    ?>
    <div style="font-family: 'Inter', system-ui, sans-serif; padding: 10px 4px;">
      <!-- Barra superior con enlace a Vista Cliente -->
      <div style="background:#0f172a; color:#fff; padding:16px 20px; border-radius:12px; display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div>
          <span style="color:#fb923c; font-size:11px; font-weight:700; text-transform:uppercase;">Cotización #COT-<?php echo str_pad($post->ID, 3, '0', STR_PAD_LEFT); ?></span>
          <h3 style="color:#fff; margin:4px 0 0; font-size:17px;">Vista Previa de Propuesta Comercial para el Cliente</h3>
        </div>
        <a href="<?php echo esc_url($preview_url); ?>" target="_blank" style="background:linear-gradient(135deg,#ea580c,#dc2626); color:#fff; text-decoration:none; padding:10px 18px; border-radius:8px; font-weight:700; font-size:13px;">
          👁️ Abrir Propuesta como la ve el Cliente ↗
        </a>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
        <!-- Columna Izquierda: Datos del Cliente y Solicitud -->
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
          <h4 style="margin:0 0 14px; font-size:15px; color:#0f172a; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">👤 1. Datos del Cliente y Requerimiento</h4>

          <p style="margin:0 0 10px;">
            <label style="font-size:12px; font-weight:700; color:#475569; display:block;">Nombre del Cliente:</label>
            <input type="text" name="eiken_client_name" value="<?php echo esc_attr($client_name); ?>" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;" />
          </p>
          <p style="margin:0 0 10px;">
            <label style="font-size:12px; font-weight:700; color:#475569; display:block;">Empresa / Organización:</label>
            <input type="text" name="eiken_company" value="<?php echo esc_attr($company); ?>" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;" />
          </p>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <label style="font-size:12px; font-weight:700; color:#475569; display:block;">Correo Electrónico:</label>
              <input type="email" name="eiken_email" value="<?php echo esc_attr($email); ?>" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;" />
            </div>
            <div>
              <label style="font-size:12px; font-weight:700; color:#475569; display:block;">Teléfono / WhatsApp:</label>
              <input type="text" name="eiken_phone" value="<?php echo esc_attr($phone); ?>" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;" />
            </div>
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
            <div>
              <label style="font-size:12px; font-weight:700; color:#475569; display:block;">Servicio Solicitado:</label>
              <input type="text" name="eiken_service" value="<?php echo esc_attr($service); ?>" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;" />
            </div>
            <div>
              <label style="font-size:12px; font-weight:700; color:#475569; display:block;">Categoría / División:</label>
              <input type="text" name="eiken_category" value="<?php echo esc_attr($category); ?>" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;" />
            </div>
          </div>

          <p style="margin:0 0 10px;">
            <label style="font-size:12px; font-weight:700; color:#475569; display:block;">Descripción detallada del Cliente:</label>
            <textarea name="eiken_description" rows="4" style="width:100%; padding:8px; border-radius:8px; border:1px solid #cbd5e1;"><?php echo esc_textarea($description); ?></textarea>
          </p>

          <?php if ($ref_image) : ?>
            <div style="margin-top:12px;">
              <label style="font-size:12px; font-weight:700; color:#475569; display:block; margin-bottom:6px;">📸 Imagen de Referencia Adjunta:</label>
              <img src="<?php echo esc_url($ref_image); ?>" alt="Referencia" style="max-width:100%; height:160px; object-fit:cover; border-radius:10px; border:1px solid #cbd5e1;" />
            </div>
          <?php endif; ?>
        </div>

        <!-- Columna Derecha: Respuesta Comercial y Cotización de Eiken Design -->
        <div style="background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; padding:20px;">
          <h4 style="margin:0 0 14px; font-size:15px; color:#9a3412; border-bottom:1px solid #fed7aa; padding-bottom:8px;">💼 2. Respuesta Comercial &amp; Presupuesto Eiken</h4>

          <p style="margin:0 0 14px;">
            <label style="font-size:12px; font-weight:700; color:#9a3412; display:block; margin-bottom:4px;">Estado de la Cotización:</label>
            <select name="eiken_status" style="width:100%; padding:9px; border-radius:8px; border:1px solid #fdba74; font-weight:700;">
              <?php foreach (['Pendiente', 'En Revisión', 'Cotizada', 'Aprobado', 'Rechazado'] as $st) : ?>
                <option value="<?php echo esc_attr($st); ?>" <?php selected($status, $st); ?>><?php echo esc_html($st); ?></option>
              <?php endforeach; ?>
            </select>
          </p>

          <p style="margin:0 0 14px;">
            <label style="font-size:12px; font-weight:700; color:#9a3412; display:block; margin-bottom:4px;">Monto Cotizado en CLP ($):</label>
            <input type="number" name="eiken_quoted_amount" value="<?php echo esc_attr($amount); ?>" placeholder="Ej: 1700000" style="width:100%; padding:10px; border-radius:8px; border:1px solid #fdba74; font-size:16px; font-weight:800; color:#0f172a;" />
          </p>

          <p style="margin:0 0 14px;">
            <label style="font-size:12px; font-weight:700; color:#9a3412; display:block; margin-bottom:4px;">Fecha Estimada de Entrega:</label>
            <input type="date" name="eiken_delivery_date" value="<?php echo esc_attr($delivery_date); ?>" style="width:100%; padding:8px; border-radius:8px; border:1px solid #fdba74;" />
          </p>

          <p style="margin:0 0 14px;">
            <label style="font-size:12px; font-weight:700; color:#9a3412; display:block; margin-bottom:4px;">Propuesta Técnica / Detalle de Materiales e Instalación:</label>
            <textarea name="eiken_reply_notes" rows="6" placeholder="Escribe aquí el desglose técnico de la propuesta (materiales Avery Dennison / 3M, plazos y garantía)..." style="width:100%; padding:10px; border-radius:8px; border:1px solid #fdba74;"><?php echo esc_textarea($reply_notes); ?></textarea>
          </p>

          <div style="background:#ffffff; border:1px dashed #fdba74; border-radius:10px; padding:12px; font-size:12px; color:#7c2d12;">
            💡 <strong>Tip:</strong> Al hacer clic en <strong>Actualizar</strong> (arriba a la derecha), los cambios se reflejan de inmediato en el enlace público de propuesta que ve el cliente.
          </div>
        </div>
      </div>
    </div>
    <?php
}

add_action('save_post_eiken_quote', function ($post_id) {
    if (!isset($_POST['eiken_quote_nonce']) || !wp_verify_nonce($_POST['eiken_quote_nonce'], 'eiken_save_quote_meta')) {
        return;
    }
    if (isset($_POST['eiken_client_name'])) update_post_meta($post_id, '_eiken_client_name', sanitize_text_field($_POST['eiken_client_name']));
    if (isset($_POST['eiken_company'])) update_post_meta($post_id, '_eiken_company', sanitize_text_field($_POST['eiken_company']));
    if (isset($_POST['eiken_email'])) update_post_meta($post_id, '_eiken_email', sanitize_email($_POST['eiken_email']));
    if (isset($_POST['eiken_phone'])) update_post_meta($post_id, '_eiken_phone', sanitize_text_field($_POST['eiken_phone']));
    if (isset($_POST['eiken_service'])) update_post_meta($post_id, '_eiken_service', sanitize_text_field($_POST['eiken_service']));
    if (isset($_POST['eiken_category'])) update_post_meta($post_id, '_eiken_category', sanitize_text_field($_POST['eiken_category']));
    if (isset($_POST['eiken_status'])) update_post_meta($post_id, '_eiken_status', sanitize_text_field($_POST['eiken_status']));
    if (isset($_POST['eiken_quoted_amount'])) update_post_meta($post_id, '_eiken_quoted_amount', intval($_POST['eiken_quoted_amount']));
    if (isset($_POST['eiken_delivery_date'])) update_post_meta($post_id, '_eiken_delivery_date', sanitize_text_field($_POST['eiken_delivery_date']));
    if (isset($_POST['eiken_reply_notes'])) update_post_meta($post_id, '_eiken_reply_notes', sanitize_textarea_field($_POST['eiken_reply_notes']));
    if (isset($_POST['eiken_description'])) {
        update_post_meta($post_id, '_eiken_description', sanitize_textarea_field($_POST['eiken_description']));
    }
});

// Permitir al cliente Aceptar o Rechazar la cotización desde la Vista de Propuesta
add_action('wp_ajax_eiken_respond_quote', 'eiken_ajax_respond_quote');
add_action('wp_ajax_nopriv_eiken_respond_quote', 'eiken_ajax_respond_quote');
function eiken_ajax_respond_quote() {
    $quote_id = intval($_POST['quote_id'] ?? 0);
    $decision = sanitize_text_field($_POST['decision'] ?? '');
    if ($quote_id <= 0 || !in_array($decision, ['Aprobado', 'Rechazado'], true)) {
        wp_send_json_error(['message' => 'Solicitud inválida']);
    }
    update_post_meta($quote_id, '_eiken_status', $decision);
    wp_send_json_success([
        'status' => $decision,
        'message' => $decision === 'Aprobado'
            ? '¡Has aprobado la cotización! Nuestro equipo técnico coordinará el ingreso a taller.'
            : 'Has rechazado la propuesta. Si deseas ajustes, contáctanos por WhatsApp.',
    ]);
}

// Vista interactiva de Cotización / Propuesta Comercial (?eiken_quote_view=ID o ?eiken_quote_view=latest)
add_action('template_redirect', function () {
    if (!isset($_GET['eiken_quote_view'])) {
        return;
    }
    $raw = sanitize_text_field($_GET['eiken_quote_view']);
    $all_quotes = get_posts([
        'post_type' => 'eiken_quote',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'ID',
        'order' => 'ASC',
    ]);

    if (empty($all_quotes)) {
        wp_redirect(home_url('/'));
        exit;
    }

    $quote = null;
    if (is_numeric($raw)) {
        $candidate = get_post(intval($raw));
        if ($candidate && $candidate->post_type === 'eiken_quote') {
            $quote = $candidate;
        }
    }
    if (!$quote) {
        $quote = $all_quotes[0];
    }

    $qid = $quote->ID;
    $code = '#COT-' . str_pad($qid, 3, '0', STR_PAD_LEFT);
    $client_name = get_post_meta($qid, '_eiken_client_name', true) ?: $quote->post_title;
    $company = get_post_meta($qid, '_eiken_company', true) ?: 'Particular';
    $email = get_post_meta($qid, '_eiken_email', true) ?: 'cliente@empresa.cl';
    $phone = get_post_meta($qid, '_eiken_phone', true) ?: '+56 9 5005 6961';
    $service = get_post_meta($qid, '_eiken_service', true) ?: 'Wrap Vehicular Completo';
    $category = get_post_meta($qid, '_eiken_category', true) ?: 'Vehicular';
    $status = get_post_meta($qid, '_eiken_status', true) ?: 'Cotizada';
    $amount = (int) get_post_meta($qid, '_eiken_quoted_amount', true);
    $delivery_date = get_post_meta($qid, '_eiken_delivery_date', true) ?: '2026-11-25';
    $reply_notes = get_post_meta($qid, '_eiken_reply_notes', true) ?: '';
    $ref_image = get_post_meta($qid, '_eiken_ref_image', true) ?: '';
    $description = $quote->post_content ?: get_post_meta($qid, '_eiken_description', true);

    $status_badges = [
        'Pendiente' => 'bg-amber-500/15 text-amber-300 border-amber-500/40',
        'En Revisión' => 'bg-sky-500/15 text-sky-300 border-sky-500/40',
        'Cotizada' => 'bg-orange-500/15 text-orange-300 border-orange-500/40',
        'Aprobado' => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/40',
        'Rechazado' => 'bg-red-500/15 text-red-300 border-red-500/40',
    ];
    $badge_class = $status_badges[$status] ?? $status_badges['Cotizada'];

    $neto = $amount > 0 ? round($amount / 1.19) : 0;
    $iva = $amount > 0 ? ($amount - $neto) : 0;

    get_header();
    ?>
    <section class="py-10 bg-slate-950 min-h-screen">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Selector Superior de Cotizaciones Simuladas + Acceso a WP-Admin -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 sm:p-5 mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-orange-400 block mb-1">
              Simulador de Cotizaciones en WordPress
            </span>
            <div class="flex flex-wrap items-center gap-2">
              <?php foreach ($all_quotes as $idx => $q_item) :
                  $q_st = get_post_meta($q_item->ID, '_eiken_status', true) ?: 'Pendiente';
                  $q_srv = get_post_meta($q_item->ID, '_eiken_service', true) ?: 'Proyecto';
                  $active = ($q_item->ID === $qid);
              ?>
                <a
                  href="<?php echo esc_url(home_url('/?eiken_quote_view=' . $q_item->ID)); ?>"
                  class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all border <?php echo $active ? 'bg-orange-600 text-white border-orange-500 shadow-lg shadow-orange-600/25' : 'bg-slate-950 text-slate-300 border-slate-800 hover:border-slate-700'; ?>"
                >
                  #COT-<?php echo str_pad($q_item->ID, 3, '0', STR_PAD_LEFT); ?> · <?php echo esc_html($q_srv); ?> (<?php echo esc_html($q_st); ?>)
                </a>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-2.5">
            <a
              href="<?php echo esc_url(admin_url('post.php?post=' . $qid . '&action=edit')); ?>"
              class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 px-4 py-2.5 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5"
            >
              <span>✏️ Editar esta Cotización en WP-Admin</span>
            </a>
            <a
              href="<?php echo esc_url(admin_url('edit.php?post_type=eiken_quote')); ?>"
              class="bg-slate-950 hover:bg-slate-800 text-slate-300 border border-slate-800 px-4 py-2.5 rounded-xl text-xs font-bold transition-colors"
            >
              📋 Ver Tabla Completa en Admin
            </a>
          </div>
        </div>

        <!-- Documento de Cotización / Propuesta Comercial -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
          <!-- Cabecera de la Cotización -->
          <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border-b border-slate-800 p-6 sm:p-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
              <div class="flex items-start gap-4">
                <img
                  src="<?php echo esc_url(get_template_directory_uri() . '/logo.png'); ?>"
                  alt="Eiken Design"
                  class="w-16 h-16 rounded-2xl object-contain bg-slate-950 p-1.5 border border-slate-800"
                />
                <div>
                  <div class="flex flex-wrap items-center gap-2.5 mb-1">
                    <span class="font-mono text-sm font-black text-orange-400 bg-orange-500/10 border border-orange-500/30 px-3 py-0.5 rounded-lg">
                      <?php echo esc_html($code); ?>
                    </span>
                    <span id="eiken-quote-status-badge" class="px-3 py-0.5 rounded-full text-xs font-bold border <?php echo esc_attr($badge_class); ?>">
                      Estado: <?php echo esc_html($status); ?>
                    </span>
                    <span class="text-xs text-slate-400">
                      Categoría: <strong class="text-slate-200"><?php echo esc_html($category); ?></strong>
                    </span>
                  </div>
                  <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    <?php echo esc_html($service); ?>
                  </h1>
                  <p class="text-slate-400 text-sm mt-1">
                    Propuesta Comercial preparada para <strong class="text-white"><?php echo esc_html($client_name); ?></strong> — <?php echo esc_html($company); ?>
                  </p>
                </div>
              </div>

              <div class="bg-slate-950/90 border border-slate-800 rounded-2xl px-6 py-4 text-left md:text-right">
                <span class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold block">
                  Total Propuesta Comercial
                </span>
                <?php if ($amount > 0) : ?>
                  <div class="text-3xl font-black text-orange-400 mt-0.5">
                    $<?php echo number_format($amount, 0, ',', '.'); ?> <span class="text-xs font-normal text-slate-400">CLP</span>
                  </div>
                  <span class="text-[11px] text-slate-500">
                    Entrega estimada: <?php echo esc_html($delivery_date); ?>
                  </span>
                <?php else : ?>
                  <div class="text-xl font-bold text-amber-400 mt-1">
                    En evaluación técnica
                  </div>
                  <span class="text-[11px] text-slate-500">
                    Asignar monto en WP-Admin
                  </span>
                <?php endif; ?>
              </div>
            </div>

            <!-- Línea de progreso del estado -->
            <?php
            $steps = ['Pendiente', 'En Revisión', 'Cotizada', 'Aprobado'];
            $current_idx = array_search($status, $steps, true);
            if ($current_idx === false) $current_idx = 2;
            ?>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-8 pt-6 border-t border-slate-800/80">
              <?php foreach ($steps as $i => $step_label) :
                  $done = ($i <= $current_idx && $status !== 'Rechazado');
              ?>
                <div class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border <?php echo $done ? 'bg-orange-500/10 border-orange-500/40 text-white' : 'bg-slate-950/60 border-slate-800 text-slate-500'; ?>">
                  <span class="w-6 h-6 rounded-full text-xs font-extrabold flex items-center justify-center <?php echo $done ? 'bg-orange-600 text-white' : 'bg-slate-800 text-slate-400'; ?>">
                    <?php echo $done ? '✓' : ($i + 1); ?>
                  </span>
                  <span class="text-xs font-bold"><?php echo esc_html($step_label); ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Cuerpo en 2 Columnas -->
          <div class="p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Columna Izquierda: Solicitud del Cliente + Imagen de Referencia -->
            <div class="lg:col-span-5 space-y-6">
              <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-orange-400 mb-4">
                  1. Datos del Solicitante
                </h3>
                <dl class="space-y-3 text-sm">
                  <div class="flex justify-between border-b border-slate-800/80 pb-2">
                    <dt class="text-slate-400">Cliente:</dt>
                    <dd class="font-semibold text-white text-right"><?php echo esc_html($client_name); ?></dd>
                  </div>
                  <div class="flex justify-between border-b border-slate-800/80 pb-2">
                    <dt class="text-slate-400">Empresa / Flota:</dt>
                    <dd class="font-semibold text-white text-right"><?php echo esc_html($company); ?></dd>
                  </div>
                  <div class="flex justify-between border-b border-slate-800/80 pb-2">
                    <dt class="text-slate-400">Correo:</dt>
                    <dd class="font-semibold text-orange-400 text-right"><?php echo esc_html($email); ?></dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-slate-400">Teléfono / WhatsApp:</dt>
                    <dd class="font-semibold text-slate-200 text-right"><?php echo esc_html($phone); ?></dd>
                  </div>
                </dl>
              </div>

              <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-orange-400 mb-3">
                  2. Requerimiento Enviado por el Cliente
                </h3>
                <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">
                  <?php echo esc_html($description); ?>
                </p>

                <?php if ($ref_image) : ?>
                  <div class="mt-5">
                    <span class="text-xs font-semibold text-slate-400 block mb-2">
                      📸 Referencia visual del proyecto:
                    </span>
                    <img
                      src="<?php echo esc_url($ref_image); ?>"
                      alt="Referencia Cotización"
                      class="w-full h-52 object-cover rounded-xl border border-slate-800"
                    />
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Columna Derecha: Propuesta Técnica de Eiken Design + Desglose CLP + Botones Aprobar/Rechazar -->
            <div class="lg:col-span-7 space-y-6">
              <div class="bg-slate-950 border border-slate-800 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-sm font-bold uppercase tracking-wider text-orange-400">
                    3. Propuesta Técnica &amp; Especificación de Materiales
                  </h3>
                  <span class="text-xs bg-slate-900 border border-slate-700 text-slate-300 px-3 py-1 rounded-full">
                    Taller Los Ángeles · Garantía Oficial
                  </span>
                </div>

                <?php if ($reply_notes) : ?>
                  <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 text-slate-200 text-sm leading-relaxed whitespace-pre-line">
                    <?php echo esc_html($reply_notes); ?>
                  </div>
                <?php else : ?>
                  <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-5 text-amber-200 text-sm">
                    Esta solicitud está pendiente de cotización. Puedes ingresar el monto y la propuesta técnica desde <strong>WP-Admin → Cotizaciones Eiken</strong>.
                  </div>
                <?php endif; ?>

                <!-- Tabla de Desglose Económico -->
                <?php if ($amount > 0) : ?>
                  <div class="mt-6 border border-slate-800 rounded-2xl overflow-hidden">
                    <table class="w-full text-sm">
                      <thead class="bg-slate-900 text-slate-400 text-xs uppercase">
                        <tr>
                          <th class="text-left py-3 px-4">Ítem / Servicio Cotizado</th>
                          <th class="text-right py-3 px-4">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-slate-800/80">
                        <tr>
                          <td class="py-3.5 px-4">
                            <div class="font-bold text-white"><?php echo esc_html($service); ?></div>
                            <div class="text-xs text-slate-400">Incluye diseño digital a escala, materiales certificados e instalación en taller</div>
                          </td>
                          <td class="py-3.5 px-4 text-right font-bold text-slate-200">
                            $<?php echo number_format($neto, 0, ',', '.'); ?>
                          </td>
                        </tr>
                        <tr class="bg-slate-900/40">
                          <td class="py-2.5 px-4 text-slate-400 text-right">IVA (19%)</td>
                          <td class="py-2.5 px-4 text-right text-slate-300">$<?php echo number_format($iva, 0, ',', '.'); ?></td>
                        </tr>
                        <tr class="bg-orange-500/10">
                          <td class="py-3.5 px-4 font-extrabold text-white text-right">TOTAL COTIZADO (IVA Incluido)</td>
                          <td class="py-3.5 px-4 text-right font-black text-xl text-orange-400">$<?php echo number_format($amount, 0, ',', '.'); ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Acciones interactivas del Cliente -->
              <div class="bg-gradient-to-br from-slate-950 to-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                  <h4 class="text-white font-bold text-base">¿Deseas aprobar o rechazar esta propuesta?</h4>
                  <p class="text-slate-400 text-xs mt-0.5">
                    Prueba cómo el cliente aprueba su cotización en línea y el estado se actualiza automáticamente en WordPress.
                  </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                  <button
                    type="button"
                    onclick="window.eikenRespondQuote(<?php echo intval($qid); ?>, 'Aprobado')"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all"
                  >
                    ✓ Aprobar Cotización
                  </button>
                  <button
                    type="button"
                    onclick="window.eikenRespondQuote(<?php echo intval($qid); ?>, 'Rechazado')"
                    class="bg-slate-800 hover:bg-red-600/80 text-slate-200 hover:text-white border border-slate-700 px-4 py-2.5 rounded-xl font-bold text-xs transition-all"
                  >
                    ✕ Rechazar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <script>
      window.eikenRespondQuote = async function(quoteId, decision) {
        const body = new URLSearchParams({
          action: 'eiken_respond_quote',
          quote_id: String(quoteId),
          decision: decision
        });
        const res = await fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          body: body.toString()
        });
        const json = await res.json();
        if (json.success) {
          if (window.Swal) {
            await Swal.fire({
              icon: decision === 'Aprobado' ? 'success' : 'info',
              title: 'Estado actualizado a: ' + decision,
              text: json.data.message,
              background: '#0f172a',
              color: '#f8fafc',
              confirmButtonColor: '#ea580c'
            });
          }
          window.location.reload();
        }
      };
    </script>
    <?php
    get_footer();
    exit;
});

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
        'post_title' => $name . ($company ? " ($company)" : ''),
        'post_content' => $description,
        'post_status' => 'publish',
    ]);

    if ($post_id) {
        update_post_meta($post_id, '_eiken_client_name', $name);
        update_post_meta($post_id, '_eiken_email', $email);
        update_post_meta($post_id, '_eiken_phone', $phone);
        update_post_meta($post_id, '_eiken_company', $company);
        update_post_meta($post_id, '_eiken_service', $service);
        update_post_meta($post_id, '_eiken_category', 'Vehicular');
        update_post_meta($post_id, '_eiken_description', $description);
        update_post_meta($post_id, '_eiken_status', 'Pendiente');
    }

    wp_send_json_success([
        'quoteId' => $post_id,
        'previewUrl' => home_url('/?eiken_quote_view=' . $post_id),
        'message' => '¡Cotización enviada! Puedes ver el comprobante de tu solicitud o revisarla en WP-Admin → Cotizaciones Eiken.',
    ]);
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
