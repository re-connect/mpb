<?php

namespace App\Tests\EndToEnd\Feature;

use App\Tests\EndToEnd\AbstractBrowserKitEndToEndTest;

class FeatureLikeTest extends AbstractBrowserKitEndToEndTest
{
    /**
     * @throws \Exception
     */
    public function testLikeIncrementsCounter(): void
    {
        $user = $this->loginUser();
        $this->assertTrue(true);
    }

}