<?php
get_header();

$services = eiken_get_services_data();
$projects = eiken_get_projects_data();
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/?post_type=product');

$wc_products = function_exists('wc_get_products') ? wc_get_products([
    'status' => 'publish',
    'limit' => 4,
]) : [];
?>

<!-- ==============================================================
     HERO SECTION (High Impact Motorsport & Design Studio)
     ============================================================== -->
<section class="relative min-h-[88vh] flex items-center justify-center overflow-hidden bg-slate-950 pt-14 pb-24">
  <!-- Resplandor decorativo -->
  <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[700px] h-[400px] bg-gradient-to-tr from-orange-600/25 to-red-600/15 rounded-full blur-[130px] pointer-events-none"></div>

  <!-- Carrusel de fondo con imágenes reales de proyectos -->
  <div class="absolute inset-0 z-0" id="eiken-hero-slides">
    <?php foreach ($projects as $idx => $proj) : ?>
      <div
        class="eiken-hero-slide absolute inset-0 transition-all duration-1000 ease-out <?php echo $idx === 0 ? 'opacity-90 scale-100' : 'opacity-0 scale-105'; ?>"
        data-slide-idx="<?php echo $idx; ?>"
      >
        <img
          src="<?php echo esc_url($proj['image']); ?>"
          alt="<?php echo esc_attr($proj['title']); ?>"
          class="w-full h-full object-cover filter brightness-95 contrast-110 saturate-110"
        />
      </div>
    <?php endforeach; ?>
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/60 via-slate-950/45 to-slate-950/90"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(2,6,23,0.45)_0%,rgba(2,6,23,0.15)_65%,rgba(2,6,23,0.65)_100%)]"></div>
  </div>

  <!-- Contenido Principal del Hero -->
  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-8">
      <span class="flex h-2 w-2 rounded-full bg-orange-500 animate-ping"></span>
      <span class="text-xs font-semibold tracking-wider uppercase text-orange-300">
        Estudio de Gráfica &amp; Rotulado Profesional • WordPress + WooCommerce
      </span>
    </div>

    <h1 class="text-4xl sm:text-6xl md:text-7xl font-black tracking-tight text-white mb-6">
      Transformamos tu Marca en
      <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 via-red-500 to-orange-500">
        Movimiento &amp; Impacto
      </span>
    </h1>

    <p class="text-lg sm:text-xl text-slate-200 max-w-3xl mx-auto mb-10 leading-relaxed font-light">
      Wrap vehicular de alta precisión, gráfica para competición y soluciones de diseño publicitario integral.
      Materiales premium y tienda online integrada con <strong>WooCommerce y Webpay Plus</strong>.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <button
        type="button"
        onclick="window.eikenOpenQuoteModal()"
        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold text-base shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 transition-all transform hover:-translate-y-0.5"
      >
        <span>Cotizar Proyecto Ahora</span>
        <span>→</span>
      </button>

      <a
        href="<?php echo esc_url($shop_url); ?>"
        class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/15 text-white font-semibold text-base backdrop-blur-md transition-all hover:-translate-y-0.5"
      >
        <span class="text-orange-400">🛍️</span>
        <span>Explorar Tienda Online</span>
      </a>
    </div>

    <!-- Estadísticas Bento -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mt-16 max-w-5xl mx-auto">
      <div class="bg-slate-950/60 border border-white/10 backdrop-blur-md rounded-2xl p-5 hover:border-orange-500/40 transition-all">
        <div class="text-3xl sm:text-4xl font-extrabold text-orange-400 mb-1">20+</div>
        <div class="text-xs uppercase tracking-wider text-slate-300 font-medium">Años de Trayectoria</div>
      </div>
      <div class="bg-slate-950/60 border border-white/10 backdrop-blur-md rounded-2xl p-5 hover:border-orange-500/40 transition-all">
        <div class="text-3xl sm:text-4xl font-extrabold text-orange-400 mb-1">500+</div>
        <div class="text-xs uppercase tracking-wider text-slate-300 font-medium">Proyectos Finalizados</div>
      </div>
      <div class="bg-slate-950/60 border border-white/10 backdrop-blur-md rounded-2xl p-5 hover:border-orange-500/40 transition-all">
        <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1">Avery</div>
        <div class="text-xs uppercase tracking-wider text-slate-300 font-medium">Instalador Certificado</div>
      </div>
      <div class="bg-slate-950/60 border border-white/10 backdrop-blur-md rounded-2xl p-5 hover:border-orange-500/40 transition-all">
        <div class="text-3xl sm:text-4xl font-extrabold text-emerald-400 mb-1">100%</div>
        <div class="text-xs uppercase tracking-wider text-slate-300 font-medium">Calidad Garantizada</div>
      </div>
    </div>

    <!-- Puntos del carrusel -->
    <div class="mt-8 flex justify-center items-center gap-2" id="eiken-hero-dots">
      <?php foreach ($projects as $idx => $proj) : ?>
        <button
          type="button"
          onclick="window.eikenSetHeroSlide(<?php echo $idx; ?>)"
          class="eiken-hero-dot h-2 rounded-full transition-all duration-300 <?php echo $idx === 0 ? 'bg-orange-500 w-9 shadow-sm shadow-orange-500/50' : 'bg-white/35 hover:bg-white/60 w-2.5'; ?>"
          aria-label="Ir a diapositiva <?php echo $idx + 1; ?>"
        ></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==============================================================
     NUESTRAS 3 DIVISIONES ESPECIALIZADAS
     ============================================================== -->
