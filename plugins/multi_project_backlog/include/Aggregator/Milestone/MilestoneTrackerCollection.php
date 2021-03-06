<?php
/**
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

namespace Tuleap\MultiProjectBacklog\Aggregator\Milestone;

final class MilestoneTrackerCollection
{
    /**
     * @var \Tracker[]
     * @psalm-readonly
     */
    private $milestone_trackers;

    /**
     * @param \Tracker[] $milestone_trackers
     */
    public function __construct(array $milestone_trackers)
    {
        $this->milestone_trackers = $milestone_trackers;
    }

    /**
     * @return int[]
     * @psalm-mutation-free
     */
    public function getTrackerIds(): array
    {
        return self::extractTrackerIDs($this->milestone_trackers);
    }

    /**
     * @return \Tracker[]
     * @psalm-mutation-free
     */
    public function getMilestoneTrackers(): array
    {
        return $this->milestone_trackers;
    }

    /**
     * @param \Tracker[] $trackers
     * @return int[]
     * @psalm-pure
     */
    private static function extractTrackerIDs(array $trackers): array
    {
        return array_map(
            static function (\Tracker $tracker) {
                return $tracker->getId();
            },
            $trackers
        );
    }
}
