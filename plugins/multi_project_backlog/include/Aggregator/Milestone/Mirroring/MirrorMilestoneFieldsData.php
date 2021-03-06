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

final class MirrorMilestoneFieldsData
{
    /**
     * @var int
     * @psalm-readonly
     */
    private $title_field_id;
    /**
     * @var \Tracker_Artifact_ChangesetValue_String
     */
    private $title_changeset_value;

    private function __construct(int $title_field_id, \Tracker_Artifact_ChangesetValue_String $title_changeset_value)
    {
        $this->title_field_id        = $title_field_id;
        $this->title_changeset_value = $title_changeset_value;
    }

    public static function fromCopiedValuesAndTargetFields(
        CopiedValues $copied_values,
        TargetFields $target_fields
    ): self {
        return new self($target_fields->getTitleFieldId(), $copied_values->getTitleValue());
    }

    /**
     * @return array<int,string>
     */
    public function toFieldsDataArray(): array
    {
        return [$this->title_field_id => $this->title_changeset_value->getValue()];
    }
}
