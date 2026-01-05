<?php

namespace App\Http\Controllers;

use App\Models\SunatConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SunatConfigController extends Controller
{
    public function edit()
    {
        $config = SunatConfig::query()->first();
        return view('sunat.config', [
            'config' => $config,
            'defaults' => config('sunat.endpoints'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'ruc' => ['nullable','string','max:11'],
            'razon_social' => ['nullable','string','max:255'],
            'sol_user' => ['nullable','string','max:50'],
            'sol_password' => ['nullable','string','max:255'],
            'fe_wsdl' => ['nullable','url','max:255'],
            'gre_wsdl' => ['nullable','url','max:255'],
            'cert_password' => ['nullable','string','max:255'],
            'is_enabled' => ['nullable','boolean'],
            'cert_file' => ['nullable','file','mimes:pfx,p12','max:2048'],
        ]);

        $config = SunatConfig::query()->firstOrNew([]);

        $config->ruc = $data['ruc'] ?? $config->ruc;
        $config->razon_social = $data['razon_social'] ?? $config->razon_social;
        $config->sol_user = $data['sol_user'] ?? $config->sol_user;

        if (!empty($data['sol_password'])) {
            $config->sol_password = $data['sol_password'];
        }

        $config->fe_wsdl = $data['fe_wsdl'] ?? (config('sunat.endpoints.fe_wsdl'));
        $config->gre_wsdl = $data['gre_wsdl'] ?? (config('sunat.endpoints.gre_wsdl'));

        if (!empty($data['cert_password'])) {
            $config->cert_password = $data['cert_password'];
        }

        $config->is_enabled = (bool)($request->boolean('is_enabled'));

        if ($request->hasFile('cert_file')) {
            $path = $request->file('cert_file')->storeAs('sunat', 'certificado.pfx');
            $config->cert_path = $path;
        }

        $config->save();

        return back()->with('status', 'Configuración SUNAT guardada.');
    }

    /**
     * Prueba rápida: intenta descargar el WSDL de Facturación Electrónica.
     * Nota: SUNAT requiere internet desde el servidor.
     */
    public function testConnection()
    {
        $config = SunatConfig::query()->first();
        $wsdl = $config?->fe_wsdl ?: config('sunat.endpoints.fe_wsdl');

        try {
            // Solo verificamos accesibilidad. No envia comprobantes.
            $ctx = stream_context_create([
                'http' => ['timeout' => 8],
                'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
            ]);
            $content = @file_get_contents($wsdl, false, $ctx);
            if ($content === false) {
                throw new \RuntimeException('No se pudo acceder al WSDL.');
            }
            return back()->with('status', 'Conexión OK: WSDL accesible.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Conexión fallida: '.$e->getMessage());
        }
    }
}
