<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <header class="eiken-header">
    <div class="eiken-header-inner">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="eiken-brand">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/logo.png'); ?>" alt="Eiken Design">
        <div>
          <div class="eiken-brand-title">EIKEN <span>DESIGN</span></div>
          <div class="eiken-brand-sub">DISEÑO &amp; PUBLICIDAD • WOOCOMMERCE</div>
        </div>
      </a>

      <nav class="eiken-nav">
        <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/')); ?>">Tienda</a>
        <a href="<?php echo esc_url(admin_url()); ?>">Panel WP-Admin</a>
        <?php if (function_exists('WC') && WC()->cart) : ?>
          <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="eiken-cart-btn">
            🛒 Carrito (<?php echo intval(WC()->cart->get_cart_contents_count()); ?>)
          </a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <section class="eiken-hero">
    <span class="eiken-hero-badge">Tienda Oficial • Prototipo WooCommerce</span>
    <h1>Eiken <span>Design</span></h1>
    <p>Poleras, polerones, stickers y productos personalizados con pago seguro vía Webpay Plus (Transbank).</p>
  </section>

  <main class="eiken-container">