<section class="py-24 bg-slate-900 border-t border-slate-800/80 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <span class="text-xs font-bold text-orange-400 uppercase tracking-widest block mb-2">Especialización &amp; Dominio Técnico</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Nuestras Divisiones de Trabajo</h2>
      <p class="text-slate-400 mt-3 text-base">Soluciones estructuradas para atender desde marcas corporativas hasta el automovilismo de alta exigencia.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- 1. Eiken Design -->
      <div class="group bg-slate-950 border border-slate-800 rounded-3xl p-8 hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between">
        <div>
          <div class="w-14 h-14 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-400 flex items-center justify-center mb-6 text-2xl">🎨</div>
          <span class="text-xs font-semibold text-orange-400 uppercase tracking-wider block mb-1">División Publicitaria</span>
          <h3 class="text-2xl font-extrabold text-white mb-3">Eiken Design</h3>
          <p class="text-slate-400 text-sm leading-relaxed mb-6">Identidad corporativa, letreros publicitarios, gráfica de vitrinas e indumentaria textil personalizada para empresas.</p>
          <ul class="space-y-2 text-xs text-slate-300 mb-6">
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Branding e Identidad Visual</li>
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Señalética y Puntos de Venta</li>
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Estampado Textil DTF y Corporativo</li>
          </ul>
        </div>
        <button type="button" onclick="window.eikenOpenQuoteModal('División Eiken Design')" class="w-full py-3 rounded-xl bg-slate-900 hover:bg-orange-600 text-white text-xs font-bold border border-slate-800 hover:border-orange-500 transition-all">
          Cotizar en Eiken Design →
        </button>
      </div>

      <!-- 2. Eiken Racing Design -->
      <div class="group bg-slate-950 border border-orange-500/40 rounded-3xl p-8 hover:border-orange-500 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between relative shadow-xl shadow-orange-500/5">
        <span class="absolute top-4 right-4 bg-gradient-to-r from-orange-600 to-red-600 text-white text-[10px] font-extrabold uppercase px-3 py-1 rounded-full">Motorsport</span>
        <div>
          <div class="w-14 h-14 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center justify-center mb-6 text-2xl">🏆</div>
          <span class="text-xs font-semibold text-red-400 uppercase tracking-wider block mb-1">División Competición</span>
          <h3 class="text-2xl font-extrabold text-white mb-3">Eiken Racing Design</h3>
          <p class="text-slate-400 text-sm leading-relaxed mb-6">Diseño y rotulado de alta resistencia para autos de rally, pista, karting, cascos y motos de competición.</p>
          <ul class="space-y-2 text-xs text-slate-300 mb-6">
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Livery y Gráfica Rally Mobil / Pista</li>
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Vinilos Flúor, Metalizados y Laminados</li>
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Personalización de Cascos y Equipos</li>
          </ul>
        </div>
        <button type="button" onclick="window.eikenOpenQuoteModal('División Eiken Racing Design')" class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white text-xs font-bold transition-all shadow-md shadow-orange-600/20">
          Cotizar Proyecto Racing →
        </button>
      </div>

      <!-- 3. Eiken Truck Design -->
      <div class="group bg-slate-950 border border-slate-800 rounded-3xl p-8 hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between">
        <div>
          <div class="w-14 h-14 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-400 flex items-center justify-center mb-6 text-2xl">🚛</div>
          <span class="text-xs font-semibold text-orange-400 uppercase tracking-wider block mb-1">División Flotas &amp; Transporte</span>
          <h3 class="text-2xl font-extrabold text-white mb-3">Eiken Truck Design</h3>
          <p class="text-slate-400 text-sm leading-relaxed mb-6">Rotulación integral de flotas comerciales, camiones de carga, buses y vehículos de emergencia con vinilo certificado.</p>
          <ul class="space-y-2 text-xs text-slate-300 mb-6">
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Wrap Completo de Flotas Corporativas</li>
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Gráfica Reflectante Normada</li>
            <li class="flex items-center gap-2"><span class="text-orange-500">✔</span> Atención a Nivel Regional y Nacional</li>
          </ul>
        </div>
        <button type="button" onclick="window.eikenOpenQuoteModal('División Eiken Truck Design')" class="w-full py-3 rounded-xl bg-slate-900 hover:bg-orange-600 text-white text-xs font-bold border border-slate-800 hover:border-orange-500 transition-all">
          Cotizar Flota Vehicular →
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ==============================================================
     VITRINA TIENDA ONLINE WOOCOMMERCE (VISTA PREVIA EN PORTADA)
     ============================================================== -->
