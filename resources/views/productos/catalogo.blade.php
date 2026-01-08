@extends('layouts.app')

@section('content')

<style>
  /* Base layout en tarjeta */
  .producto-card .prod-layout { display:flex; gap:.75rem; }
  .producto-card .prod-img { width:72px; height:72px; flex:0 0 72px; }
  .producto-card .prod-cols { display:block; width:100%; }
  .producto-card .prod-col { margin-bottom:.25rem; }

  /* LIST VIEW */
  .list-view .producto-card { flex: 0 0 100%; max-width: 100%; }
  .list-view .producto-card .card-body { padding: .65rem .9rem; }
  .list-view .producto-card .prod-layout { display:flex; align-items:center; gap:.75rem; }
  .list-view .producto-card .prod-img { display: none !important; }
  .list-view .producto-card .prod-cols { display:flex; flex:1 1 auto; gap:1rem; align-items:center; }
  .list-view .producto-card .prod-col { flex: 0 0 auto; }
  .list-view .producto-card .col-codigo { width: 90px; }
  .list-view .producto-card .col-nombre { flex: 1 1 auto; min-width: 260px; }
  .list-view .producto-card .col-proveedor { width: 180px; }
  .list-view .producto-card .col-precios { width: 210px; text-align:right; }
  .list-view .producto-card .col-actions { width: 140px; text-align:right; }

  /* Encabezado de columnas (solo vista lista) */
  #listHeaderRow { display:none; }
  body.list-view #listHeaderRow { display:flex; }

  /* Botón flotante */
  #floatingCartBtn { position:fixed; right:18px; bottom:18px; z-index:1060; }
</style>



{{-- ======================================================
     MODO EDICIÓN: DATA SEGURA PARA JS
     Debe incluir: id, numero, cliente_id, moneda, afecto_igv, tipo_precio, observaciones, detalles[]
     detalles[]: producto_id, codigo, descripcion, cantidad, precio_unitario, observaciones
====================================================== --}}
{{-- ================= BOTÓN FLOTANTE CARRITO (SIEMPRE) ================= --}}
<button id="floatingCartBtn"
        type="button"
        class="btn btn-success shadow"
        data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasCarrito"
        aria-controls="offcanvasCarrito">
  Carrito <span class="badge bg-light text-dark ms-1" id="cartCountFloat">0</span>
</button>

@if($cotizacionJs)
  <script>
    window.COTIZACION_EDIT = @json($cotizacionJs);
  </script>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Catálogo de Productos</h3>

  @if($cotizacionJs)
    <span class="badge bg-warning text-dark">
      Editando cotización Nº {{ $cotizacionJs['numero'] ?? $cotizacionJs['id'] }}
    </span>
  @endif
</div>

{{-- ================= ACCIONES ================= --}}
<div class="d-flex flex-wrap gap-2 mb-3">
  <button class="btn btn-success" type="button"
      data-bs-toggle="offcanvas"
      data-bs-target="#offcanvasCarrito"
      aria-controls="offcanvasCarrito">
    Ver carrito / Cotización
    <span class="badge bg-light text-dark ms-1" id="cartCount">0</span>
  </button>

  @if($cotizacionJs)
    <a class="btn btn-outline-secondary"
       href="{{ route('cotizaciones.show', $cotizacionJs['id']) }}">
      Volver a la cotización
    </a>
  @endif
</div>

