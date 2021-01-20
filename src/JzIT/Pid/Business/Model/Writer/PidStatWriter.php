<?php

namespace JzIT\Pid\Business\Model\Writer;

use Generated\Transfer\Pid\PidStatsTransfer;
use JzIT\Pid\Persistence\PidEntityManagerInterface;
use JzIT\Pid\Persistence\PidRepositoryInterface;

class PidStatWriter implements PidStatWriterInterface
{
    /**
     * @var \JzIT\Pid\Persistence\PidRepositoryInterface
     */
    protected $repository;

    /**
     * @var \JzIT\Pid\Persistence\PidEntityManagerInterface
     */
    protected $entityManager;

    /**
     * PidFacade constructor.
     *
     * @param \JzIT\Pid\Persistence\PidRepositoryInterface $repository
     * @param \JzIT\Pid\Persistence\PidEntityManagerInterface $entityManager
     */
    public function __construct(PidRepositoryInterface $repository, PidEntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Generated\Transfer\Pid\PidStatsTransfer $pidStatsTransfer
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer
     */
    public function writeStats(PidStatsTransfer $pidStatsTransfer): PidStatsTransfer
    {
        return $this->entityManager->createPidStat($pidStatsTransfer);
    }
}