<section class="py-24 bg-slate-950 border-t border-slate-800/80 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
      <div>
        <span class="text-xs font-bold text-orange-400 uppercase tracking-widest block mb-2">E-Commerce Integrado • WooCommerce + Webpay Plus</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Productos Destacados de la Tienda</h2>
        <p class="text-slate-400 mt-2 text-base">Compra poleras, polerones y stickers personalizados con stock en tiempo real.</p>
      </div>
      <a href="<?php echo esc_url($shop_url); ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm shadow-lg shadow-orange-600/20 transition-all">
        <span>Ver Toda la Tienda WooCommerce</span>
        <span>→</span>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($wc_products as $product) :
        $pid = $product->get_id();
        $img = wp_get_attachment_image_url($product->get_image_id(), 'large') ?: (get_template_directory_uri() . '/logo.png');
        $cats = wp_get_post_terms($pid, 'product_cat', ['fields' => 'names']);
        $cat_name = !empty($cats) ? $cats[0] : 'General';
        $stock = $product->get_stock_quantity() ?: 25;
      ?>
        <div class="group bg-slate-900/90 border border-slate-800 rounded-3xl p-4 hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between">
          <div>
            <div class="relative overflow-hidden rounded-2xl aspect-[4/3] bg-slate-950 mb-4">
              <a href="<?php echo esc_url(get_permalink($pid)); ?>">
                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
              </a>
              <span class="absolute top-3 left-3 bg-slate-950/85 backdrop-blur-md border border-white/10 text-orange-400 text-[10px] font-bold uppercase px-2.5 py-1 rounded-full">
                <?php echo esc_html($cat_name); ?>
              </span>
              <span class="absolute top-3 right-3 bg-emerald-600/90 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg">
                Stock: <?php echo intval($stock); ?>
              </span>
            </div>

            <a href="<?php echo esc_url(get_permalink($pid)); ?>" class="block">
              <h3 class="text-base font-bold text-white group-hover:text-orange-400 transition-colors mb-1">
                <?php echo esc_html($product->get_name()); ?>
              </h3>
            </a>
            <p class="text-xs text-slate-400 line-clamp-2 mb-4">
              <?php echo esc_html(wp_strip_all_tags($product->get_short_description())); ?>
            </p>
          </div>

          <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
            <div>
              <span class="text-[10px] uppercase text-slate-500 block">Precio CLP</span>
              <span class="text-xl font-black text-white">$<?php echo number_format((float) $product->get_price(), 0, ',', '.'); ?></span>
            </div>
            <button
              type="button"
              onclick="window.eikenAddToCart(<?php echo intval($pid); ?>, 1)"
              class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold shadow-md shadow-orange-600/20 transition-all active:scale-95"
            >
              + Agregar
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==============================================================
     SECCIÓN DE SERVICIOS PROFESIONALES
     ============================================================== -->
