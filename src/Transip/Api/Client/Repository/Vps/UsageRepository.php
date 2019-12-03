<?php

namespace Transip\Api\Client\Repository\Vps;

use Transip\Api\Client\Entity\Vps;
use Transip\Api\Client\Entity\Vps\UsageDataCpu;
use Transip\Api\Client\Entity\Vps\UsageDataDisk;
use Transip\Api\Client\Entity\Vps\UsageDataNetwork;
use Transip\Api\Client\Repository\ApiRepository;
use Transip\Api\Client\Repository\VpsRepository;

class UsageRepository extends ApiRepository
{
    public const TYPE_CPU = 'cpu';
    public const TYPE_DISK = 'disk';
    public const TYPE_NETWORK = 'network';
    public const RESOURCE_NAME = 'usage';

    protected function getRepositoryResourceNames(): array
    {
        return [VpsRepository::RESOURCE_NAME, self::RESOURCE_NAME];
    }

    /**
     * @param string $vpsName
     * @param array  $types
     * @param int    $dateTimeStart
     * @param int    $dateTimeEnd
     * @return array
     */
    public function getByVpsName(
        string $vpsName,
        array $types = [],
        int $dateTimeStart = 0,
        int $dateTimeEnd = 0
    ): array {
        $parameters = [];
        if (count($types) > 0) {
            $parameters['types'] = implode(',',$types);
        }
        if ($dateTimeStart > 0) {
            $parameters['dateTimeStart'] = $dateTimeStart;
        }
        if ($dateTimeEnd > 0) {
            $parameters['dateTimeEnd'] = $dateTimeEnd;
        }

        $usages          = [];
        $response        = $this->httpClient->get($this->getResourceUrl($vpsName), $parameters);
        $usageTypesArray = $response['usage'] ?? [];

        foreach ($usageTypesArray as $usageType => $usageArray) {
            switch ($usageType) {
                case self::TYPE_CPU:
                    foreach ($usageArray as $usage) {
                        $usages[$usageType][] = new UsageDataCpu($usage);
                    }
                    break;
                case self::TYPE_DISK:
                    foreach ($usageArray as $usage) {
                        $usages[$usageType][] = new UsageDataDisk($usage);
                    }
                    break;
                case self::TYPE_NETWORK:
                    foreach ($usageArray as $usage) {
                        $usages[$usageType][] = new UsageDataNetwork($usage);
                    }
                    break;
            }
        }

        return $usages;
    }
}
