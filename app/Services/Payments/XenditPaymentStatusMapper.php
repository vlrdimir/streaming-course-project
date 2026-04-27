<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class XenditPaymentStatusMapper
{
    /**
     * @var array<string, list<string>>
     */
    private array $statusMap = [
        'pending' => [
            'PENDING',
            'INVOICE_CREATED',
            'AWAITING_PAYMENT',
        ],
        'paid' => [
            'PAID',
            'SETTLED',
            'SUCCEEDED',
            'PAYMENT_SUCCEEDED',
            'INVOICE_PAID',
        ],
        'failed' => [
            'FAILED',
            'PAYMENT_FAILED',
            'INVOICE_FAILED',
        ],
        'expired' => [
            'EXPIRED',
            'INVOICE_EXPIRED',
        ],
        'cancelled' => [
            'CANCELLED',
            'VOIDED',
            'REVOKED',
            'STOPPED',
            'INVOICE_CANCELLED',
        ],
    ];

    public function normalize(?string $status, ?string $event = null): string
    {
        $candidates = array_filter([
            $this->normalizeToken($status),
            $this->normalizeToken($event),
        ]);

        foreach ($candidates as $candidate) {
            foreach ($this->statusMap as $internalStatus => $providerStatuses) {
                if (in_array($candidate, $providerStatuses, true)) {
                    return $internalStatus;
                }
            }
        }

        log_message(
            'error',
            'Unsupported Xendit payment status encountered. status={status}, event={event}',
            [
                'status' => $status ?? 'null',
                'event' => $event ?? 'null',
            ]
        );

        throw new InvalidArgumentException('Unsupported Xendit payment status or event received.');
    }

    /**
     * @return array<string, string>
     */
    public function sampleMappings(): array
    {
        return [
            'PENDING' => $this->normalize('PENDING'),
            'PAID' => $this->normalize('PAID'),
            'FAILED' => $this->normalize('FAILED'),
            'EXPIRED' => $this->normalize('EXPIRED'),
            'CANCELLED' => $this->normalize('CANCELLED'),
            'invoice.paid' => $this->normalize(null, 'invoice.paid'),
        ];
    }

    private function normalizeToken(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmedValue = trim($value);

        if ($trimmedValue === '') {
            return null;
        }

        return strtoupper((string) preg_replace('/[^A-Z0-9]+/i', '_', $trimmedValue));
    }
}
