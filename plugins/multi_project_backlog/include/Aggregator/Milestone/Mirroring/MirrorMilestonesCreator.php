<?php
/*
 * Copyright (c) Enalean, 2020-Present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Tuleap\MultiProjectBacklog\Aggregator\Milestone\Mirroring;

use Tuleap\DB\DBTransactionExecutor;
use Tuleap\MultiProjectBacklog\Aggregator\Milestone\ContributorMilestoneTrackerCollection;

class MirrorMilestonesCreator
{
    /**
     * @var DBTransactionExecutor
     */
    private $transaction_executor;
    /**
     * @var TargetFieldsGatherer
     */
    private $target_fields_gatherer;
    /**
     * @var \Tracker_ArtifactCreator
     */
    private $artifact_creator;

    public function __construct(
        DBTransactionExecutor $transaction_executor,
        TargetFieldsGatherer $target_fields_gatherer,
        \Tracker_ArtifactCreator $artifact_creator
    ) {
        $this->transaction_executor   = $transaction_executor;
        $this->target_fields_gatherer = $target_fields_gatherer;
        $this->artifact_creator       = $artifact_creator;
    }

    /**
     * @throws MirrorMilestoneCreationException
     */
    public function createMirrors(
        CopiedValues $copied_values,
        ContributorMilestoneTrackerCollection $contributor_milestones,
        \PFUser $current_user
    ): void {
        $this->transaction_executor->execute(
            function () use ($copied_values, $contributor_milestones, $current_user) {
                foreach ($contributor_milestones->getMilestoneTrackers() as $milestone_tracker) {
                    $target_fields = $this->target_fields_gatherer->gather($milestone_tracker);
                    $fields_data   = MirrorMilestoneFieldsData::fromCopiedValuesAndTargetFields(
                        $copied_values,
                        $target_fields
                    );
                    $result        = $this->artifact_creator->create(
                        $milestone_tracker,
                        $fields_data->toFieldsDataArray(),
                        $current_user,
                        $copied_values->getSubmittedOn(),
                        false,
                        false
                    );
                    if ($result === false) {
                        throw new MirrorMilestoneCreationException($copied_values->getArtifactId());
                    }
                }
            }
        );
    }
}
