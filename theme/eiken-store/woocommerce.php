<?php
get_header();

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/?post_type=product');
$checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/?page_id=8');

if (function_exists('is_product') && is_product()) :
    // ==============================================================
    // VISTA DETALLE DE PRODUCTO INDIVIDUAL (SINGLE PRODUCT)
    // ==============================================================
    while (have_posts()) : the_post();
        global $product;
        $pid = $product->get_id();
        $img = wp_get_attachment_image_url($product->get_image_id(), 'full') ?: (get_template_directory_uri() . '/logo.png');
        $cats = wp_get_post_terms($pid, 'product_cat', ['fields' => 'names']);
        $cat_name = !empty($cats) ? $cats[0] : 'General';
        $stock = $product->get_stock_quantity() ?: 25;
        $sku = $product->get_sku() ?: ('EIK-' . $pid);
        $attrs = $product->get_attributes();
        $sizes = [];
        foreach ($attrs as $attr) {
            if (is_object($attr) && method_exists($attr, 'get_options')) {
                $sizes = $attr->get_options();
            }
        }
        if (empty($sizes)) {
            $sizes = ['Estándar'];
        }
?>
<div class="bg-slate-50 min-h-screen text-slate-900 py-10">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-8">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-orange-600">Inicio</a>
      <span>/</span>
      <a href="<?php echo esc_url($shop_url); ?>" class="hover:text-orange-600">Tienda WooCommerce</a>
      <span>/</span>
      <span class="text-orange-600"><?php echo esc_html($cat_name); ?></span>
      <span>/</span>
      <span class="text-slate-800 truncate"><?php the_title(); ?></span>
    </nav>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
      <!-- Galería Izquierda -->
      <div class="relative rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 aspect-[4/3] group">
        <img
          src="<?php echo esc_url($img); ?>"
          alt="<?php the_title_attribute(); ?>"
          class="w-full h-full object-cover cursor-zoom-in transition-transform duration-500 group-hover:scale-105"
          onclick="window.eikenOpenLightbox('<?php echo esc_url($img); ?>')"
        />
        <span class="absolute top-4 left-4 bg-slate-950/90 text-orange-400 text-xs font-extrabold uppercase px-3.5 py-1.5 rounded-full">
          <?php echo esc_html($cat_name); ?>
        </span>
        <button
          type="button"
          onclick="window.eikenOpenLightbox('<?php echo esc_url($img); ?>')"
          class="absolute bottom-4 right-4 bg-slate-950/80 hover:bg-orange-600 text-white text-xs font-bold px-3.5 py-2 rounded-xl backdrop-blur-md transition-colors"
        >
          🔍 Ampliar imagen
        </button>
      </div>

      <!-- Información Derecha -->
      <div class="space-y-6">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">SKU: <?php echo esc_html($sku); ?></span>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Disponible (<?php echo intval($stock); ?> en stock)
          </span>
        </div>

        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight"><?php the_title(); ?></h1>

        <div class="flex items-baseline gap-3 pb-4 border-b border-slate-100">
          <span class="text-4xl font-black text-orange-600">
            $<?php echo number_format((float) $product->get_price(), 0, ',', '.'); ?>
          </span>
          <span class="text-xs font-semibold text-slate-400 uppercase">CLP • IVA Incluido</span>
        </div>

        <p class="text-sm text-slate-600 leading-relaxed">
          <?php echo esc_html(wp_strip_all_tags($product->get_description() ?: $product->get_short_description())); ?>
        </p>

        <!-- Selector de Talla / Formato (Ventaja de WooCommerce) -->
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2.5">
            Selecciona Talla / Formato:
          </label>
          <div class="flex flex-wrap gap-2.5" id="eiken-single-size-group">
            <?php foreach ($sizes as $idx => $sz) : ?>
              <button
                type="button"
                onclick="window.eikenSelectSingleSize(this, '<?php echo esc_js($sz); ?>')"
                data-size="<?php echo esc_attr($sz); ?>"
                class="eiken-size-pill px-4 py-2 rounded-xl text-xs font-bold border transition-all <?php echo $idx === 0 ? 'bg-slate-950 text-white border-slate-950 shadow-sm' : 'bg-white text-slate-700 border-slate-300 hover:border-orange-500'; ?>"
              >
                <?php echo esc_html($sz); ?>
              </button>
            <?php endforeach; ?>
          </div>
          <input type="hidden" id="eiken-selected-size" value="<?php echo esc_attr($sizes[0]); ?>" />
        </div>

        <!-- Selector de Cantidad + Botones de Compra -->
        <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
          <div class="inline-flex items-center justify-between border border-slate-300 rounded-xl bg-slate-50 px-3 py-2">
            <button type="button" onclick="window.eikenStepSingleQty(-1)" class="w-8 h-8 rounded-lg hover:bg-white font-bold text-slate-700">-</button>
            <input type="number" id="eiken-single-qty" value="1" min="1" max="<?php echo intval($stock); ?>" class="w-12 text-center bg-transparent font-extrabold text-slate-900 text-sm focus:outline-none" readonly />
            <button type="button" onclick="window.eikenStepSingleQty(1)" class="w-8 h-8 rounded-lg hover:bg-white font-bold text-slate-700">+</button>
          </div>

          <button
            type="button"
            onclick="window.eikenAddSingleToCart(<?php echo intval($pid); ?>, false)"
            class="flex-1 py-3.5 px-6 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm shadow-lg shadow-orange-600/25 transition-all"
          >
            🛒 Agregar al Carrito
          </button>

          <button
            type="button"
            onclick="window.eikenAddSingleToCart(<?php echo intval($pid); ?>, true)"
            class="py-3.5 px-5 rounded-xl bg-slate-950 hover:bg-slate-800 text-white font-bold text-sm transition-all"
          >
            Comprar Ahora →
          </button>
        </div>

        <!-- Beneficios Eiken Design + Webpay -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-6 border-t border-slate-100 text-xs">
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70">
            <span class="font-bold text-slate-900 block">💳 Webpay Plus Oficial</span>
            <span class="text-slate-500">Débito Redcompra y Crédito</span>
          </div>
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70">
            <span class="font-bold text-slate-900 block">📍 Retiro Gratis en Taller</span>
            <span class="text-slate-500">Calle 2 Sur 106, Los Ángeles</span>
          </div>
          <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70">
            <span class="font-bold text-slate-900 block">🚚 Despacho a Todo Chile</span>
            <span class="text-slate-500">Envíos rápidos y protegidos</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window.eikenSelectSingleSize = function(btn, size) {
    document.getElementById('eiken-selected-size').value = size;
    document.querySelectorAll('.eiken-size-pill').forEach(el => {
      el.className = 'eiken-size-pill px-4 py-2 rounded-xl text-xs font-bold border transition-all bg-white text-slate-700 border-slate-300 hover:border-orange-500';
    });
    btn.className = 'eiken-size-pill px-4 py-2 rounded-xl text-xs font-bold border transition-all bg-slate-950 text-white border-slate-950 shadow-sm';
  };

  window.eikenStepSingleQty = function(delta) {
    const input = document.getElementById('eiken-single-qty');
    const max = parseInt(input.getAttribute('max') || '99', 10);
    let val = parseInt(input.value || '1', 10) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    input.value = val;
  };

  window.eikenAddSingleToCart = function(productId, goToCheckout) {
    const qty = parseInt(document.getElementById('eiken-single-qty')?.value || '1', 10);
    const size = document.getElementById('eiken-selected-size')?.value || '';
    if (goToCheckout) {
      const body = new URLSearchParams({ action: 'eiken_add_to_cart', product_id: productId, quantity: qty, size: size });
      fetch(window.EIKEN_AJAX_URL, { method: 'POST', body })
        .then(() => { window.location.href = <?php echo json_encode($checkout_url); ?>; });
    } else {
      window.eikenAddToCart(productId, qty, size);
    }
  };
