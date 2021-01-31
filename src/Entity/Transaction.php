<?php

namespace App\Entity;

use App\Entity\Traits\TimestampsTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transaction.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 * @ORM\Table(name="transaction")
 * @ORM\HasLifecycleCallbacks()
 */
class Transaction
{
    use TimestampsTrait;

    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private Uuid $id;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Agent", cascade={"persist"})
     * @ORM\JoinColumn(name="principal_agent_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotBlank(message="transaction.principalAgent.not_blank")
     */
    private Agent $principalAgent;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Agent", cascade={"persist"})
     * @ORM\JoinColumn(name="associate_agent_id", referencedColumnName="id", nullable=false)
     *
     * @Assert\NotBlank(message="transaction.associateAgent.not_blank")
     */
    private Agent $associateAgent;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=false)
     *
     * @Assert\NotBlank(message="transaction.amount.not_blank")
     */
    private float $amount;

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return Agent
     */
    public function getPrincipalAgent(): Agent
    {
        return $this->principalAgent;
    }

    /**
     * @param Agent $principalAgent
     *
     * @return self
     */
    public function setPrincipalAgent(Agent $principalAgent): self
    {
        $this->principalAgent = $principalAgent;

        return $this;
    }

    /**
     * @return Agent
     */
    public function getAssociateAgent(): Agent
    {
        return $this->associateAgent;
    }

    /**
     * @param Agent $associateAgent
     *
     * @return self
     */
    public function setAssociateAgent(Agent $associateAgent): self
    {
        $this->associateAgent = $associateAgent;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return self
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
