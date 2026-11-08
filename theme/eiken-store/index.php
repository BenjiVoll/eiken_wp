<?php
get_header();

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/?post_type=product');
$is_cart_page = function_exists('is_cart') && is_cart();
$is_checkout_page = function_exists('is_checkout') && is_checkout();
?>

<div class="eiken-wc-surface bg-slate-50 min-h-screen text-slate-900 pb-20">
  <!-- Banner superior para Carrito / Checkout -->
  <div class="bg-slate-950 text-white border-b border-slate-800 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <span class="text-xs font-bold uppercase tracking-widest text-orange-400 block mb-1">
          Compra Segura • Transbank Webpay Plus
        </span>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
          <?php the_title(); ?>
        </h1>
      </div>

      <!-- Indicador de pasos de compra -->
      <?php if ($is_cart_page || $is_checkout_page) : ?>
        <div class="flex items-center gap-3 text-xs font-bold">
          <span class="px-3 py-1.5 rounded-full <?php echo $is_cart_page ? 'bg-orange-600 text-white' : 'bg-slate-800 text-slate-300'; ?>">
            1. Carrito
          </span>
          <span class="text-slate-600">→</span>
          <span class="px-3 py-1.5 rounded-full <?php echo $is_checkout_page ? 'bg-orange-600 text-white' : 'bg-slate-800 text-slate-300'; ?>">
            2. Datos y Pago Webpay
          </span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Contenido principal -->
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
    <div class="mb-6">
      <a href="<?php echo esc_url($shop_url); ?>" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-orange-600 transition-colors">
        <span>← Seguir comprando en la Tienda</span>
      </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-10">
      <?php
      if (have_posts()) :
          while (have_posts()) : the_post();
              the_content();
          endwhile;
      endif;
      ?>
    </div>
  </div>
</div>

<?php get_footer(); ?>
