<?php

namespace App\Entity;

interface ExportableEntityInterface
{
    /** @return array<int, string|null> */
    public function getExportableData(): array;
}
