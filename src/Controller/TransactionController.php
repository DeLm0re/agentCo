<?php

namespace App\Controller;

use App\Controller\Traits\HandleFormTrait;
use App\Entity\Transaction;
use App\Form\Type\TransactionType;
use App\Helper\PropertyExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TransactionController.
 */
class TransactionController extends AbstractController
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
     * TransactionController constructor.
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
     * @Route("/transactions", name="listing_transactions")
     *
     * @return Response
     */
    public function listingTransactions(): Response
    {
        $allTransactions = $this->em->getRepository(Transaction::class)->findAll();
        $properties = $this->propertyExtractor->getProperties(Transaction::class);

        return $this->render('transaction/listing.html.twig', [
            'allTransactions' => $allTransactions,
            'properties' => $properties,
        ]);
    }

    /**
     * @Route("/transaction/new", name="new_transaction")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newTransaction(Request $request): Response
    {
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $formProperties = $this->propertyExtractor->getPropertiesForForm(Transaction::class);

        if ($this->handleFormSubmission($request, $form)) {
            return $this->redirectToRoute('listing_transactions');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
            'formProperties' => $formProperties,
        ]);
    }

    /**
     * @Route("/transaction/update/{id}", name="update_transaction")
     *
     * @param Request     $request
     * @param Transaction $transaction
     *
     * @return Response
     */
    public function updateTransaction(Request $request, Transaction $transaction): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $formProperties = $this->propertyExtractor->getPropertiesForForm(Transaction::class);

        if ($this->handleFormSubmission($request, $form)) {
            return $this->redirectToRoute('listing_transactions');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
            'formProperties' => $formProperties,
        ]);
    }

    /**
     * @Route("/transaction/delete/{id}", name="delete_transaction")
     *
     * @param Transaction $transaction
     *
     * @return RedirectResponse
     */
    public function deleteTransaction(Transaction $transaction): RedirectResponse
    {
        $this->em->remove($transaction);
        $this->em->flush();

        return $this->redirectToRoute('listing_transactions');
    }
}
