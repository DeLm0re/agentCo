<?php

namespace App\Controller;

use App\Controller\Traits\ExceptionHandlerTrait;
use App\Controller\Traits\FakeDataLoaderTrait;
use App\Controller\Traits\ResolveReportsTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ReportController.
 */
class ReportController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use FakeDataLoaderTrait;
    use ExceptionHandlerTrait;
    use ResolveReportsTrait;

    /**
     * @Route("/report", name="report")
     *
     * @return Response
     */
    public function report(): Response
    {
        $resolveReports = null;

        try {
            $reports = $this->fakeDataLoader('reports');
            $resolveReports = $this->resolveReports($reports);
        } catch (\Exception $e) {
            $this->logger->critical($this->getDefaultErrorMsg($e));
        }

        return $this->render('report.html.twig', [
            'resolvedReports' => $resolveReports,
        ]);
    }
}
