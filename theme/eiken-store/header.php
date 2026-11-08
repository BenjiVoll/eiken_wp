<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
          colors: {
            eiken: {
              red: '#ff2b00',
              orange: '#ff3d00',
              black: '#0A0A0C',
              silver: '#C4C7CE'
            }
          }
        }
      }
    };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php wp_head(); ?>
</head>
<body <?php body_class('bg-slate-950 text-slate-100 font-sans antialiased selection:bg-orange-600 selection:text-white'); ?>>
<?php
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/?post_type=product');
$cart_count = (function_exists('WC') && WC()->cart) ? intval(WC()->cart->get_cart_contents_count()) : 0;
?>
  <!-- ==============================================================
       STICKY NAVBAR EIKEN DESIGN (WORDPRESS + WOOCOMMERCE)
       ============================================================== -->
  <header class="bg-slate-950/95 backdrop-blur-md shadow-lg shadow-black/30 border-b border-slate-800/80 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center space-x-3 group">
          <img
            src="<?php echo esc_url(get_template_directory_uri() . '/logo.png'); ?>"
            alt="Eiken Design Logo"
            class="h-10 w-auto rounded transition-transform group-hover:scale-105"
          />
          <div class="hidden sm:block">
            <div class="text-lg font-extrabold tracking-tight text-white leading-none">
              EIKEN <span class="text-orange-500">DESIGN</span>
            </div>
            <p class="text-[11px] text-slate-400 tracking-wide mt-0.5">
              Diseño Publicitario &amp; Gráfica Vehicular
            </p>
          </div>
        </a>

        <!-- Navegación Desktop -->
        <nav class="hidden md:flex items-center space-x-7">
          <a href="<?php echo esc_url(home_url('/#servicios')); ?>" class="text-slate-300 hover:text-orange-500 font-medium text-sm transition-colors">
            Servicios
          </a>
          <a href="<?php echo esc_url(home_url('/#portafolio')); ?>" class="text-slate-300 hover:text-orange-500 font-medium text-sm transition-colors">
            Portafolio
          </a>
          <a href="<?php echo esc_url(home_url('/#nosotros')); ?>" class="text-slate-300 hover:text-orange-500 font-medium text-sm transition-colors">
            Nosotros
          </a>
          <a href="<?php echo esc_url(home_url('/#contacto')); ?>" class="text-slate-300 hover:text-orange-500 font-medium text-sm transition-colors">
            Contacto
          </a>
          <a href="<?php echo esc_url(admin_url()); ?>" class="text-xs font-semibold text-slate-400 hover:text-white bg-slate-900 border border-slate-800 px-3 py-1.5 rounded-lg transition-colors">
            ⚙️ WP-Admin
          </a>
        </nav>

        <!-- Acciones Derecha (Tienda + Carrito Drawer) -->
        <div class="flex items-center space-x-3">
          <a
            href="<?php echo esc_url($shop_url); ?>"
            class="bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm shadow-md shadow-orange-600/20 hover:shadow-orange-600/35 transition-all flex items-center space-x-2"
          >
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <span>Ir a Tienda</span>
          </a>

          <button
            type="button"
            onclick="window.eikenOpenCartDrawer()"
            class="relative p-2.5 text-slate-300 hover:text-orange-400 hover:bg-slate-900 rounded-xl border border-slate-800 transition-colors"
            title="Ver carrito de compras"
            aria-label="Abrir carrito de compras"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span
              id="eiken-header-cart-badge"
              class="<?php echo $cart_count > 0 ? 'flex' : 'hidden'; ?> absolute -top-1 -right-1 bg-orange-600 text-white text-[11px] rounded-full h-5 w-5 items-center justify-center font-bold shadow-sm"
            >
              <?php echo $cart_count; ?>
            </span>
          </button>
        </div>
      </div>
    </div>
  </header>
