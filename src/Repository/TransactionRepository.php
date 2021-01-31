<?php

namespace App\Repository;

use App\Entity\Agent;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    /**
     * TransactionRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @param Agent $agent
     *
     * @return array
     */
    public function findByAgent(Agent $agent): array
    {
        // The two following functions use magic keywords that allow doctrine to understand
        $principalTransactions = $this->findByPrincipalAgent($agent);
        $associateTransactions = $this->findByAssociateAgent($agent);

        return \array_merge($principalTransactions, $associateTransactions);
    }

    /**
     * @param Agent $agent
     *
     * @return array|null
     */
    public function findCommissionsByAgent(Agent $agent): ?array
    {
        $commissions = [];
        $commissions['principal'] = $this->findCommissions($agent, 'principalAgent', 85);
        $commissions['associate'] = $this->findCommissions($agent, 'associateAgent', 10);

        $total = 0;
        foreach ($commissions['principal'] as $commission) {
            $total += $commission['commission'];
        }
        $commissions['total']['principal'] = $total;

        $total = 0;
        foreach ($commissions['associate'] as $commission) {
            $total += $commission['commission'];
        }
        $commissions['total']['associate'] = $total;

        $commissions['total']['all'] = $commissions['total']['principal'] + $commissions['total']['associate'];

        return $commissions;
    }

    /**
     * @param Agent  $agent
     * @param string $fieldName
     * @param int    $percent
     *
     * @return array
     */
    private function findCommissions(Agent $agent, string $fieldName, int $percent): array
    {
        $commissions = [];

        /** @var Transaction[] $transactions */
        $transactions = $this->createQueryBuilder('t')
            ->innerJoin(Agent::class, 'a', 'WITH', "t.$fieldName = a")
            ->where('a.lastname = :agentLastName')
            ->setParameter('agentLastName', $agent->getLastname())
            ->getQuery()
            ->getResult();

        foreach ($transactions as $transaction) {
            $commission = [];
            $transactionAmount = $transaction->getAmount();
            $commission['amount'] = $transactionAmount;
            $gain = $transactionAmount * $percent / 100;
            $commission['commission'] = $gain;
            $commission['date'] = $transaction->getUpdatedAt() ?
                $transaction->getUpdatedAt()->format('d/m/Y h:m:s')
                : $transaction->getCreatedAt()->format('d/m/Y h:m:s');

            \array_push($commissions, $commission);
        }

        return $commissions;
    }
}
