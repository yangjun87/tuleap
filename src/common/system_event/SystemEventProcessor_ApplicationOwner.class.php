<?php
/**
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
require_once 'SystemEventProcessor.class.php';

class SystemEventProcessor_ApplicationOwner extends SystemEventProcessor {

    public function getOwner() {
        return SystemEvent::OWNER_APP;
    }

    protected function postEventsActions(array $executed_events_ids, $queue_name) {
        $this->ensureWorkerIsRunning();
        $params = array(
            'executed_events_ids'  => $executed_events_ids,
            'queue_name'           => $queue_name
        );

        EventManager::instance()->processEvent(
            Event::POST_SYSTEM_EVENTS_ACTIONS,
            $params
        );
    }

    private function ensureWorkerIsRunning()
    {
        $this->logger->debug("Check if backend worker is running");
        if (ForgeConfig::get('sys_async_emails') !== false && ! $this->isWorkerRunning()) {
            $this->logger->info("Start backend notifier");
            try {
                $command = new System_Command();
                $command->exec('/usr/share/tuleap/src/utils/worker.php >/dev/null 2>/dev/null &');
            } catch (System_Command_CommandException $exception) {
                $this->logger->error("Unable to launch backend worker: ".$exception->getMessage());
            }
        }
    }

    private function isWorkerRunning()
    {
        if (file_exists(\Tuleap\Queue\Worker::PID_FILE_PATH)) {
            $pid = (int) trim(file_get_contents(\Tuleap\Queue\Worker::PID_FILE_PATH));
            $ret = posix_kill($pid, SIG_DFL);
            return $ret === true;
        }
    }

    public function getProcessOwner() {
        return ForgeConfig::get('sys_http_user');
    }
}
