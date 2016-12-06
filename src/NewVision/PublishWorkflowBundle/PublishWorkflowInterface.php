<?php
/**
 * This file is part of the NewVisionPublishWorkflow.
 *
 * (c) Nikolay Tumbalev <n.tumbalev@newvision.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NewVision\PublishWorkflowBundle;

/**
 *  Interface for allowing publish
 *
 * @package NewVisionPublishWorkflow
 * @author  Nikolay Tumbalev <n.tumbalev@newvision.bg>
 */
interface PublishWorkflowInterface
{
    /**
     * Gets PublishWorkflow entity
     * {@link \NewVision\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @return integer
     */
    public function getPublishWorkflow();

    /**
     * Sets PublishWorkflow entity
     * {@link \NewVision\PublishWorkflowBundle\Entity\PublishWorkflow object}.
     *
     * @param integer $publishWorkflow
     *
     * @return self
     */
    public function setPublishWorkflow($publishWorkflow);
}
