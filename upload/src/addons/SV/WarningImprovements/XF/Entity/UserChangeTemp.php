<?php

namespace SV\WarningImprovements\XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Extends \XF\Entity\UserChangeTemp
 */
class UserChangeTemp extends XFCP_UserChangeTemp
{
    public function getName()
    {
        $name = 'n_a';

        switch ($this->action_type)
        {
            case 'groups':
                $name = 'sv_warning_action_added_to_user_groups';
                break;

            case 'field':
                $name = 'discouraged';
                break;
        }

        return \XF::phrase($name);
    }

    public function getResult()
    {
        $result = 'n_a';

        switch ($this->action_type)
        {
            case 'groups':
                $result = 'sv_warning_action_added_to_user_groups';
                break;

            case 'field':
                $result = $this->new_value;
                break;
        }

        return \XF::phrase($result);
    }

    public function getIsExpired()
    {
        return ($this->expiry_date < \XF::$time);
    }

    public function getExpiryDateRounded()
    {
        $expiryDateRound = $this->expiry_date;

        if (!empty($expiryDateRound))
        {
            $expiryDateRound = ($expiryDateRound - ($expiryDateRound % 3600)) + 3600;
        }

        return $expiryDateRound;
    }

    public function canEditWarningActions(&$error = '')
    {
        $visitor = \XF::visitor();

        if (!$visitor->user_id)
        {
            return false;
        }

        return $visitor->hasPermission('general', 'sv_editWarningActions');
    }

    protected function _postSave()
    {
        parent::_postSave();

        $this->_getWarningRepo()->updatePendingExpiryFor($this->User, true);
    }

    protected function _postDelete()
    {
        parent::_postDelete();

        $this->_getWarningRepo()->updatePendingExpiryFor($this->User, true);
    }

    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->getters['name'] = true;
        $structure->getters['result'] = true;
        $structure->getters['is_expired'] = true;
        $structure->getters['expiry_date_rounded'] = true;

        return $structure;
    }

    /**
     * @return \XF\Mvc\Entity\Repository|\XF\Repository\Warning|\SV\WarningImprovements\XF\Repository\Warning
     */
    protected function _getWarningRepo()
    {
        return $this->repository('XF:Warning');
    }
}
