@extends('layouts.app')

@section('content')
<h3>{{ $cotizacion ? 'Editar Cotización' : 'Nueva Cotización' }}</h3>

<form method="POST"
      action="{{ $cotizacion
          ? route('cotizaciones.update_from_catalog', $cotizacion)
          : route('cotizaciones.store_from_catalog') }}"
      class="mt-3"
      id="cot-form">

  @csrf
  @if($cotizacion)
      @method('PUT')
  @endif

  @include('cotizaciones.form', ['cotizacion' => $cotizacion])

  {{-- ENVÍA EL CARRITO --}}
  <input type="hidden" name="items" id="items">

  <button class="btn btn-primary mt-3">
      {{ $cotizacion ? 'Actualizar' : 'Guardar' }}
  </button>

  <a class="btn btn-outline-secondary mt-3"
     href="{{ route('cotizaciones.index') }}">
     Volver
  </a>
</form>
@endsection

@push('scripts')
<script>
/* =========================
   VARIABLES GLOBALES
========================= */
let cart = {};
let tipoPrecio = document.getElementById('tipo_precio')?.value || 'obra';

function clienteSeleccionado() {
  const sel = document.getElementById('cliente_id');
  return !!(sel && sel.value);
}

function setCatalogoHabilitado(enabled) {
  document.querySelectorAll('.add-product').forEach(btn => {
    btn.disabled = !enabled;
  });
}

/* =========================
   FUNCIONES
========================= */
function renderCart() {
  const tbody = document.getElementById('cart-body');
  if (!tbody) return;

  tbody.innerHTML = '';
  let total = 0;

  Object.values(cart).forEach(item => {
    const subtotal = item.cantidad * item.precio;
    total += subtotal;

    tbody.innerHTML += `
      <tr>
        <td>${item.codigo}</td>
        <td>${item.nombre}</td>
        <td>
          <input type="number" min="1"
            class="form-control form-control-sm"
            value="${item.cantidad}"
            onchange="updateQty(${item.id}, this.value)">
        </td>
        <td>${item.precio.toFixed(2)}</td>
        <td>${subtotal.toFixed(2)}</td>
        <td>
          <input type="text"
            class="form-control form-control-sm"
            value="${(item.observaciones || '').replace(/\"/g,'&quot;')}"
            onchange="updateObs(${item.id}, this.value)"
            placeholder="Obs...">
        </td>
        <td>
          <button type="button"
            class="btn btn-sm btn-danger"
            onclick="removeItem(${item.id})">✕</button>
        </td>
      </tr>
    `;
  });

  document.getElementById('total').innerText = total.toFixed(2);
}

function getPrecioBtnPorTipo(btn, tipo) {
  const pObra = parseFloat(btn.dataset.precioObra || '0');
  const pEdi  = parseFloat(btn.dataset.precioEdificio || '0');
  return tipo === 'edificio' ? pEdi : pObra;
}

function addProduct(id, codigo, nombre, precioObra, precioEdificio) {
  if (!cart[id]) {
    cart[id] = {
      id,
      codigo,
      nombre,
      precio_obra: parseFloat(precioObra),
      precio_edificio: parseFloat(precioEdificio),
      precio: (tipoPrecio === 'edificio' ? parseFloat(precioEdificio) : parseFloat(precioObra)),
      cantidad: 1,
      observaciones: ''
    };
  } else {
    cart[id].cantidad++;
  }
  renderCart();
}

function updateQty(id, qty) {
  qty = parseInt(qty);
  if (qty < 1) qty = 1;
  cart[id].cantidad = qty;
  renderCart();
}

function removeItem(id) {
  delete cart[id];
  renderCart();
}

function updateObs(id, val) {
  if (!cart[id]) return;
  cart[id].observaciones = val;
}

function syncPricesToTipo() {
  tipoPrecio = document.getElementById('tipo_precio')?.value || 'obra';

  Object.values(cart).forEach(item => {
    const p = (tipoPrecio === 'edificio') ? item.precio_edificio : item.precio_obra;
    item.precio = parseFloat(p || 0);
  });

  renderCart();
}

/* =========================
   PRECARGA PARA EDICIÓN
========================= */
function preloadCotizacion() {
  if (typeof window.COTIZACION_EDIT === 'undefined') return;

  const detalles = window.COTIZACION_EDIT.detalles || [];

  detalles.forEach(d => {
    cart[d.producto_id] = {
      id: d.producto_id,
      codigo: d.codigo,
      nombre: d.descripcion,
      // para edición: intentamos tomar precios del botón; si no existe, caemos al precio_unitario
      precio_obra: (() => {
        const btn = document.querySelector(`.add-product[data-id="${d.producto_id}"]`);
        return btn ? parseFloat(btn.dataset.precioObra || d.precio_unitario) : parseFloat(d.precio_unitario);
      })(),
      precio_edificio: (() => {
        const btn = document.querySelector(`.add-product[data-id="${d.producto_id}"]`);
        return btn ? parseFloat(btn.dataset.precioEdificio || d.precio_unitario) : parseFloat(d.precio_unitario);
      })(),
      precio: parseFloat(d.precio_unitario),
      cantidad: d.cantidad,
      observaciones: d.observaciones || ''
    };
  });

  // Ajustar precio al tipo actual
  syncPricesToTipo();

}

/* =========================
   EVENTOS
========================= */
document.addEventListener('DOMContentLoaded', function () {

  // bloquear catálogo si no hay cliente
  setCatalogoHabilitado(clienteSeleccionado());

  const clienteSel = document.getElementById('cliente_id');
  if (clienteSel) {
    clienteSel.addEventListener('change', function () {
      setCatalogoHabilitado(clienteSeleccionado());
    });
  }

  // Agregar producto
  document.querySelectorAll('.add-product').forEach(btn => {
    btn.addEventListener('click', function () {
      if (!clienteSeleccionado()) {
        alert('Primero debes seleccionar un cliente para agregar productos.');
        return;
      }

      addProduct(
        this.dataset.id,
        this.dataset.codigo,
        this.dataset.nombre,
        this.dataset.precioObra,
        this.dataset.precioEdificio
      );
    });
  });

  // Cambio de tipo de precio
  const tipo = document.getElementById('tipo_precio');
  if (tipo) {
    tipo.addEventListener('change', syncPricesToTipo);
  }

  // Precargar si es edición
  preloadCotizacion();

  // Enviar carrito al backend
  document.getElementById('cot-form').addEventListener('submit', function () {
    const payload = Object.values(cart).map(i => ({
      producto_id: i.id,
      cantidad: i.cantidad,
      observaciones: i.observaciones || null
    }));
    document.getElementById('items').value = JSON.stringify(payload);
  });
});
</script>
@endpush
