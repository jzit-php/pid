<?php

namespace JzIT\Pid\Business;

use Generated\Transfer\Pid\PidStatsTransfer;
use JzIT\Pid\Business\Model\Writer\PidStatWriterInterface;
use JzIT\Pid\Persistence\PidEntityManagerInterface;
use JzIT\Pid\Persistence\PidRepositoryInterface;

class PidFacade implements PidFacadeInterface
{
    /**
     * @var \JzIT\Pid\Business\Model\Writer\PidStatWriterInterface
     */
    protected $statWriter;

    /**
     * PidFacade constructor.
     *
     * @param \JzIT\Pid\Business\Model\Writer\PidStatWriterInterface $pidStatWriter
     */
    public function __construct(PidStatWriterInterface $pidStatWriter)
    {
        $this->statWriter = $pidStatWriter;
    }

    /**
     * @param \Generated\Transfer\Pid\PidStatsTransfer $pidStatsTransfer
     *
     * @return \Generated\Transfer\Pid\PidStatsTransfer
     */
    public function writePidStat(PidStatsTransfer $pidStatsTransfer): PidStatsTransfer
    {
        return $this->statWriter->writeStats($pidStatsTransfer);
    }
}
