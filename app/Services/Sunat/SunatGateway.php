<?php

namespace App\Services\Sunat;

use App\Models\SunatConfig;

/**
 * Puerta de entrada a SUNAT.
 *
 * Este proyecto deja listo el módulo de configuración y conectividad.
 * Para emisión real (generar XML UBL 2.1, firmar, comprimir y enviar por SOAP),
 * se recomienda integrar una librería especializada (p.ej. Greenter).
 */
class SunatGateway
{
    public function __construct(private SunatConfig $config)
    {
    }

    public static function fromDb(): ?self
    {
        $cfg = SunatConfig::query()->first();
        if (!$cfg || !$cfg->is_enabled) {
            return null;
        }
        return new self($cfg);
    }

    public function isGreenterAvailable(): bool
    {
        return class_exists('Greenter\\Ws\\Services\\SunatEndpoints');
    }

    /**
     * Emisión real de comprobantes.
     *
     * Nota: este método requiere una implementación de UBL + firma.
     */
    public function emitComprobante(array $payload): array
    {
        if (!$this->isGreenterAvailable()) {
            return [
                'ok' => false,
                'message' => 'Módulo SUNAT activado, pero falta instalar la librería de emisión (p.ej. Greenter). ' .
                    'Ejecuta: composer require greenter/greenter greenter/ws',
            ];
        }

        // Aquí se integraría la emisión usando Greenter u otra librería.
        return [
            'ok' => false,
            'message' => 'Emisión no configurada aún. Requiere implementación UBL 2.1 + firma + envío SOAP.',
        ];
    }
}
