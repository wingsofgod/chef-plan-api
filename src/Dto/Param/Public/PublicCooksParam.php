<?php
declare(strict_types=1);

namespace App\Dto\Param\Public;

use Symfony\Component\Validator\Constraints as Assert;

class PublicCooksParam
{
    #[Assert\NotBlank]
    public ?array $materials;

    #[Assert\NotBlank]
    #[Assert\Range(notInRangeMessage: "Kişi sayısı 1 ile 10 arasında olmalıdır", min: 1, max: 10)]
    public int $personCount;
}