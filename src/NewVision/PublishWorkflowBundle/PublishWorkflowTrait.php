<?php
/**
 * This file is part of the NewVisionPublishWorkflowBundle.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewVision\PublishWorkflowBundle;

/**
 *  A plugin making connection with the PublishWorkflow entity
 *
 * @package NewVisionPublishWorkflowBundle
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */

trait PublishWorkflowTrait
{
    /**
     * A variable making connection PublishWorkflow entity.
     * {@link \NewVision\PublishWorkflowBundle\Entity\PublishWorkflow object}
     *
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="\NewVision\PublishWorkflowBundle\Entity\PublishWorkflow", cascade={"remove","persist"})
     */
    protected $publishWorkflow;

    /**
     * Gets PublishWorkflow entity
     * {@link \NewVision\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @return integer
     */
    public function getPublishWorkflow()
    {
        return $this->publishWorkflow;
    }

    /**
     * Sets PublishWorkflow entity
     * {@link \NewVision\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @param integer $publishWorkflow
     *
     * @return self
     */
    public function setPublishWorkflow($publishWorkflow)
    {
        $this->publishWorkflow = $publishWorkflow;

        return $this;
    }
}
