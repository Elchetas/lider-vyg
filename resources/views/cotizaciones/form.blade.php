{{-- =====================
   CLIENTE
===================== --}}
<div class="mb-3">
  <label class="form-label">Cliente</label>
  <select name="cliente_id" id="cliente_id" class="form-select" required>
    <option value="">Seleccione cliente</option>
    @foreach($clientes as $c)
      <option value="{{ $c->id }}"
        @if(isset($cotizacion) && $cotizacion && $cotizacion->cliente_id == $c->id) selected @endif>
        {{ $c->nombre_cliente ?? $c->nombre ?? '' }}
      </option>
    @endforeach
  </select>
  <div class="form-text">Debes seleccionar un cliente para poder agregar productos del catálogo.</div>
</div>

{{-- =====================
   TIPO DE PRECIO
===================== --}}
<div class="mb-3">
  <label class="form-label">Tipo de precio</label>
  <select name="tipo_precio" id="tipo_precio" class="form-select">
    <option value="obra"
      @if(isset($cotizacion) && $cotizacion && $cotizacion->tipo_precio === 'obra') selected @endif>
      Obra
    </option>
    <option value="edificio"
      @if(isset($cotizacion) && $cotizacion && $cotizacion->tipo_precio === 'edificio') selected @endif>
      Edificio
    </option>
  </select>
</div>

{{-- =====================
   OBSERVACIONES (GENERALES)
===================== --}}
<div class="mb-3">
  <label class="form-label">Observaciones</label>
  <textarea
    name="observaciones"
    id="observaciones"
    class="form-control"
    rows="3"
    placeholder="Observaciones generales para la cotización..."
  >{{ old('observaciones', $cotizacion->observaciones ?? '') }}</textarea>
</div>

<hr>

{{-- =====================
   CATÁLOGO
===================== --}}
<h5>Catálogo</h5>
<div class="row">
@foreach($productos as $p)
  <div class="col-md-3 mb-3">
    <div class="card h-100">
      @if($p->imagen_url)
        <img src="{{ $p->imagen_url }}" class="card-img-top" alt="{{ $p->nombre_producto }}">
      @elseif($p->imagen_path)
        <img src="/{{ $p->imagen_path }}" class="card-img-top" alt="{{ $p->nombre_producto }}">
      @endif

      <div class="card-body p-2">
        <strong>{{ $p->nombre_producto }}</strong><br>
        <small class="text-muted">{{ $p->codigo }}</small><br>
        <span class="text-muted">
          Obra: S/ {{ number_format((float)($p->precio_obra ?? $p->precio ?? 0), 2) }}<br>
          Edificio: S/ {{ number_format((float)($p->precio_edificio ?? $p->precio ?? 0), 2) }}
        </span>
      </div>

      <div class="card-footer p-2">
        <button type="button"
          class="btn btn-sm btn-primary w-100 add-product"
          data-id="{{ $p->id }}"
          data-codigo="{{ $p->codigo }}"
          data-nombre="{{ $p->nombre_producto }}"
          data-precio-obra="{{ (float)($p->precio_obra ?? $p->precio ?? 0) }}"
          data-precio-edificio="{{ (float)($p->precio_edificio ?? $p->precio ?? 0) }}">
          Agregar
        </button>
      </div>
    </div>
  </div>
@endforeach
</div>

<hr>

{{-- =====================
   CARRITO
===================== --}}
<h5>Productos seleccionados</h5>

<table class="table table-sm table-bordered align-middle">
  <thead class="table-light">
    <tr>
      <th>Código</th>
      <th>Producto</th>
      <th style="width:90px">Cant.</th>
      <th>Precio</th>
      <th>Subtotal</th>
      <th style="width:180px">Obs.</th>
      <th></th>
    </tr>
  </thead>
  <tbody id="cart-body"></tbody>
</table>

<div class="text-end">
  <strong>Total: S/ <span id="total">0.00</span></strong>
</div>

{{-- =====================
   DATA PARA JS (EDICIÓN)
===================== --}}
@if(isset($cotizacion) && $cotizacion)
  <script>
    window.COTIZACION_EDIT = @json($cotizacion);
  </script>
@endif
