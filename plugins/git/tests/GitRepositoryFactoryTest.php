<?php
/**
 * Copyright (c) Enalean, 2011. All Rights Reserved.
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

require_once(dirname(__FILE__).'/../include/constants.php');
require_once dirname(__FILE__).'/../include/GitRepositoryFactory.class.php';

Mock::generate('GitDao');
Mock::generate('ProjectManager');
Mock::generate('Project');

class GitRepositoryFactoryTest extends UnitTestCase {
    
    function testGetRepositoryFromFullPath() {
        $dao            = new MockGitDao();
        $projectManager = new MockProjectManager();
        $project        = new MockProject();
        
        $project->setReturnValue('getID', 101);
        $project->setReturnValue('getUnixName', 'garden');
        
        $projectManager->setReturnValue('getProjectByUnixName', $project, array('garden'));
        
        $factory        = new GitRepositoryFactory($dao, $projectManager);
        
        $dao->expectOnce('searchProjectRepositoryByPath', array(101, 'garden/u/manuel/grou/ping/diskinstaller.git'));
        $dao->setReturnValue('searchProjectRepositoryByPath', new MockDataAccessResult());
        
        $factory->getFromFullPath('/data/tuleap/gitolite/repositories/garden/u/manuel/grou/ping/diskinstaller.git');
    }
    
    function testGetRepositoryFromFullPathAndGitRoot() {
        $dao            = new MockGitDao();
        $projectManager = new MockProjectManager();
        $project        = new MockProject();
        
        $project->setReturnValue('getID', 101);
        $project->setReturnValue('getUnixName', 'garden');
        
        $projectManager->setReturnValue('getProjectByUnixName', $project, array('garden'));
        
        $factory        = new GitRepositoryFactory($dao, $projectManager);
        
        $dao->expectOnce('searchProjectRepositoryByPath', array(101, 'garden/diskinstaller.git'));
        $dao->setReturnValue('searchProjectRepositoryByPath', new MockDataAccessResult());
        
        $factory->getFromFullPath('/data/tuleap/gitroot/garden/diskinstaller.git');
    }
}

class GitRepositoryFactory_getGerritRepositoriesWithPermissionsForUGroupTest extends TuleapTestCase {

    public function setUp() {
        parent::setUp();
        $this->dao = mock('GitDao');
        $this->project_manager = mock('ProjectManager');

        $this->factory = new GitRepositoryFactory($this->dao, $this->project_manager);

        $this->project_id = 320;
        $this->ugroup_id = 115;

        $this->user_ugroups = array(404, 416);
        $this->user         = stub('User')->getUgroups($this->project_id, null)->returns($this->user_ugroups);

        $this->project = stub('Project')->getID()->returns($this->project_id);
        $this->ugroup  = stub('UGroup')->getId()->returns($this->ugroup_id);
    }

    public function itCallsDaoWithArguments() {
        $ugroups = array(404, 416, 115);
        expect($this->dao)->searchGerritRepositoriesWithPermissionsForUGroup($this->project_id, $ugroups)->once();
        stub($this->dao)->searchGerritRepositoriesWithPermissionsForUGroup()->returnsEmptyDar();
        $this->factory->getGerritRepositoriesWithPermissionsForUGroup($this->project, $this->ugroup, $this->user);
    }

    public function itHydratesTheRepositoriesWithDao() {
        $db_row_for_repo_12 = array('repository_id' => 12, 'permission_type' => Git::PERM_READ, 'ugroup_id' => 115);
        $db_row_for_repo_23 = array('repository_id' => 23, 'permission_type' => Git::PERM_READ, 'ugroup_id' => 115);

        stub($this->dao)->searchGerritRepositoriesWithPermissionsForUGroup()->returnsDar(
            $db_row_for_repo_12,
            $db_row_for_repo_23
        );
        expect($this->dao)->instanciateFromRow()->count(2);
        expect($this->dao)->instanciateFromRow($db_row_for_repo_12)->at(0);
        expect($this->dao)->instanciateFromRow($db_row_for_repo_23)->at(1);
        stub($this->dao)->instanciateFromRow()->returns(mock('GitRepository'));

        $this->factory->getGerritRepositoriesWithPermissionsForUGroup($this->project, $this->ugroup, $this->user);
    }

    public function itReturnsOneRepositoryWithOnePermission() {
        stub($this->dao)->searchGerritRepositoriesWithPermissionsForUGroup()->returnsDar(
            array(
                'repository_id'   => 12,
                'permission_type' => Git::PERM_READ,
                'ugroup_id'       => 115
            )
        );

        $repository = mock('GitRepository');

        stub($this->dao)->instanciateFromRow()->returns($repository);

        $git_with_permission = $this->factory->getGerritRepositoriesWithPermissionsForUGroup($this->project, $this->ugroup, $this->user);

        $this->assertEqual(
            $git_with_permission,
            array(
                12 => new GitRepositoryWithPermissions(
                    $repository,
                    array(
                        Git::PERM_READ          => array(115),
                        Git::PERM_WRITE         => array(),
                        Git::PERM_WPLUS         => array(),
                        Git::SPECIAL_PERM_ADMIN => array(),
                    )
                )
            )
        );
    }

    public function itReturnsOneRepositoryWithTwoPermissions() {
        stub($this->dao)->searchGerritRepositoriesWithPermissionsForUGroup()->returnsDar(
            array(
                'repository_id'   => 12,
                'permission_type' => Git::PERM_READ,
                'ugroup_id'       => 115
            ),
            array(
                'repository_id'   => 12,
                'permission_type' => Git::PERM_WRITE,
                'ugroup_id'       => 115
            )
        );

        $repository = mock('GitRepository');

        stub($this->dao)->instanciateFromRow()->returns($repository);

        $git_with_permission = $this->factory->getGerritRepositoriesWithPermissionsForUGroup($this->project, $this->ugroup, $this->user);

        $this->assertEqual(
            $git_with_permission,
            array(
                12 => new GitRepositoryWithPermissions(
                    $repository,
                    array(
                        Git::PERM_READ          => array(115),
                        Git::PERM_WRITE         => array(115),
                        Git::PERM_WPLUS         => array(),
                        Git::SPECIAL_PERM_ADMIN => array(),
                    )
                )
            )
        );
    }

    public function itReturnsOneRepositoryWithTwoGroupsForOnePermissionType() {
        stub($this->dao)->searchGerritRepositoriesWithPermissionsForUGroup()->returnsDar(
            array(
                'repository_id'   => 12,
                'permission_type' => Git::PERM_READ,
                'ugroup_id'       => 115
            ),
            array(
                'repository_id'   => 12,
                'permission_type' => Git::PERM_READ,
                'ugroup_id'       => 120
            )
        );

        $repository = mock('GitRepository');

        stub($this->dao)->instanciateFromRow()->returns($repository);

        $git_with_permission = $this->factory->getGerritRepositoriesWithPermissionsForUGroup($this->project, $this->ugroup, $this->user);

        $this->assertEqual(
            $git_with_permission,
            array(
                12 => new GitRepositoryWithPermissions(
                    $repository,
                    array(
                        Git::PERM_READ          => array(115, 120),
                        Git::PERM_WRITE         => array(),
                        Git::PERM_WPLUS         => array(),
                        Git::SPECIAL_PERM_ADMIN => array()
                    )
                )
            )
        );

        $this->assertEqual(
            $git_with_permission[12]->getPermissions(),
            array(
                Git::PERM_READ          => array(115, 120),
                Git::PERM_WRITE         => array(),
                Git::PERM_WPLUS         => array(),
                Git::SPECIAL_PERM_ADMIN => array()
            )
        );
    }
}

?>
