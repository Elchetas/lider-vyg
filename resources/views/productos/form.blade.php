@php($p = $producto)

<div class="row g-3">

  <div class="col-md-4">
    <label class="form-label">Código</label>
    <input name="codigo"
           class="form-control"
           value="{{ old('codigo', $p->codigo ?? '') }}">
  </div>

  <div class="col-md-8">
    <label class="form-label">Nombre Producto</label>
    <input name="nombre_producto"
           class="form-control"
           required
           value="{{ old('nombre_producto', $p->nombre_producto ?? '') }}">
  </div>

  <div class="col-12">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion"
              class="form-control"
              rows="3">{{ old('descripcion', $p->descripcion ?? '') }}</textarea>
  </div>

  <div class="col-md-4">
    <label class="form-label">Precio Obra</label>
    <input name="precio_obra"
           type="number"
           step="0.01"
           class="form-control"
           value="{{ old('precio_obra', $p->precio_obra ?? $p->precio ?? 0) }}">
  </div>

  <div class="col-md-4">
    <label class="form-label">Precio Edificio</label>
    <input name="precio_edificio"
           type="number"
           step="0.01"
           class="form-control"
           value="{{ old('precio_edificio', $p->precio_edificio ?? $p->precio ?? 0) }}">
  </div>

  <div class="col-md-4">
    <label class="form-label">Precio Proveedor</label>
    <input name="precio_proveedor"
           type="number"
           step="0.01"
           class="form-control"
           value="{{ old('precio_proveedor', $p->precio_proveedor ?? $p->precio_prov ?? 0) }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">Proveedor</label>
    <input name="nombre_proveedor"
           class="form-control"
           value="{{ old('nombre_proveedor', $p->nombre_proveedor ?? $p->proveedor ?? '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">Cantidad</label>
    <input name="cantidad"
           type="number"
           step="1"
           class="form-control"
           value="{{ old('cantidad', $p->cantidad ?? 0) }}">
  </div>

  <div class="col-12">
    <label class="form-label">Observaciones</label>
    <textarea name="observaciones"
              class="form-control"
              rows="2">{{ old('observaciones', $p->observaciones ?? '') }}</textarea>
  </div>

  {{-- IMAGEN --}}
  <div class="col-md-6">
    <label class="form-label">Imagen</label>
    <input name="imagen"
           type="file"
           accept="image/*"
           class="form-control">

    @if($p->imagen_url)
      <div class="mt-2">
        <img src="{{ $p->imagen_url }}?v={{ $p->updated_at?->timestamp }}"
             alt="Imagen del producto"
             class="img-thumbnail"
             style="height:80px">
      </div>
    @endif
  </div>

  {{-- ACTIVO --}}
  <div class="col-md-6 d-flex align-items-end">
    @php($act = old('activo', $p->activo ?? true))
    <div class="form-check">
      <input class="form-check-input"
             type="checkbox"
             name="activo"
             value="1"
             {{ $act ? 'checked' : '' }}>
      <label class="form-check-label">
        Activo
      </label>
    </div>
  </div>

</div>