<section id="servicios" class="py-24 bg-slate-900 border-t border-slate-800/80 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
      <div>
        <span class="text-xs font-bold text-orange-400 uppercase tracking-widest block mb-2">Soluciones a Medida</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Nuestros Servicios</h2>
        <p class="text-slate-400 mt-2 text-base">Elige el servicio que buscas y solicita una propuesta técnica inmediata.</p>
      </div>

      <div class="w-full md:w-72">
        <input
          type="text"
          id="eiken-service-search"
          oninput="window.eikenFilterServices()"
          placeholder="🔍 Buscar servicios..."
          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-orange-500"
        />
      </div>
    </div>

    <!-- Filtros de Categorías de Servicios -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-10">
      <?php foreach (['all' => 'Todos los Servicios', 'Vehicular' => 'Vehicular', 'Racing' => 'Racing', 'Corporativo' => 'Corporativo', 'Textil' => 'Textil'] as $key => $label) : ?>
        <button
          type="button"
          onclick="window.eikenSelectServiceCat('<?php echo esc_attr($key); ?>')"
          data-srv-cat="<?php echo esc_attr($key); ?>"
          class="eiken-srv-cat-btn px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition-all whitespace-nowrap <?php echo $key === 'all' ? 'bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-md' : 'bg-slate-950 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800'; ?>"
        >
          <?php echo esc_html($label); ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Grid de Servicios -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="eiken-services-grid">
      <?php foreach ($services as $srv) : ?>
        <div
          class="eiken-service-card group bg-slate-950 border border-slate-800/80 rounded-3xl overflow-hidden hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between"
          data-cat="<?php echo esc_attr($srv['category']); ?>"
          data-title="<?php echo esc_attr(strtolower($srv['name'] . ' ' . $srv['description'])); ?>"
        >
          <div>
            <div class="relative overflow-hidden aspect-[16/10] bg-slate-900">
              <img
                src="<?php echo esc_url($srv['image']); ?>"
                alt="<?php echo esc_attr($srv['name']); ?>"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 cursor-pointer"
                onclick="window.eikenOpenLightbox('<?php echo esc_url($srv['image']); ?>')"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent opacity-60 pointer-events-none"></div>
              <?php if (!empty($srv['popular'])) : ?>
                <span class="absolute top-3 left-3 bg-gradient-to-r from-orange-500 to-red-600 text-white text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full shadow-md">
                  Destacado
                </span>
              <?php endif; ?>
            </div>

            <div class="p-6">
              <h3 class="text-xl font-bold text-white mb-2 leading-tight group-hover:text-orange-400 transition-colors">
                <?php echo esc_html($srv['name']); ?>
              </h3>
              <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed mb-4">
                <?php echo esc_html($srv['description']); ?>
              </p>

              <div class="bg-slate-900/80 border border-slate-800/80 rounded-xl p-3 space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block">Características:</span>
                <ul class="text-xs text-slate-300 space-y-1">
                  <?php foreach ($srv['features'] as $feat) : ?>
                    <li class="flex items-center gap-1.5">
                      <span class="text-emerald-400">✔</span>
                      <span><?php echo esc_html($feat); ?></span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>

          <div class="px-6 pb-6 pt-3 border-t border-slate-800/80 flex items-center justify-between">
            <div>
              <span class="text-[10px] uppercase tracking-wider text-slate-500 block">Referencial</span>
              <span class="text-xl font-black text-white">$<?php echo number_format($srv['price'], 0, ',', '.'); ?></span>
            </div>
            <button
              type="button"
              onclick="window.eikenOpenQuoteModal('<?php echo esc_js($srv['name']); ?>')"
              class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold transition-all shadow-sm shadow-orange-500/20"
            >
              Cotizar
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==============================================================
     PORTAFOLIO DE PROYECTOS REALIZADOS (LIMPIO SIN CLIENTE NI ESTADO)
     ============================================================== -->
<section id="portafolio" class="py-24 bg-slate-950 border-t border-slate-800/80 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-12">
      <span class="text-xs font-bold text-orange-400 uppercase tracking-widest block mb-2">Galería de Trabajos Reales</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Proyectos Realizados</h2>
      <p class="text-slate-400 mt-2 text-base">Conoce la calidad de terminación y atención al detalle en cada entrega.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($projects as $proj) : ?>
        <div class="group bg-slate-900 border border-slate-800/80 rounded-3xl overflow-hidden hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1">
          <div class="relative overflow-hidden aspect-[16/11] bg-slate-950">
            <img
              src="<?php echo esc_url($proj['image']); ?>"
              alt="<?php echo esc_attr($proj['title']); ?>"
              class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 cursor-pointer"
              onclick="window.eikenOpenLightbox('<?php echo esc_url($proj['image']); ?>')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/20 to-transparent opacity-70 pointer-events-none"></div>
            <span class="absolute top-3 left-3 bg-slate-950/80 backdrop-blur-md border border-white/10 text-slate-200 text-[10px] font-bold uppercase px-3 py-1 rounded-full">
              <?php echo esc_html($proj['division']); ?>
            </span>
          </div>

          <div class="p-6">
            <div class="flex items-center text-xs text-orange-400 font-semibold mb-1">
              <span><?php echo esc_html($proj['category']); ?></span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2 leading-tight group-hover:text-orange-400 transition-colors">
              <?php echo esc_html($proj['title']); ?>
            </h3>
            <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
              <?php echo esc_html($proj['description']); ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==============================================================
     SECCIÓN NOSOTROS & CONTACTO
     ============================================================== -->
