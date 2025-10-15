<?php

namespace App\Tests\Service;

use App\Service\FeatureService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

class FeatureServiceTest extends KernelTestCase
{
    use Factories;

    private FeatureService $service;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $container = self::getContainer();
        $this->service = $container->get(FeatureService::class);
    }

    public function testGetDraftsToClean(): void
    {
        $drafts = $this->service->getDraftsToClean();

        foreach ($drafts as $draft) {
            $this->assertEmpty($draft->getTitle());
            $this->assertEmpty($draft->getContent());
            $this->assertTrue($draft->isDraft());
        }
    }
}
