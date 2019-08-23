<?php
/*
 * Copyright Enalean (c) 2011, 2012, 2013. All rights reserved.
 *
 * Tuleap and Enalean names and logos are registrated trademarks owned by
 * Enalean SAS. All other trademarks or names are properties of their respective
 * owners.
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

/**
 * Store push information in the database
 */
class Git_Hook_LogPushes {
    /** @var GitDao */
    private $dao;

    public function __construct(GitDao $dao)
    {
        $this->dao = $dao;
    }

    public function executeForRepository(Git_Hook_PushDetails $push_details)
    {
        $this->dao->logGitPush(
            $push_details->getRepository()->getId(),
            $push_details->getUser()->getId(),
            $_SERVER['REQUEST_TIME'],
            count($push_details->getRevisionList()),
            $push_details->getRefname(),
            $push_details->getType(),
            $push_details->getRefnameType()
        );

    }
}

?>
