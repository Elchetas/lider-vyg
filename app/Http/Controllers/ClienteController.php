<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('id','desc')->paginate(15);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'lugar' => 'nullable|string|max:255',
            'nombre_administrador' => 'nullable|string|max:255',
            'observacion' => 'nullable|string',
            'unidad_inmobiliaria' => 'nullable|string|max:255',
            'tipo_comprobante' => 'required|in:Factura,Boleta',
            'nombre_factura' => 'nullable|string|max:255',
            'ruc' => 'nullable|string|max:20',
            'activo' => 'nullable|boolean',
        ]);

        $data['activo'] = $request->boolean('activo');
        Cliente::create($data);
        return redirect()->route('clientes.index')->with('ok','Cliente creado');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'lugar' => 'nullable|string|max:255',
            'nombre_administrador' => 'nullable|string|max:255',
            'observacion' => 'nullable|string',
            'unidad_inmobiliaria' => 'nullable|string|max:255',
            'tipo_comprobante' => 'required|in:Factura,Boleta',
            'nombre_factura' => 'nullable|string|max:255',
            'ruc' => 'nullable|string|max:20',
            'activo' => 'nullable|boolean',
        ]);
        $data['activo'] = $request->boolean('activo');
        $cliente->update($data);
        return redirect()->route('clientes.index')->with('ok','Cliente actualizado');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('ok','Cliente eliminado');
    }
}
