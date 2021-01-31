<?php

namespace App\Controller;

use App\Controller\Traits\HandleFormTrait;
use App\Entity\Agent;
use App\Entity\Transaction;
use App\Form\Type\AgentType;
use App\Helper\PropertyExtractor;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AgentController.
 */
class AgentController extends AbstractController
{
    use HandleFormTrait;

    /**
     * @var EntityManagerInterface $em
     */
    public EntityManagerInterface $em;

    /**
     * @var PropertyExtractor $propertyExtractor
     */
    public PropertyExtractor $propertyExtractor;

    /**
     * AgentController constructor.
     *
     * @param EntityManagerInterface $em
     * @param PropertyExtractor      $propertyExtractor
     */
    public function __construct(EntityManagerInterface $em, PropertyExtractor $propertyExtractor)
    {
        $this->em = $em;
        $this->propertyExtractor = $propertyExtractor;
    }

    /**
     * @Route("/agents", name="listing_agents")
     *
     * @return Response
     */
    public function listingAgents(): Response
    {
        $allAgents = $this->em->getRepository(Agent::class)->findAll();
        $properties = $this->propertyExtractor->getProperties(Agent::class);

        return $this->render('agent/listing.html.twig', [
            'allAgents' => $allAgents,
            'properties' => $properties,
        ]);
    }

    /**
     * @Route("/agent/consult/{id}", name="consult_agent")
     *
     * @param Agent $agent
     *
     * @return Response
     */
    public function consultAgent(Agent $agent): Response
    {
        $commissions = $this->em->getRepository(Transaction::class)->findCommissionsByAgent($agent);

        return $this->render('agent/consult.html.twig', [
            'agent' => $agent,
            'commissions' => $commissions,
        ]);
    }

    /**
     * @Route("/agent/new", name="new_agent")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newAgent(Request $request): Response
    {
        $agent = new Agent();

        $form = $this->createForm(AgentType::class, $agent);
        $formProperties = $this->propertyExtractor->getPropertiesForForm(Agent::class);

        if ($this->handleFormSubmission($request, $form)) {
            return $this->redirectToRoute('listing_agents');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
            'formProperties' => $formProperties,
        ]);
    }

    /**
     * @Route("/agent/update/{id}", name="update_agent")
     *
     * @param Request $request
     * @param Agent   $agent
     *
     * @return Response
     */
    public function updateAgent(Request $request, Agent $agent): Response
    {
        $form = $this->createForm(AgentType::class, $agent);
        $formProperties = $this->propertyExtractor->getPropertiesForForm(Agent::class);

        if ($this->handleFormSubmission($request, $form)) {
            return $this->redirectToRoute('listing_agents');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
            'formProperties' => $formProperties,
        ]);
    }

    /**
     * @Route("/agent/delete/{id}", name="delete_agent")
     *
     * @param Agent $agent
     *
     * @return RedirectResponse
     */
    public function deleteAgent(Agent $agent): RedirectResponse
    {
        /** @var Transaction[] $transactions */
        $transactions = $this->em->getRepository(Transaction::class)->findByAgent($agent);

        foreach ($transactions as $transaction) {
            $this->em->remove($transaction);
        }

        $this->em->remove($agent);
        $this->em->flush();

        return $this->redirectToRoute('listing_agents');
    }
}
