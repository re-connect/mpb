<?php

namespace App\Tests\EndToEnd\Feature;

use App\Tests\EndToEnd\AbstractBrowserKitEndToEndTest;

class FeatureLikeTest extends AbstractBrowserKitEndToEndTest
{
    public function testLikeIncrementsCounter(): void
    {
        $this->loginUser();
    }

}