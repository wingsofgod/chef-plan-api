<?php

namespace App\Trait;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait NativeQueryTrait
{
    use EntityManagerTrait;

    public function fetchAll(string $sql, array $params = [], array $decodes = []): array
    {
        try {
            $results = $this->getQueryResult($sql, $params)
                ->fetchAllAssociative();

            foreach ($results as $i => $result) {
                foreach ($decodes as $key) {
                    $results[$i][$key] = !empty($results[$i][$key]) ? json_decode($results[$i][$key], true) : null;
                }
            }

            return $results;
        } catch (Exception $e) {
            return [];
        }
    }

    public function fetchAssociative(string $sql, array $params = [], array $decodes = [], string $expose = null): mixed
    {
        try {
            $result = $this->getQueryResult($sql, $params)
                ->fetchAssociative();
            if (!$result) {
                return null;
            }
            foreach ($decodes as $key) {
                $result[$key] = !empty($result[$key]) ? json_decode($result[$key], true) : null;
            }
            if ($expose) {
                return $result[$expose] ?? null;
            }

            return $result;
        } catch (Exception $e) {
            return null;
        }
    }

    private function getQueryResult($sql, $params): Result
    {
        try {
            $conn = $this->entityManager->getConnection();
            $stmt = $conn->prepare($sql);
            return $stmt->executeQuery($params);
        } catch (Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }

    }
}
