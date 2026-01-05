@php($c = $cliente)
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Nombre Cliente</label>
    <input name="nombre_cliente" class="form-control" required value="{{ old('nombre_cliente', $c->nombre_cliente ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">RUC</label>
    <input name="ruc" class="form-control" value="{{ old('ruc', $c->ruc ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">Dirección</label>
    <input name="direccion" class="form-control" value="{{ old('direccion', $c->direccion ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">Lugar</label>
    <input name="lugar" class="form-control" value="{{ old('lugar', $c->lugar ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">Nombre Administrador</label>
    <input name="nombre_administrador" class="form-control" value="{{ old('nombre_administrador', $c->nombre_administrador ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">Unidad Inmobiliaria (Proyecto/Edificio)</label>
    <input name="unidad_inmobiliaria" class="form-control" value="{{ old('unidad_inmobiliaria', $c->unidad_inmobiliaria ?? '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">Tipo Comprobante</label>
    <select name="tipo_comprobante" class="form-select" required>
      @php($tc = old('tipo_comprobante', $c->tipo_comprobante ?? 'Factura'))
      <option value="Factura" {{ $tc==='Factura'?'selected':'' }}>Factura</option>
      <option value="Boleta" {{ $tc==='Boleta'?'selected':'' }}>Boleta</option>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label">Nombre de Factura (Razón Social)</label>
    <input name="nombre_factura" class="form-control" value="{{ old('nombre_factura', $c->nombre_factura ?? '') }}">
  </div>
  <div class="col-12">
    <label class="form-label">Observación</label>
    <textarea name="observacion" class="form-control" rows="3">{{ old('observacion', $c->observacion ?? '') }}</textarea>
  </div>
  <div class="col-12">
    @php($act = old('activo', $c->activo ?? true))
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="activo" value="1" {{ $act ? 'checked' : '' }}>
      <label class="form-check-label">Activo</label>
    </div>
  </div>
</div>
