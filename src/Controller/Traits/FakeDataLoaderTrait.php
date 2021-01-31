<?php

namespace App\Controller\Traits;

trait FakeDataLoaderTrait
{
    /**
     * @param string $fakeDataName
     *
     * @return array|null
     *
     * @throws \Exception
     */
    protected function fakeDataLoader(string $fakeDataName): ?array
    {
        $projectRoot = $this->getParameter('kernel.project_dir');
        $fakeDataPath = "$projectRoot/fakeData/$fakeDataName.json";
        $rawData = file_get_contents($fakeDataPath, true);

        return json_decode($rawData, true);
    }
}