</script>
<?php
    endwhile;
else :
    // ==============================================================
    // TIENDA OFICIAL WOOCOMMERCE (CATÁLOGO CON FILTROS INTERACTIVOS)
    // ==============================================================
    $all_products = function_exists('wc_get_products') ? wc_get_products([
        'status' => 'publish',
        'limit' => -1,
    ]) : [];
    $all_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
?>
<div class="bg-slate-50 min-h-screen text-slate-900">
  <!-- Hero de la Tienda -->
  <div class="relative bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white overflow-hidden border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
      <span class="inline-block px-3.5 py-1 mb-3 text-xs font-bold tracking-wider text-orange-300 uppercase bg-orange-950/60 rounded-full border border-orange-500/30">
        Tienda Online • WooCommerce + Webpay Plus
      </span>
      <h1 class="text-3xl sm:text-5xl font-black tracking-tight mb-3">
        Eiken <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-red-500">Design Store</span>
      </h1>
      <p class="max-w-2xl mx-auto text-sm sm:text-base text-slate-300 font-light">
        Poleras, polerones, stickers y productos personalizados con selector de tallas y pago seguro vía Transbank Webpay Plus.
      </p>
    </div>
  </div>

  <!-- Contenido Principal: Sidebar de Filtros + Grilla de Productos -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Sidebar de Filtros (Columna 1) -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 lg:sticky lg:top-24 space-y-6">
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-base font-extrabold text-slate-900">Filtros de Tienda</h2>
            <button type="button" onclick="window.eikenResetStoreFilters()" class="text-xs font-bold text-orange-600 hover:underline">
              Limpiar
            </button>
          </div>

          <!-- Buscador -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Buscar producto</label>
            <input
              type="text"
              id="eiken-store-search"
              oninput="window.eikenApplyStoreFilters()"
              placeholder="Ej: Polera, Honda, F1..."
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-900 focus:outline-none focus:border-orange-500 focus:bg-white transition-all"
            />
          </div>

          <!-- Categorías -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">Categorías</label>
            <div class="space-y-2">
              <?php foreach ($all_cats as $term) :
                if ($term->slug === 'uncategorized') continue;
              ?>
                <label class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 cursor-pointer">
                  <span class="flex items-center gap-2.5 text-sm font-medium text-slate-700">
                    <input
                      type="checkbox"
                      value="<?php echo esc_attr($term->name); ?>"
                      onchange="window.eikenApplyStoreFilters()"
                      class="eiken-store-cat-cb w-4 h-4 rounded text-orange-600 focus:ring-orange-500"
                    />
                    <span><?php echo esc_html($term->name); ?></span>
                  </span>
                  <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full"><?php echo intval($term->count); ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Rango de Precio Máximo -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Precio Máximo</label>
              <span id="eiken-store-price-label" class="text-xs font-extrabold text-orange-600">$50.000</span>
            </div>
            <input
              type="range"
              id="eiken-store-price-range"
              min="5000"
              max="50000"
              step="1000"
              value="50000"
              oninput="window.eikenApplyStoreFilters()"
              class="w-full accent-orange-600 cursor-pointer"
            />
            <div class="flex justify-between text-[11px] text-slate-400 mt-1">
              <span>$5.000</span>
              <span>$50.000</span>
            </div>
          </div>

          <!-- Ordenar por -->
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Ordenar por</label>
            <select
              id="eiken-store-sort"
              onchange="window.eikenApplyStoreFilters()"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-800 focus:outline-none focus:border-orange-500"
            >
              <option value="default">Destacados</option>
              <option value="price-asc">Precio: Menor a Mayor</option>
              <option value="price-desc">Precio: Mayor a Menor</option>
              <option value="name-asc">Nombre: A – Z</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Grilla de Productos (Columnas 2 a 4) -->
      <div class="lg:col-span-3">
        <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
          <p class="text-sm text-slate-600">
            Mostrando <span id="eiken-store-count" class="font-extrabold text-slate-900"><?php echo count($all_products); ?></span> productos disponibles
          </p>
          <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
            ✔ Pasarela Transbank Webpay Plus Activa
          </span>
        </div>

        <div id="eiken-store-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($all_products as $product) :
            $pid = $product->get_id();
            $img = wp_get_attachment_image_url($product->get_image_id(), 'large') ?: (get_template_directory_uri() . '/logo.png');
            $cats = wp_get_post_terms($pid, 'product_cat', ['fields' => 'names']);
            $cat_name = !empty($cats) ? $cats[0] : 'General';
            $stock = $product->get_stock_quantity() ?: 25;
            $price = (int) $product->get_price();
            $attrs = $product->get_attributes();
            $sizes = [];
            foreach ($attrs as $attr) {
                if (is_object($attr) && method_exists($attr, 'get_options')) {
                    $sizes = $attr->get_options();
                }
            }
            if (empty($sizes)) {
                $sizes = ['Estándar'];
            }
          ?>
            <div
              class="eiken-store-card group bg-white rounded-3xl p-4 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-slate-200/80 hover:border-orange-500/40 flex flex-col justify-between"
              data-name="<?php echo esc_attr(strtolower($product->get_name() . ' ' . $product->get_short_description())); ?>"
              data-cat="<?php echo esc_attr($cat_name); ?>"
              data-price="<?php echo intval($price); ?>"
              data-title="<?php echo esc_attr($product->get_name()); ?>"
            >
              <div>
                <!-- Imagen -->
                <div class="relative overflow-hidden rounded-2xl aspect-[4/3] bg-slate-100 mb-4">
                  <a href="<?php echo esc_url(get_permalink($pid)); ?>">
                    <img
                      src="<?php echo esc_url($img); ?>"
                      alt="<?php echo esc_attr($product->get_name()); ?>"
                      class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                    />
                  </a>
                  <span class="absolute top-3 left-3 bg-slate-950/85 backdrop-blur-md text-orange-400 text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full">
                    <?php echo esc_html($cat_name); ?>
                  </span>
                  <span class="absolute top-3 right-3 bg-emerald-600/95 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow-sm">
                    <?php echo intval($stock); ?> disp.
                  </span>
                </div>

                <!-- Título y Descripción -->
                <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="block">
                  <h3 class="text-lg font-extrabold text-slate-900 group-hover:text-orange-600 transition-colors leading-snug mb-1">
                    <?php echo esc_html($product->get_name()); ?>
                  </h3>
                </a>
                <p class="text-xs text-slate-500 line-clamp-2 mb-3">
                  <?php echo esc_html(wp_strip_all_tags($product->get_short_description())); ?>
                </p>

                <!-- Selector rápido de Talla / Formato en la tarjeta -->
                <div class="mb-4">
                  <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Talla / Formato:</span>
                  <div class="flex flex-wrap gap-1.5" data-card-sizes="<?php echo intval($pid); ?>">
                    <?php foreach ($sizes as $idx => $sz) : ?>
                      <button
                        type="button"
                        onclick="window.eikenSelectCardSize(<?php echo intval($pid); ?>, this, '<?php echo esc_js($sz); ?>')"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-all <?php echo $idx === 0 ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-orange-400'; ?>"
                      >
                        <?php echo esc_html($sz); ?>
                      </button>
                    <?php endforeach; ?>
                  </div>
                  <input type="hidden" id="eiken-card-size-<?php echo intval($pid); ?>" value="<?php echo esc_attr($sizes[0]); ?>" />
                </div>
              </div>

              <!-- Precio y Botón Agregar al Carrito -->
              <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                <div>
                  <span class="text-[10px] uppercase tracking-wider text-slate-400 block font-semibold">Precio</span>
                  <span class="text-xl font-black text-slate-900">$<?php echo number_format($price, 0, ',', '.'); ?></span>
                </div>

                <div class="flex items-center gap-1.5">
                  <a
                    href="<?php echo esc_url(get_permalink($pid)); ?>"
                    class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:text-orange-600 hover:border-orange-300 transition-colors text-xs font-bold"
                    title="Ver detalle del producto"
                  >
                    👁️
                  </a>
                  <button
                    type="button"
                    onclick="window.eikenAddCardToCart(<?php echo intval($pid); ?>)"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white text-xs font-bold shadow-md shadow-orange-600/20 transition-all active:scale-95"
                  >
                    + Agregar
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window.eikenSelectCardSize = function(pid, btn, size) {
    const input = document.getElementById('eiken-card-size-' + pid);
    if (input) input.value = size;
    const container = document.querySelector('[data-card-sizes="' + pid + '"]');
    if (container) {
      container.querySelectorAll('button').forEach(b => {
        b.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-all bg-slate-50 text-slate-600 border-slate-200 hover:border-orange-400';
      });
    }
    btn.className = 'px-2.5 py-1 rounded-lg text-[11px] font-bold border transition-all bg-slate-900 text-white border-slate-900';
  };

  window.eikenAddCardToCart = function(pid) {
    const size = document.getElementById('eiken-card-size-' + pid)?.value || '';
    window.eikenAddToCart(pid, 1, size);
  };

  window.eikenApplyStoreFilters = function() {
    const q = (document.getElementById('eiken-store-search')?.value || '').toLowerCase();
    const maxPrice = parseInt(document.getElementById('eiken-store-price-range')?.value || '50000', 10);
    const sort = document.getElementById('eiken-store-sort')?.value || 'default';
    const priceLabel = document.getElementById('eiken-store-price-label');
    if (priceLabel) priceLabel.textContent = '$' + maxPrice.toLocaleString('es-CL');

    const selectedCats = Array.from(document.querySelectorAll('.eiken-store-cat-cb:checked')).map(cb => cb.value);
    const grid = document.getElementById('eiken-store-grid');
    const cards = Array.from(document.querySelectorAll('.eiken-store-card'));

    let visibleCount = 0;
    cards.forEach(card => {
      const name = card.getAttribute('data-name') || '';
      const cat = card.getAttribute('data-cat') || '';
      const price = parseInt(card.getAttribute('data-price') || '0', 10);

      const matchQ = !q || name.includes(q);
      const matchCat = selectedCats.length === 0 || selectedCats.includes(cat);
      const matchPrice = price <= maxPrice;

      if (matchQ && matchCat && matchPrice) {
        card.style.display = '';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    if (sort !== 'default' && grid) {
      cards.sort((a, b) => {
        const pa = parseInt(a.getAttribute('data-price') || '0', 10);
        const pb = parseInt(b.getAttribute('data-price') || '0', 10);
        const na = a.getAttribute('data-title') || '';
        const nb = b.getAttribute('data-title') || '';
        if (sort === 'price-asc') return pa - pb;
        if (sort === 'price-desc') return pb - pa;
        if (sort === 'name-asc') return na.localeCompare(nb);
        return 0;
      });
      cards.forEach(c => grid.appendChild(c));
    }

    const countEl = document.getElementById('eiken-store-count');
    if (countEl) countEl.textContent = visibleCount;
  };

  window.eikenResetStoreFilters = function() {
    const s = document.getElementById('eiken-store-search');
    if (s) s.value = '';
    const r = document.getElementById('eiken-store-price-range');
    if (r) r.value = 50000;
    const o = document.getElementById('eiken-store-sort');
    if (o) o.value = 'default';
    document.querySelectorAll('.eiken-store-cat-cb').forEach(cb => cb.checked = false);
    window.eikenApplyStoreFilters();
  };
</script>
<?php
endif;

get_footer();
