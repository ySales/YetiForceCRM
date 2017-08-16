<?php
/* * *******************************************************************************
 * * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 * ****************************************************************************** */

/** Class to retreive all the Parent Groups of the specified Group
 *
 */
require_once('include/utils/UserInfoUtil.php');

class GetUserGroups
{

	public $user_groups = [];

	//var $userRole='';

	/** to get all the parent vtiger_groups of the specified group
	 * @params $groupId --> Group Id :: Type Integer
	 * @returns updates the parent group in the varibale $parent_groups of the class
	 */
	public function getAllUserGroups($userid)
	{
		//Retreiving from the user2grouptable
		$userGroups = App\PrivilegeUtil::getUserGroups($userid);
		//Setting the User Role
		$userRole = \App\PrivilegeUtil::getRoleByUsers($userid);
		//Retreiving from the vtiger_user2role
		$roleGroups = (new \App\Db\Query())->select('groupid')->from('vtiger_group2role')->where(['roleid' => $userRole])->column();
		//Retreiving from the user2rs
		$roles = \App\PrivilegeUtil::getParentRole($userRole);
		$roles[] = $userRole;
		$rsGroups = (new \App\Db\Query())->select(['groupid'])->from('vtiger_group2rs')->where(['roleandsubid' => $roles])->column();
		$this->user_groups = array_unique(array_merge($this->user_groups, $userGroups, $roleGroups, $rsGroups));
		foreach ($this->user_groups as $groupId) {
			foreach (App\PrivilegeUtil::getParentGroups($groupId) as $parentGroupId) {
				if (!in_array($parentGroupId, $this->user_groups)) {
					$this->user_groups[] = $parentGroupId;
				}
			}
		}
	}
}
