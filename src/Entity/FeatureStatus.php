<?php

namespace App\Entity;

enum FeatureStatus: string
{
    case ToBeDecided = 'TO_BE_DECIDED';
    case WaitingForRecurrences = 'WAITING_FOR_RECURRENCES';
    case ToBeDeveloped = 'TO_BE_DEVELOPED';
    case WontBeDeveloped = 'WONT_BE_DEVELOPED';
    case BeingDeveloped = 'BEING_DEVELOPED';
    case InProduction = 'IN_PRODUCTION';
    case AlternativeSolutionProposed = 'ALTERNATIVE_SOLUTION_PROPOSED';

    public const FINAL_STATUS = [
        self::WontBeDeveloped,
        self::InProduction,
        self::AlternativeSolutionProposed,
    ];

    public function getColor(): string
    {
        return match ($this) {
            FeatureStatus::ToBeDecided => '#bdc3c7',
            FeatureStatus::WaitingForRecurrences => '#FFA41E',
            FeatureStatus::ToBeDeveloped => '#3498db',
            FeatureStatus::WontBeDeveloped => '#e74c3c',
            FeatureStatus::BeingDeveloped => '#0E6F73',
            FeatureStatus::InProduction => '#28AD7A',
            FeatureStatus::AlternativeSolutionProposed => '#754eb1',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            FeatureStatus::ToBeDecided => 'hourglass-1',
            FeatureStatus::WaitingForRecurrences => 'clock-rotate-left',
            FeatureStatus::ToBeDeveloped => 'truck-fast',
            FeatureStatus::WontBeDeveloped => 'circle-xmark',
            FeatureStatus::BeingDeveloped => 'person-digging',
            FeatureStatus::InProduction => 'check',
            FeatureStatus::AlternativeSolutionProposed => 'comments',
        };
    }
}
