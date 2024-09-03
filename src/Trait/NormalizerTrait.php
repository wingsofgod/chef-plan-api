<?php

namespace App\Trait;

use App\Serializer\Normalizer\ModelNormalizer;
use Symfony\Contracts\Service\Attribute\Required;

trait NormalizerTrait
{
    protected ModelNormalizer $normalizer;

    #[Required]
    public function setNormalizer(ModelNormalizer $normalizer): void
    {
        $this->normalizer = $normalizer;
    }
}