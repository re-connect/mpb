<?php

namespace App\Manager;

use App\Entity\Bug;
use App\Entity\Feature;
use App\Mapper\UserRequestMapper;
use App\Traits\UserAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Workflow\WorkflowInterface;

class BugManager extends UserRequestManager
{
    use UserAwareTrait;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly WorkflowInterface $bugLifecycleStateMachine,
    ) {
        parent::__construct($em, $security);
    }

    public function takeOver(Bug $bug): void
    {
        $bug->setAssignee($this->getUser());
        $this->bugLifecycleStateMachine->apply($bug, 'take_over');
        $this->em->flush();
    }

    public function lowerPriority(Bug $bug): void
    {
        $this->bugLifecycleStateMachine->apply($bug, 'lower_priority');
        $this->em->flush();
    }

    public function createBug(string $userAgent): Bug
    {
        $bug = new Bug($userAgent);
        $this->bugLifecycleStateMachine->getMarking($bug);
        $this->create($bug);

        return $bug;
    }

    public function convertToFeature(Bug $bug): Feature
    {
        $feature = UserRequestMapper::mapBugToFeature($bug);
        $this->bugLifecycleStateMachine->apply($bug, 'dismiss');

        $this->em->persist($feature);
        $this->em->flush();

        return $feature;
    }

    public function solve(Bug $bug): void
    {
        $this->bugLifecycleStateMachine->apply($bug, 'solve');
        $this->em->flush();
    }

    public function dismiss(Bug $bug): void
    {
        $this->bugLifecycleStateMachine->apply($bug, 'dismiss');
        $this->em->flush();
    }
}
