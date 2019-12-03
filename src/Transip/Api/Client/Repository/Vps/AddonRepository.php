<?php

namespace Transip\Api\Client\Repository\Vps;

use Transip\Api\Client\Entity\Product;
use Transip\Api\Client\Repository\ApiRepository;
use Transip\Api\Client\Repository\VpsRepository;

class AddonRepository extends ApiRepository
{
    public const RESOURCE_NAME = 'addons';
    protected function getRepositoryResourceNames(): array
    {
        return [VpsRepository::RESOURCE_NAME, self::RESOURCE_NAME];
    }

    /**
     * @param string $vpsName
     * @return Product[]
     */
    public function getByVpsName(string $vpsName): array
    {
        $addons        = [];
        $response      = $this->httpClient->get($this->getResourceUrl($vpsName));
        $categoryArray = $response['addons'] ?? [];

        foreach ($categoryArray as $category => $addonsArray) {
            foreach ($addonsArray as $addonArray) {
                $addonArray['category'] = $category;
                $addons[]               = new Product($addonArray);
            }
        }

        return $addons;
    }

    public function order(string $vpsName, array $addonNames): void
    {
        $parameters['addons'] = $addonNames;
        $this->httpClient->post($this->getResourceUrl($vpsName), $parameters);
    }

    public function cancel(string $vpsName, string $addonName): void
    {
        $this->httpClient->delete($this->getResourceUrl($vpsName, $addonName), []);
    }
}
