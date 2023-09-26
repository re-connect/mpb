<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\UserRequestExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserRequestExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_vote_path', [UserRequestExtensionRuntime::class, 'getVotePath']),
            new TwigFunction('get_mark_done_path', [UserRequestExtensionRuntime::class, 'getMarkDonePath']),
            new TwigFunction('get_convert_path', [UserRequestExtensionRuntime::class, 'getConvertPath']),
        ];
    }
}
