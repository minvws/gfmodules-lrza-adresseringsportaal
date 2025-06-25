<?php

declare(strict_types=1);

namespace App\Models;

enum EndpointConnectionTypes: string
{
    case IHE_XCPD = 'ihe-xcpd';
    case IHE_XCA = 'ihe-xca';
    case IHE_XDR = 'ihe-xdr';
    case IHE_XDS = 'ihe-xds';
    case IHE_IID = 'ihe-iid';
    case DICOM_WADO_RS = 'dicom-wado-rs';
    case DICOM_QIDO_RS = 'dicom-qido-rs';
    case DICOM_STOW_RS = 'dicom-stow-rs';
    case DICOM_WADO_URI = 'dicom-wado-uri';
    case HL7_FHIR_REST = 'hl7-fhir-rest';
    case HL7_FHIR_MSG = 'hl7-fhir-msg';
    case HL7V2_MLLP = 'hl7v2-mllp';
    case SECURE_EMAIL = 'secure-email';
    case DIRECT_PROJECT = 'direct-project';

    /**
     * Get the display name for the connection type
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::IHE_XCPD => 'IHE XCPD',
            self::IHE_XCA => 'IHE XCA',
            self::IHE_XDR => 'IHE XDR',
            self::IHE_XDS => 'IHE XDS',
            self::IHE_IID => 'IHE IID',
            self::DICOM_WADO_RS => 'DICOM WADO-RS',
            self::DICOM_QIDO_RS => 'DICOM QIDO-RS',
            self::DICOM_STOW_RS => 'DICOM STOW-RS',
            self::DICOM_WADO_URI => 'DICOM WADO-URI',
            self::HL7_FHIR_REST => 'HL7 FHIR',
            self::HL7_FHIR_MSG => 'HL7 FHIR Messaging',
            self::HL7V2_MLLP => 'HL7 v2 MLLP',
            self::SECURE_EMAIL => 'Secure email',
            self::DIRECT_PROJECT => 'Direct Project',
        };
    }

    /**
     * Get all connection types as code => display array
     * @return array<string, string>
     */
    public static function getConnectionTypes(): array
    {
        $types = [];
        foreach (self::cases() as $case) {
            $types[$case->value] = $case->getDisplayName();
        }
        return $types;
    }

    /**
     * Get all connection type codes
     * @return array<string>
     */
    public static function getCodes(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get display name for a connection type code
     */
    public static function getDisplayNameByCode(string $code): ?string
    {
        $case = self::tryFrom($code);
        return $case?->getDisplayName();
    }
}