{{-- ================= CLIENTE / CONFIG ================= --}}
<div class="card mb-3">
  <div class="card-body row g-3 align-items-end">

    <div class="col-md-4">
      <label class="form-label">Cliente</label>
      <select class="form-select" id="cliente_id">
        <option value="">Seleccionar cliente</option>
        @foreach($clientes as $c)
          <option value="{{ $c->id }}"
            data-unidad="{{ strtolower($c->unidad_inmobiliaria ?? '') }}"
            @if($cotizacionJs && ($cotizacionJs['cliente_id'] ?? null) == $c->id) selected @endif>
            {{ $c->nombre_cliente }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-md-2">
      <label class="form-label">Tipo de precio</label>
      <select class="form-select" id="tipo_precio">
        <option value="obra">Obra</option>
        <option value="edificio">Edificio</option>
      </select>
      <div class="form-text">Se sugiere según el cliente (Obra/Edificio).</div>
    </div>

    <div class="col-md-2">
      <label class="form-label">Moneda</label>
      <select class="form-select" id="moneda">
        <option value="PEN" @if($cotizacionJs && ($cotizacionJs['moneda'] ?? '') === 'PEN') selected @endif>PEN</option>
        <option value="USD" @if($cotizacionJs && ($cotizacionJs['moneda'] ?? '') === 'USD') selected @endif>USD</option>
      </select>
    </div>

    <div class="col-md-2 pt-4">
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="afecto_igv"
          {{ !$cotizacionJs || ($cotizacionJs['afecto_igv'] ?? true) ? 'checked' : '' }}>
        <label class="form-check-label">Afecto IGV</label>
      </div>
    </div>

  </div>
</div>

{{-- ================= FILTROS ================= --}}
<div class="card mb-3">
  <div class="card-body row g-2">
    <div class="col-md-6">
      <input class="form-control" id="buscador"
             placeholder="Buscar por código, nombre o descripción"
             value="{{ $q ?? '' }}">
    </div>
    
    <div class="col-md-3 d-flex align-items-center justify-content-md-end">
      <div class="form-check form-switch mt-2 mt-md-0">
        <input class="form-check-input" type="checkbox" id="toggleListView">
        <label class="form-check-label" for="toggleListView">Vista lista (sin imágenes)</label>
      </div>
    </div>

    <div class="col-md-3">
      <select class="form-select" id="filtroProveedor">
        <option value="">Todos los proveedores</option>
        @foreach($proveedores as $p)
          <option value="{{ $p }}" @selected(($proveedor ?? '') === $p)>{{ $p }}</option>
        @endforeach
      </select>
    </div>
  </div>
</div>


{{-- ================= REQUIERE CLIENTE ================= --}}
<div id="clienteRequiredMsg" class="alert alert-warning mb-3" style="display:none;">
  Para agregar productos y generar una cotización, primero selecciona un <strong>cliente</strong>.
</div>

{{-- ================= PRODUCTOS ================= --}}

{{-- Encabezado solo para vista lista --}}
<div id="listHeaderRow" class="card mb-2">
  <div class="card-body py-2 small text-muted">
    <div class="prod-cols" style="display:flex; gap:1rem; align-items:center;">
      <div class="prod-col col-codigo"><strong>Código</strong></div>
      <div class="prod-col col-nombre"><strong>Producto</strong></div>
      <div class="prod-col col-proveedor"><strong>Proveedor</strong></div>
      <div class="prod-col col-precios"><strong>Precios</strong></div>
      <div class="prod-col col-actions"><strong>Acciones</strong></div>
    </div>
  </div>
</div>

<div class="row g-3" id="productosRow">
@foreach($productos as $prod)
  <div class="col-md-4 producto-card"
      data-id="{{ $prod->id }}"
      data-codigo="{{ $prod->codigo }}"
      data-nombre="{{ $prod->nombre_producto }}"
      data-descripcion="{{ $prod->descripcion ?? '' }}"
      data-proveedor="{{ $prod->nombre_proveedor ?? '' }}"
      data-precio-obra="{{ (float)($prod->precio_obra ?? $prod->precio ?? 0) }}"
      data-precio-edificio="{{ (float)($prod->precio_edificio ?? $prod->precio ?? 0) }}"
      data-imagen="{{ $prod->imagen_url ?? '' }}">

    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <div class="prod-layout">
          <div class="prod-img rounded border bg-white overflow-hidden">
            @if($prod->imagen_url)
              <img src="{{ $prod->imagen_url }}" alt="{{ $prod->codigo }}" style="width:72px;height:72px;object-fit:cover">
            @else
              <div class="d-flex align-items-center justify-content-center h-100 text-muted small">Sin foto</div>
            @endif
          </div>

          <div class="prod-cols">
            <div class="prod-col col-codigo small text-muted">{{ $prod->codigo }}</div>

            <div class="prod-col col-nombre">
              <div class="fw-semibold">{{ $prod->nombre_producto }}</div>
              @if($prod->descripcion)
                <div class="small mt-1">{{ $prod->descripcion }}</div>
              @endif
            </div>

            <div class="prod-col col-proveedor small">
              <span class="text-muted">{{ $prod->nombre_proveedor ? 'Proveedor' : '' }}</span>
              {{ $prod->nombre_proveedor }}
            </div>

            <div class="prod-col col-precios small text-end">
              <div><span class="text-muted">Obra:</span> <strong>{{ number_format($prod->precio_obra ?? $prod->precio,2) }}</strong></div>
              <div><span class="text-muted">Edificio:</span> <strong>{{ number_format($prod->precio_edificio ?? $prod->precio,2) }}</strong></div>
            </div>

            <div class="prod-col col-actions d-flex gap-2 justify-content-end">
              <button class="btn btn-sm btn-primary btnAgregar" type="button">Agregar</button>
              <button class="btn btn-sm btn-outline-secondary btnAgregar10" type="button">+10</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
@endforeach
</div>

{{-- ================= OFFCANVAS CARRITO ================= --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCarrito" aria-labelledby="offcanvasCarritoLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasCarritoLabel">
      {{ $cotizacionJs ? 'Editar cotización' : 'Nueva cotización' }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">

    <div class="alert alert-warning py-2 small" id="cartWarn" style="display:none"></div>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>Producto</th>
            <th style="width:80px" class="text-end">Cant.</th>
            <th style="width:95px" class="text-end">P. Unit.</th>
            <th style="width:28px"></th>
          </tr>
        </thead>
        <tbody id="cartBody"></tbody>
      </table>
    </div>

    <div class="mb-3">
      <label class="form-label">Observaciones generales (opcional)</label>
      <textarea class="form-control" rows="3" id="obsGeneral" placeholder="Ej: Entrega en obra / coordinación previa..."></textarea>
      <div class="form-text">Se imprimen en el PDF. Para observaciones por item, edítalas en la tabla (texto rojo).</div>
    </div>

    <div class="card mb-3">
      <div class="card-body small">
        <div class="d-flex justify-content-between"><span>Subtotal</span><strong id="sumSubtotal">0.00</strong></div>
        <div class="d-flex justify-content-between"><span>IGV</span><strong id="sumIgv">0.00</strong></div>
        <hr class="my-2">
        <div class="d-flex justify-content-between"><span>Total</span><strong id="sumTotal">0.00</strong></div>
      </div>
    </div>

    <form id="formCotizacion" method="POST"
      action="{{ $cotizacionJs ? route('cotizaciones.update_from_catalog', $cotizacionJs['id']) : route('cotizaciones.store_from_catalog') }}">
      @csrf
      @if($cotizacionJs) @method('PUT') @endif

      <input type="hidden" name="cliente_id" id="f_cliente_id">
      <input type="hidden" name="tipo_precio" id="f_tipo_precio">
      <input type="hidden" name="moneda" id="f_moneda">
      <input type="hidden" name="afecto_igv" id="f_afecto_igv">
      <input type="hidden" name="observaciones" id="f_observaciones">
      <div id="f_items"></div>

      <button class="btn btn-success w-100" type="submit" id="btnGuardar">
        {{ $cotizacionJs ? 'Guardar cambios' : 'Generar cotización' }}
      </button>
      <div class="small text-muted mt-2">
        Al aprobar la cotización y generar guía ya no se podrá editar.
      </div>
    </form>

  </div>
</div>

@endsection

@push('scripts')
<script>
(() => {
  // -------------------------------
  // Helpers
  // -------------------------------
  const money = (n) => (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2);
  const sym = () => (document.getElementById('moneda').value === 'USD' ? '$' : 'S/');

  // -------------------------------
  // DOM refs
  // -------------------------------
  const cartCount = document.getElementById('cartCount');
  const cartBody  = document.getElementById('cartBody');
  const sumSubtotal = document.getElementById('sumSubtotal');
  const sumIgv = document.getElementById('sumIgv');
  const sumTotal = document.getElementById('sumTotal');
  const warn = document.getElementById('cartWarn');

  const cartCountFloat = document.getElementById('cartCountFloat');
  const toggleListView = document.getElementById('toggleListView');
  const productosRow = document.getElementById('productosRow');
  const clienteRequiredMsg = document.getElementById('clienteRequiredMsg');
  const buscador = document.getElementById('buscador');
  const filtroProveedor = document.getElementById('filtroProveedor');

  const selCliente = document.getElementById('cliente_id');
  const selTipo = document.getElementById('tipo_precio');

  // -------------------------------
  // State
  // -------------------------------
  let cart = []; // {id,codigo,nombre,cantidad,precio,obs}

  // -------------------------------
  // Reglas UX: requiere cliente antes de agregar productos
  // -------------------------------
  const applyClienteState = () => {
    const hasCliente = !!(selCliente && selCliente.value);
    if (clienteRequiredMsg) clienteRequiredMsg.style.display = hasCliente ? 'none' : 'block';

    if (productosRow) {
      productosRow.style.opacity = hasCliente ? '1' : '0.5';
      productosRow.style.pointerEvents = hasCliente ? 'auto' : 'none';
    }

    if (buscador) {
      buscador.disabled = !hasCliente;
      buscador.placeholder = hasCliente ? 'Buscar producto (código / nombre / descripción)' : 'Selecciona un cliente para habilitar la búsqueda';
    }
  };

  // -------------------------------
  // Vista lista (sin imágenes)
  // -------------------------------
  const applyListView = (enabled) => {
    document.body.classList.toggle('list-view', !!enabled);
    if (toggleListView) toggleListView.checked = !!enabled;
    try { localStorage.setItem('vyg_list_view', enabled ? '1' : '0'); } catch (e) {}
  };

  // -------------------------------
  // Filtros (cliente-side + URL params)
  // -------------------------------
  const norm = (s) => String(s || '').toLowerCase().trim();

  const applyFilters = () => {
  if (!productosRow) return;

  const txt = norm(buscador?.value);
  const prov = norm(filtroProveedor?.value);
  const tipo = selTipo.value; // obra | edificio

  const cards = productosRow.querySelectorAll('.producto-card');

  cards.forEach(card => {
    const haystack = norm(`${card.dataset.codigo} ${card.dataset.nombre} ${card.dataset.descripcion}`);
    const cardProv = norm(card.dataset.proveedor);

    // 🔹 PRECIOS
    const precioObra = parseFloat(card.dataset.precioObra || '0');
    const precioEdificio = parseFloat(card.dataset.precioEdificio || '0');

    // 🔹 FILTRO POR TEXTO / PROVEEDOR
    const okTxt = !txt || haystack.includes(txt);
    const okProv = !prov || cardProv === prov;

    // 🔹 FILTRO POR TIPO DE CLIENTE
    let okTipo = true;
    if (tipo === 'obra') {
      okTipo = precioObra > 0;
    }
    if (tipo === 'edificio') {
      okTipo = precioEdificio > 0;
    }

    card.style.display = (okTxt && okProv && okTipo) ? '' : 'none';
  });
};

  const applyTipoPrecioFilter = () => {
  const tipo = selTipo.value;
  const cards = document.querySelectorAll('.producto-card');

  cards.forEach(card => {
    const precioObra = parseFloat(card.dataset.precioObra || '0');
    const precioEdif = parseFloat(card.dataset.precioEdificio || '0');

    let visible = true;

    if (tipo === 'edificio') {
      visible = precioEdif > 0;
    } else {
      visible = precioObra > 0;
    }

    card.style.display = visible ? '' : 'none';
  });
};


    // Mantener URL en sync (sin recargar)
    try {
      const url = new URL(window.location.href);
      if (txt) url.searchParams.set('q', buscador.value);
      else url.searchParams.delete('q');
      if (prov) url.searchParams.set('proveedor', filtroProveedor.value);
      else url.searchParams.delete('proveedor');
      window.history.replaceState({}, '', url);
    } catch (e) {}
  };

  const debounce = (fn, ms=250) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  };

  const applyFiltersDebounced = debounce(applyFilters, 250);

  // -------------------------------
  // Precio unitario desde tarjeta
  // -------------------------------
  const getUnitPriceFromCard = (productoId) => {
    const tipo = selTipo.value;
    const card = document.querySelector(`.producto-card[data-id="${productoId}"]`);
    if (!card) return 0;
    const p = (tipo === 'edificio') ? card.dataset.precioEdificio : card.dataset.precioObra;
    return parseFloat(p || '0');
  };

  // -------------------------------
  // Sugerir tipo_precio por cliente
  // -------------------------------
  const suggestTipoByCliente = () => {
    const opt = selCliente.options[selCliente.selectedIndex];
    const unidad = (opt?.dataset?.unidad || '').toLowerCase();
    if (unidad.includes('edif')) selTipo.value = 'edificio';
    else if (unidad.includes('obra')) selTipo.value = 'obra';
  };

  // -------------------------------
  // Recalcular precios según tipo
  // (Sí aplica también en edición)
  // -------------------------------
  const syncPricesToTipo = () => {
    cart = cart.map(it => ({ ...it, precio: getUnitPriceFromCard(it.id) }));
  };

  // -------------------------------
  // Totales
  // -------------------------------
  const totals = () => {
    const subtotal = cart.reduce((acc, it) => acc + (it.cantidad * it.precio), 0);
    const afecto = document.getElementById('afecto_igv').checked;
    const igv = afecto ? (subtotal * 0.18) : 0;
    const total = subtotal + igv;
    return { subtotal, igv, total };
  };

  // -------------------------------
  // Render
  // -------------------------------
  const renderCart = () => {
    const count = cart.reduce((a, i) => a + i.cantidad, 0);
    cartCount.textContent = String(count);
    if (cartCountFloat) cartCountFloat.textContent = String(count);

    cartBody.innerHTML = '';

    cart.forEach((it, idx) => {
      const tr = document.createElement('tr');

      // Campo de observación seguro (sin inyectar HTML)
      const obsValue = (it.obs || '');

      tr.innerHTML = `
        <td>
          <div class="fw-semibold small"></div>
          <div class="text-muted small"></div>
          <input class="form-control form-control-sm mt-1 text-danger"
                 placeholder="Obs. (se imprime en rojo)"
                 data-idx="${idx}" data-role="obs">
        </td>
        <td class="text-end">
          <input type="number" min="1" class="form-control form-control-sm text-end"
                 data-idx="${idx}" data-role="qty">
        </td>
        <td class="text-end small">
          <div class="unit"></div>
          <div class="text-muted line"></div>
        </td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-danger" type="button" data-idx="${idx}" data-role="del">×</button>
        </td>
      `;

      tr.querySelector('.fw-semibold').textContent = it.nombre;
      tr.querySelector('.text-muted').textContent = it.codigo || '';
      tr.querySelector('input[data-role="obs"]').value = obsValue;

      const qtyInput = tr.querySelector('input[data-role="qty"]');
      qtyInput.value = String(it.cantidad);

      tr.querySelector('.unit').textContent = `${sym()} ${money(it.precio)}`;
      tr.querySelector('.line').textContent = `${sym()} ${money(it.precio * it.cantidad)}`;

      cartBody.appendChild(tr);
    });

    const t = totals();
    sumSubtotal.textContent = `${sym()} ${money(t.subtotal)}`;
    sumIgv.textContent = `${sym()} ${money(t.igv)}`;
    sumTotal.textContent = `${sym()} ${money(t.total)}`;

    if (!selCliente.value) {
      warn.style.display = '';
      warn.textContent = 'Selecciona un cliente antes de generar/guardar la cotización.';
    } else if (cart.length === 0) {
      warn.style.display = '';
      warn.textContent = 'Agrega al menos un producto al carrito.';
    } else {
      warn.style.display = 'none';
      warn.textContent = '';
    }
  };

  // -------------------------------
  // Precarga edición (CORREGIDA)
  // -------------------------------
  const preloadEdit = () => {
    if (!window.COTIZACION_EDIT) return;

    // Set tipo_precio desde cotización
    selTipo.value = window.COTIZACION_EDIT.tipo_precio || 'obra';

    // Observación general
    document.getElementById('obsGeneral').value = window.COTIZACION_EDIT.observaciones || '';

    // Precargar desde detalles (no items)
    const detalles = window.COTIZACION_EDIT.detalles || [];

    cart = detalles.map(d => ({
      id: String(d.producto_id), // IMPORTANTE: debe ser el ID del producto
      codigo: d.codigo,
      nombre: d.descripcion, // en tu tabla guardas descripcion
      cantidad: parseInt(d.cantidad || 1, 10),
      // precio_unitario viene guardado, pero si cambias tipo_precio, luego recalculamos desde card
      precio: parseFloat(d.precio_unitario || 0),
      obs: d.observaciones || ''
    }));

    // Asegurar que cliente y moneda estén seteados (ya vienen del Blade selected, pero por si acaso)
    if (window.COTIZACION_EDIT.moneda) document.getElementById('moneda').value = window.COTIZACION_EDIT.moneda;
    document.getElementById('afecto_igv').checked = !!window.COTIZACION_EDIT.afecto_igv;

    // Sincronizar precios con el tipo seleccionado (usa precios actuales del catálogo)
    syncPricesToTipo();
  };

  // -------------------------------
  // Events: tabla carrito
  // -------------------------------
  cartBody.addEventListener('input', (e) => {
    const role = e.target.dataset.role;
    const idx = parseInt(e.target.dataset.idx || '-1', 10);
    if (idx < 0 || !cart[idx]) return;

    if (role === 'qty') {
      const v = Math.max(1, parseInt(e.target.value || '1', 10));
      cart[idx].cantidad = v;
      renderCart();
    }
    if (role === 'obs') {
      cart[idx].obs = e.target.value;
    }
  });

  cartBody.addEventListener('click', (e) => {
    const role = e.target.dataset.role;
    const idx = parseInt(e.target.dataset.idx || '-1', 10);
    if (role === 'del' && idx >= 0) {
      cart.splice(idx, 1);
      renderCart();
    }
  });

  // -------------------------------
  // Events: cliente / tipo / moneda / igv
  // -------------------------------
  if (!window.COTIZACION_EDIT) {
    suggestTipoByCliente();
  }

  // Inicial
  applyClienteState();
  applyListView((() => { try { return localStorage.getItem('vyg_list_view') === '1'; } catch(e) { return false; } })());
  applyFilters();

  if (toggleListView) {
    toggleListView.addEventListener('change', () => applyListView(toggleListView.checked));
  }

  if (buscador) {
    buscador.addEventListener('input', applyFiltersDebounced);
    buscador.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); applyFilters(); }
    });
  }

  if (filtroProveedor) {
    filtroProveedor.addEventListener('change', applyFilters);
  }

  selCliente.addEventListener('change', () => {
    if (!window.COTIZACION_EDIT) {
      suggestTipoByCliente();
    }

    applyClienteState();
    applyTipoPrecioFilter();
    syncPricesToTipo();
    renderCart();
  });


  document.getElementById('moneda').addEventListener('change', renderCart);
  document.getElementById('afecto_igv').addEventListener('change', renderCart);

  selTipo.addEventListener('change', () => {
    applyTipoPrecioFilter();
    syncPricesToTipo();
    renderCart();
  });



  // -------------------------------
  // Events: Agregar / +10
  // -------------------------------
  document.querySelectorAll('.btnAgregar').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!selCliente.value) {
        applyClienteState();
        if (clienteRequiredMsg) clienteRequiredMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        selCliente.focus();
        return;
      }
      const card = e.target.closest('.producto-card');
      const id = String(card.dataset.id);
      const codigo = card.dataset.codigo;
      const nombre = card.dataset.nombre;
      const precio = getUnitPriceFromCard(id);

      const item = cart.find(i => i.id === id);
      if (item) item.cantidad += 1;
      else cart.push({ id, codigo, nombre, precio, cantidad: 1, obs: '' });

      renderCart();
    });
  });

  document.querySelectorAll('.btnAgregar10').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!selCliente.value) {
        applyClienteState();
        if (clienteRequiredMsg) clienteRequiredMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        selCliente.focus();
        return;
      }
      const card = e.target.closest('.producto-card');
      const id = String(card.dataset.id);
      const codigo = card.dataset.codigo;
      const nombre = card.dataset.nombre;
      const precio = getUnitPriceFromCard(id);

      const item = cart.find(i => i.id === id);
      if (item) item.cantidad += 10;
      else cart.push({ id, codigo, nombre, precio, cantidad: 10, obs: '' });

      renderCart();
    });
  });

  // -------------------------------
  // Submit: construir inputs (SEGURO)
  // -------------------------------
  const form = document.getElementById('formCotizacion');
  form.addEventListener('submit', (e) => {
    const clienteId = selCliente.value;

    if (!clienteId || cart.length === 0) {
      e.preventDefault();
      renderCart();
      return;
    }

    document.getElementById('f_cliente_id').value = clienteId;
    document.getElementById('f_tipo_precio').value = selTipo.value;
    document.getElementById('f_moneda').value = document.getElementById('moneda').value;
    document.getElementById('f_afecto_igv').value = document.getElementById('afecto_igv').checked ? '1' : '0';
    document.getElementById('f_observaciones').value = document.getElementById('obsGeneral').value || '';

    const itemsDiv = document.getElementById('f_items');
    itemsDiv.innerHTML = '';

    cart.forEach((it, i) => {
      const p1 = document.createElement('input');
      p1.type = 'hidden';
      p1.name = `items[${i}][producto_id]`;
      p1.value = it.id;
      itemsDiv.appendChild(p1);

      const p2 = document.createElement('input');
      p2.type = 'hidden';
      p2.name = `items[${i}][cantidad]`;
      p2.value = String(it.cantidad);
      itemsDiv.appendChild(p2);

      const p3 = document.createElement('input');
      p3.type = 'hidden';
      p3.name = `items[${i}][observaciones]`;
      p3.value = it.obs || '';
      itemsDiv.appendChild(p3);
    });
  });

  // -------------------------------
  // INIT
  // -------------------------------
  preloadEdit();
  syncPricesToTipo();
  applyTipoPrecioFilter();
  renderCart();

})();
</script>
@endpush
