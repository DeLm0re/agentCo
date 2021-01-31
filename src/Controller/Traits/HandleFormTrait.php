<?php

namespace App\Controller\Traits;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait HandleFormTrait.
 */
trait HandleFormTrait
{
    /**
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return bool
     */
    public function handleFormSubmission(Request $request, FormInterface $form): bool
    {
        $submissionComplete = false;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $agent = $form->getData();

            $this->em->persist($agent);
            $this->em->flush();

            $submissionComplete = true;
        }

        return $submissionComplete;
    }
}
