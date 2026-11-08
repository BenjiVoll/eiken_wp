<?php
$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/?post_type=product');
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/?page_id=7');
$checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/?page_id=8');
$ajax_url = admin_url('admin-ajax.php');
?>
  <!-- ==============================================================
       FOOTER OFICIAL EIKEN DESIGN
       ============================================================== -->
  <footer class="bg-slate-950 border-t border-slate-800/80 text-slate-400 pt-16 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800/80">
        <!-- Marca -->
        <div class="space-y-4">
          <div class="flex items-center space-x-3">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/logo.png'); ?>" alt="Eiken Design" class="h-9 w-auto rounded" />
            <span class="text-white font-extrabold text-lg tracking-tight">EIKEN <span class="text-orange-500">DESIGN</span></span>
          </div>
          <p class="text-xs text-slate-400 leading-relaxed">
            Estudio profesional de diseño publicitario, rotulado vehicular, gráfica de competición e indumentaria personalizada en Chile.
          </p>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[11px] text-orange-400 font-semibold">
            <span>⚡ Motorizado por WordPress + WooCommerce + Webpay Plus</span>
          </div>
        </div>

        <!-- Navegación -->
        <div>
          <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Navegación</h4>
          <ul class="space-y-2.5 text-sm">
            <li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-orange-400 transition-colors">Inicio</a></li>
            <li><a href="<?php echo esc_url($shop_url); ?>" class="hover:text-orange-400 transition-colors">Tienda Online (WooCommerce)</a></li>
            <li><a href="<?php echo esc_url(home_url('/#servicios')); ?>" class="hover:text-orange-400 transition-colors">Catálogo de Servicios</a></li>
            <li><a href="<?php echo esc_url(home_url('/#portafolio')); ?>" class="hover:text-orange-400 transition-colors">Portafolio de Proyectos</a></li>
            <li><a href="<?php echo esc_url(admin_url()); ?>" class="hover:text-orange-400 transition-colors">Administración (WP-Admin)</a></li>
          </ul>
        </div>

        <!-- Divisiones -->
        <div>
          <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Nuestras Divisiones</h4>
          <ul class="space-y-2.5 text-sm">
            <li class="text-slate-300 font-medium">Eiken Design <span class="text-xs text-slate-500 block">Publicidad e Identidad Visual</span></li>
            <li class="text-slate-300 font-medium">Eiken Racing Design <span class="text-xs text-slate-500 block">Gráfica de Competición &amp; Rally</span></li>
            <li class="text-slate-300 font-medium">Eiken Truck Design <span class="text-xs text-slate-500 block">Rotulado de Flotas &amp; Transporte</span></li>
          </ul>
        </div>

        <!-- Contacto -->
        <div>
          <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Taller &amp; Contacto</h4>
          <ul class="space-y-3 text-xs text-slate-300">
            <li class="flex items-start gap-2.5">
              <span class="text-orange-500 font-bold">📍</span>
              <span>Calle 2 Sur 106, Los Ángeles, Región del Biobío, Chile</span>
            </li>
            <li class="flex items-center gap-2.5">
              <span class="text-orange-500 font-bold">🕒</span>
              <span>Lunes a Viernes: 09:00 – 18:00 hrs</span>
            </li>
            <li class="flex items-center gap-2.5">
              <span class="text-orange-500 font-bold">💳</span>
              <span>Pagos seguros vía Transbank Webpay Plus</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
        <p>&copy; <?php echo date('Y'); ?> Eiken Design. Todos los derechos reservados.</p>
        <p>Entorno completo WordPress + WooCommerce (`eiken_wp`)</p>
      </div>
    </div>
  </footer>

  <!-- ==============================================================
       SLIDE-OVER CART DRAWER (CARRITO LATERAL INTERACTIVO)
       ============================================================== -->
  <div id="eiken-cart-drawer" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-slate-950/75 backdrop-blur-sm transition-opacity" onclick="window.eikenCloseCartDrawer()"></div>
    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
      <div class="w-screen max-w-md bg-white text-slate-900 shadow-2xl flex flex-col justify-between">
        <!-- Header del Drawer -->
        <div class="p-6 bg-slate-950 text-white flex items-center justify-between border-b border-slate-800">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-orange-600/20 border border-orange-500/30 flex items-center justify-center text-orange-400 font-bold">
              🛒
            </div>
            <div>
              <h3 class="text-base font-extrabold tracking-tight">Tu Carrito de Compras</h3>
              <p class="text-xs text-slate-400">Tienda Oficial WooCommerce</p>
            </div>
          </div>
          <button type="button" onclick="window.eikenCloseCartDrawer()" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
            ✕
          </button>
        </div>

        <!-- Lista de items -->
        <div id="eiken-cart-items-container" class="flex-1 overflow-y-auto p-6 space-y-4">
          <div class="text-center py-12 text-slate-400 text-sm">Cargando carrito...</div>
        </div>

        <!-- Footer del Drawer -->
        <div id="eiken-cart-footer" class="p-6 bg-slate-50 border-t border-slate-200 space-y-4">
          <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-600">Subtotal estimado</span>
            <span id="eiken-cart-subtotal" class="text-2xl font-black text-slate-900">$0</span>
          </div>
          <p class="text-xs text-slate-500">
            Pago seguro con tarjetas de Débito (Redcompra) y Crédito vía <strong>Transbank Webpay Plus</strong>.
          </p>
          <div class="grid grid-cols-1 gap-2.5">
            <a
              href="<?php echo esc_url($checkout_url); ?>"
              id="eiken-cart-checkout-btn"
              class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white font-bold text-sm text-center shadow-lg shadow-orange-600/25 transition-all"
            >
              Ir a Pagar con Webpay Plus →
            </a>
            <a
              href="<?php echo esc_url($cart_url); ?>"
              class="w-full py-2.5 px-4 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs text-center transition-colors"
            >
              Ver Carrito Detallado
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ==============================================================
       MODAL DE COTIZACIÓN DE SERVICIOS Y PROYECTOS
       ============================================================== -->
  <div id="eiken-quote-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" onclick="window.eikenCloseQuoteModal()"></div>
    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="relative w-full max-w-xl rounded-3xl bg-slate-900 border border-slate-800 text-white shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 px-8 py-6 flex items-center justify-between">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-widest text-orange-100 block">Atención Personalizada</span>
            <h3 class="text-xl font-extrabold tracking-tight text-white">Solicitar Cotización</h3>
          </div>
          <button type="button" onclick="window.eikenCloseQuoteModal()" class="p-2 rounded-full bg-black/20 hover:bg-black/40 text-white transition-colors">
            ✕
          </button>
        </div>

        <form id="eiken-quote-form" onsubmit="window.eikenSubmitQuote(event)" class="p-8 space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nombre Completo *</label>
              <input type="text" name="clientName" required placeholder="Ej: Juan Pérez" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:outline-none focus:border-orange-500" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Correo Electrónico *</label>
              <input type="email" name="clientEmail" required placeholder="contacto@empresa.cl" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:outline-none focus:border-orange-500" />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Teléfono / WhatsApp *</label>
              <input type="text" name="clientPhone" required placeholder="+56 9 1234 5678" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:outline-none focus:border-orange-500" />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1.5">Empresa (Opcional)</label>
              <input type="text" name="company" placeholder="Nombre de tu empresa" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:outline-none focus:border-orange-500" />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Servicio o División de Interés *</label>
            <input type="text" id="eiken-quote-service-input" name="serviceName" required placeholder="Ej: Wrap Vehicular Completo / Gráfica de Competición" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:outline-none focus:border-orange-500" />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1.5">Detalles del Proyecto (Medidas, modelo de vehículo, cantidad) *</label>
            <textarea name="description" rows="3" required placeholder="Cuéntanos qué deseas realizar..." class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-white text-sm focus:outline-none focus:border-orange-500"></textarea>
          </div>

          <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-800">
            <button type="button" onclick="window.eikenCloseQuoteModal()" class="px-5 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-sm font-semibold">
              Cancelar
            </button>
            <button type="submit" id="eiken-quote-submit-btn" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-500 hover:to-red-500 text-white text-sm font-bold shadow-lg shadow-orange-600/25">
              Enviar Solicitud
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ==============================================================
       MODAL LIGHTBOX DE IMAGEN EN ALTA RESOLUCIÓN
       ============================================================== -->
  <div id="eiken-lightbox-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/90 backdrop-blur-md p-4" onclick="window.eikenCloseLightbox()">
    <button type="button" class="absolute top-6 right-6 text-white bg-white/10 hover:bg-orange-600 rounded-full p-3 transition-colors">✕</button>
    <img id="eiken-lightbox-img" src="" alt="Vista ampliada" class="max-h-[88vh] max-w-[92vw] object-contain rounded-2xl shadow-2xl border border-white/10" onclick="event.stopPropagation()" />
  </div>

  <!-- ==============================================================
       BOTÓN FLOTANTE DE WHATSAPP
       ============================================================== -->
  <a
    href="https://wa.me/56900000000?text=Hola%20Eiken%20Design,%20quisiera%20consultar%20por%20un%20producto%20o%20servicio"
    target="_blank"
    rel="noopener noreferrer"
    class="fixed bottom-6 right-6 z-40 flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-full shadow-xl shadow-emerald-500/25 hover:scale-105 transition-all font-bold text-sm"
    title="Hablar por WhatsApp"
  >
    <span>💬 WhatsApp</span>
  </a>

  <!-- ==============================================================
       SCRIPTS GLOBALES INTERACTIVOS (CARRITO AJAX, COTIZACIONES Y MODALES)
       ============================================================== -->
  <script>
    window.EIKEN_AJAX_URL = <?php echo json_encode($ajax_url); ?>;

    window.eikenRenderCart = function(data) {
      const badge = document.getElementById('eiken-header-cart-badge');
      if (badge) {
        badge.textContent = data.count || 0;
        if (data.count > 0) {
          badge.classList.remove('hidden');
          badge.classList.add('flex');
        } else {
          badge.classList.add('hidden');
          badge.classList.remove('flex');
        }
      }

      const subtotalEl = document.getElementById('eiken-cart-subtotal');
      if (subtotalEl) subtotalEl.textContent = data.subtotal || '$0';

      const container = document.getElementById('eiken-cart-items-container');
      const footer = document.getElementById('eiken-cart-footer');
      if (!container) return;

      if (!data.items || data.items.length === 0) {
        container.innerHTML = `
          <div class="text-center py-16 space-y-3">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">🛍️</div>
            <p class="text-slate-700 font-bold text-base">Tu carrito está vacío</p>
            <p class="text-slate-400 text-xs max-w-xs mx-auto">Agrega poleras, polerones o stickers desde nuestra tienda oficial.</p>
          </div>
        `;
        if (footer) footer.classList.add('opacity-50', 'pointer-events-none');
        return;
      }

      if (footer) footer.classList.remove('opacity-50', 'pointer-events-none');

      container.innerHTML = data.items.map(item => `
        <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
          <img src="${item.image}" alt="${item.name}" class="w-16 h-16 rounded-xl object-cover bg-white border border-slate-200 flex-shrink-0" />
          <div class="flex-1 min-w-0">
            <h4 class="text-sm font-bold text-slate-900 truncate">${item.name}</h4>
            ${item.size ? `<span class="inline-block text-[11px] font-semibold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md mt-0.5">Talla / Formato: ${item.size}</span>` : ''}
            <div class="text-xs text-slate-500 mt-1">${item.priceFormatted} c/u</div>
            <div class="flex items-center justify-between mt-2">
              <div class="inline-flex items-center rounded-lg border border-slate-300 bg-white">
                <button type="button" onclick="window.eikenUpdateCartQty('${item.key}', ${item.qty - 1})" class="px-2.5 py-0.5 text-slate-600 hover:text-orange-600 font-bold">-</button>
                <span class="px-2 text-xs font-bold text-slate-900">${item.qty}</span>
                <button type="button" onclick="window.eikenUpdateCartQty('${item.key}', ${item.qty + 1})" class="px-2.5 py-0.5 text-slate-600 hover:text-orange-600 font-bold">+</button>
              </div>
              <span class="text-sm font-extrabold text-slate-900">${item.lineTotalFormatted}</span>
            </div>
          </div>
          <button type="button" onclick="window.eikenUpdateCartQty('${item.key}', 0)" class="text-slate-400 hover:text-red-600 p-1" title="Eliminar">✕</button>
        </div>
      `).join('');
    };

    window.eikenFetchCart = function() {
      const body = new URLSearchParams({ action: 'eiken_get_cart' });
      fetch(window.EIKEN_AJAX_URL, { method: 'POST', body })
        .then(r => r.json())
        .then(res => { if (res.success) window.eikenRenderCart(res.data); });
    };

    window.eikenOpenCartDrawer = function() {
      document.getElementById('eiken-cart-drawer').classList.remove('hidden');
      window.eikenFetchCart();
    };

    window.eikenCloseCartDrawer = function() {
      document.getElementById('eiken-cart-drawer').classList.add('hidden');
    };

    window.eikenAddToCart = function(productId, qty = 1, size = '') {
      const body = new URLSearchParams({
        action: 'eiken_add_to_cart',
        product_id: productId,
        quantity: qty,
        size: size
      });
      fetch(window.EIKEN_AJAX_URL, { method: 'POST', body })
        .then(r => r.json())
        .then(res => {
          if (res.success) {
            window.eikenRenderCart(res.data);
            window.eikenOpenCartDrawer();
            if (window.Swal) {
              Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '¡Producto agregado al carrito!',
                showConfirmButton: false,
                timer: 2200
              });
            }
          } else {
            if (window.Swal) {
              Swal.fire({ icon: 'warning', title: 'Atención', text: res.data?.message || 'No se pudo agregar el producto.' });
            }
          }
        });
    };

    window.eikenUpdateCartQty = function(cartKey, qty) {
      const body = new URLSearchParams({
        action: 'eiken_update_cart_qty',
        cart_key: cartKey,
        quantity: qty
      });
      fetch(window.EIKEN_AJAX_URL, { method: 'POST', body })
        .then(r => r.json())
        .then(res => { if (res.success) window.eikenRenderCart(res.data); });
    };

    window.eikenOpenQuoteModal = function(serviceName = '') {
      const modal = document.getElementById('eiken-quote-modal');
      const input = document.getElementById('eiken-quote-service-input');
      if (input) input.value = serviceName || '';
      if (modal) modal.classList.remove('hidden');
    };

    window.eikenCloseQuoteModal = function() {
      const modal = document.getElementById('eiken-quote-modal');
      if (modal) modal.classList.add('hidden');
    };

    window.eikenSubmitQuote = function(e) {
      e.preventDefault();
      const form = document.getElementById('eiken-quote-form');
      const btn = document.getElementById('eiken-quote-submit-btn');
      const formData = new FormData(form);
      formData.append('action', 'eiken_submit_quote');
      if (btn) { btn.disabled = true; btn.textContent = 'Enviando...'; }

      fetch(window.EIKEN_AJAX_URL, { method: 'POST', body: new URLSearchParams(formData) })
        .then(r => r.json())
        .then(res => {
          if (btn) { btn.disabled = false; btn.textContent = 'Enviar Solicitud'; }
          if (res.success) {
            form.reset();
            window.eikenCloseQuoteModal();
            if (window.Swal) {
              Swal.fire({ icon: 'success', title: '¡Solicitud Enviada!', text: res.data.message, confirmButtonColor: '#ea580c' });
            }
          } else {
            if (window.Swal) {
              Swal.fire({ icon: 'error', title: 'Error', text: res.data?.message || 'Ocurrió un problema.' });
            }
          }
        });
    };

    window.eikenOpenLightbox = function(imgUrl) {
      const modal = document.getElementById('eiken-lightbox-modal');
      const img = document.getElementById('eiken-lightbox-img');
      if (img && modal) {
        img.src = imgUrl;
        modal.classList.remove('hidden');
      }
    };

    window.eikenCloseLightbox = function() {
      const modal = document.getElementById('eiken-lightbox-modal');
      if (modal) modal.classList.add('hidden');
    };
  </script>

  <?php wp_footer(); ?>
</body>
</html>