<section id="nosotros" class="py-24 bg-slate-900 border-t border-slate-800/80">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      <div class="space-y-6">
        <span class="text-xs font-bold text-orange-400 uppercase tracking-widest block">Trayectoria &amp; Compromiso</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
          Más de 20 años marcando el estándar en gráfica y competición
        </h2>
        <p class="text-slate-300 text-sm leading-relaxed">
          Nacidos desde la pasión por el automovilismo y el diseño publicitario, en Eiken Design combinamos creatividad visual con rigurosos procesos de aplicación técnica en vinilos automotrices e indumentaria personalizada.
        </p>
        <p class="text-slate-400 text-sm leading-relaxed">
          Trabajamos con las mejores marcas globales como Avery Dennison, 3M y Arlon, asegurando que cada rotulado, estampado o proyecto corporativo resista el paso del tiempo y las condiciones más extremas.
        </p>
      </div>

      <div id="contacto" class="bg-slate-950 border border-slate-800 rounded-3xl p-8 space-y-6">
        <h3 class="text-2xl font-extrabold text-white">¿Listo para impulsar tu imagen?</h3>
        <p class="text-xs text-slate-400">Visítanos en nuestro taller en Los Ángeles o solicita una cotización en línea.</p>
        <div class="space-y-3 text-sm text-slate-300">
          <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
            <span class="text-xs text-orange-400 font-bold uppercase block">Dirección del Taller</span>
            <span class="font-semibold text-white">Calle 2 Sur 106, Los Ángeles, Región del Biobío</span>
          </div>
          <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
            <span class="text-xs text-orange-400 font-bold uppercase block">Horario de Atención</span>
            <span class="font-semibold text-white">Lunes a Viernes de 09:00 a 18:00 hrs</span>
          </div>
        </div>
        <button type="button" onclick="window.eikenOpenQuoteModal()" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm shadow-lg shadow-orange-600/20">
          Abrir Formulario de Cotización
        </button>
      </div>
    </div>
  </div>
</section>

<script>
  // Carrusel del Hero
  (function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.eiken-hero-slide');
    const dots = document.querySelectorAll('.eiken-hero-dot');
    if (slides.length <= 1) return;

    window.eikenSetHeroSlide = function(idx) {
      currentSlide = idx;
      slides.forEach((s, i) => {
        s.classList.toggle('opacity-90', i === idx);
        s.classList.toggle('scale-100', i === idx);
        s.classList.toggle('opacity-0', i !== idx);
        s.classList.toggle('scale-105', i !== idx);
      });
      dots.forEach((d, i) => {
        d.className = 'eiken-hero-dot h-2 rounded-full transition-all duration-300 ' +
          (i === idx ? 'bg-orange-500 w-9 shadow-sm shadow-orange-500/50' : 'bg-white/35 hover:bg-white/60 w-2.5');
      });
    };

    setInterval(() => {
      window.eikenSetHeroSlide((currentSlide + 1) % slides.length);
    }, 5500);
  })();

  // Filtro interactivo de Servicios
  window.eikenCurrentSrvCat = 'all';
  window.eikenSelectServiceCat = function(cat) {
    window.eikenCurrentSrvCat = cat;
    document.querySelectorAll('.eiken-srv-cat-btn').forEach(btn => {
      const active = btn.getAttribute('data-srv-cat') === cat;
      btn.className = 'eiken-srv-cat-btn px-4 py-2 rounded-xl text-xs font-bold tracking-wide transition-all whitespace-nowrap ' +
        (active ? 'bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-md' : 'bg-slate-950 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800');
    });
    window.eikenFilterServices();
  };

  window.eikenFilterServices = function() {
    const q = (document.getElementById('eiken-service-search')?.value || '').toLowerCase();
    const cat = window.eikenCurrentSrvCat || 'all';
    document.querySelectorAll('.eiken-service-card').forEach(card => {
      const matchCat = (cat === 'all') || (card.getAttribute('data-cat') === cat);
      const matchText = !q || (card.getAttribute('data-title') || '').includes(q);
      card.style.display = (matchCat && matchText) ? '' : 'none';
    });
  };
</script>

<?php get_footer(); ?>
