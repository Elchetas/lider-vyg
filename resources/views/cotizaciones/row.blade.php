@php($it = $item)
<tr>
  <td>
    <select class="form-select form-select-sm prod-select" name="items[{{ $i }}][producto_id]">
      <option value="">--</option>
      @foreach($productos as $p)
        <option value="{{ $p->id }}" {{ (string)($it['producto_id'] ?? '')===(string)$p->id ? 'selected':'' }}>{{ $p->codigo }} - {{ $p->nombre_producto }}</option>
      @endforeach
    </select>
  </td>
  <td>
    <input class="form-control form-control-sm codigo-input" name="items[{{ $i }}][codigo]" value="{{ $it['codigo'] ?? '' }}">
  </td>
  <td>
    <input class="form-control form-control-sm desc-input" name="items[{{ $i }}][descripcion]" required value="{{ $it['descripcion'] ?? '' }}">
  </td>
  <td>
    <input class="form-control form-control-sm" type="number" min="1" name="items[{{ $i }}][cantidad]" value="{{ $it['cantidad'] ?? 1 }}">
  </td>
  <td>
    <input class="form-control form-control-sm price-input" type="number" step="0.01" min="0" name="items[{{ $i }}][precio_unitario]" value="{{ $it['precio_unitario'] ?? 0 }}">
  </td>
  <td class="total-cell">0.00</td>
  <td>
    <input class="form-control form-control-sm" name="items[{{ $i }}][observaciones]" value="{{ $it['observaciones'] ?? '' }}" placeholder="(en rojo en PDF)">
  </td>
  <td class="text-end">
    <button type="button" class="btn btn-sm btn-outline-danger btn-del">X</button>
  </td>
</tr>
