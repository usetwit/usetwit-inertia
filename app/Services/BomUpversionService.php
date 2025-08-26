<?php

namespace App\Services;

use App\Models\BomVersion;

class BomUpversionService
{
    public function upversionNetwork(BomVersion $bomVersion, array $network = [], array $options = []): BomVersion
    {
        $latestVersion = $bomVersion->bom->latestVersionNumber();

        $bomVersion->replicate()
            ->fill([
                'version' => $latestVersion + 1,
            ])
            ->save();

        return $bomVersion->bom->latestBomVersion();
    }
}
