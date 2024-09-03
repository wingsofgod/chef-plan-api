<?php

namespace App\Trait;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\PartialDenormalizationException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Service\Attribute\Required;

trait SerializerTrait
{
    use ValidatorTrait;

    protected SerializerInterface $serializer;

    #[Required]
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function serialize($data, $groups = []): string
    {
        return $this->serializer->serialize($data, JsonEncoder::FORMAT,
            [
                AbstractNormalizer::GROUPS => $groups,
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => false
            ]
        );
    }

    public function deserialize($data, $type, $validate = true, $groups = []): mixed
    {
        try {
            $object = $this->serializer->deserialize($data, $type, JsonEncoder::FORMAT,
                [
                    AbstractNormalizer::GROUPS => $groups,
                    AbstractObjectNormalizer::SKIP_UNINITIALIZED_VALUES => true,
                    DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true
                ]
            );

            if ($validate) {
                $this->checkViolationList($this->validator->validate($object, groups: $groups));
            }

            return $object;

        } catch (PartialDenormalizationException $e) {

            $violations = new ConstraintViolationList();

            /** @var NotNormalizableValueException $exception */
            foreach ($e->getErrors() as $exception) {
                $message = sprintf('The type must be one of "%s" ("%s" given).', implode(', ', $exception->getExpectedTypes()), $exception->getCurrentType());
                $parameters = [];
                if ($exception->canUseMessageForUser()) {
                    $parameters['hint'] = $exception->getMessage();
                }
                $violations->add(new ConstraintViolation($message, '', $parameters, null, $exception->getPath(), null));
            }

            $this->checkViolationList($violations);
        }

        return null;
    }
}