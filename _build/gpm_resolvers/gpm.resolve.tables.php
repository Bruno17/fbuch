<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package fbuch
 * @subpackage build
 *
 * @var mixed $object
 * @var modX $modx
 * @var array $options
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch/') . 'model/';
            
            $modx->addPackage('fbuch', $modelPath, null);


            $manager = $modx->getManager();

            $manager->createObjectContainer('fbuchFahrt');
            $manager->createObjectContainer('fbuchDestination');
            $manager->createObjectContainer('fbuchDate');
            $manager->createObjectContainer('fbuchDateNames');
            $manager->createObjectContainer('fbuchDateInvited');
            $manager->createObjectContainer('fbuchDateComment');
            $manager->createObjectContainer('fbuchFahrtNames');
            $manager->createObjectContainer('fbuchBoot');
            $manager->createObjectContainer('fbuchBootAllowedNames');
            $manager->createObjectContainer('fbuchBootComment');
            $manager->createObjectContainer('fbuchBootRiggerung');
            $manager->createObjectContainer('fbuchBootSetting');
            $manager->createObjectContainer('fbuchBootsGattung');
            $manager->createObjectContainer('fbuchBootsNutzergruppe');
            $manager->createObjectContainer('fbuchBootsNutzergruppenMembers');
            $manager->createObjectContainer('fbuchMailinglist');
            $manager->createObjectContainer('fbuchMailinglistNames');
            $manager->createObjectContainer('fbuchDateType');
            $manager->createObjectContainer('fbuchCompetencyLevel');
            $manager->createObjectContainer('mvMemberState');
            $manager->createObjectContainer('mvMember');
            $manager->createObjectContainer('mvRole');
            $manager->createObjectContainer('mvMemberGroup');
            $manager->createObjectContainer('mvMemberRoleLink');
            $manager->createObjectContainer('mvMemberFilter');
            $manager->createObjectContainer('mvExportFilter');
            $manager->createObjectContainer('mvMail');
            $manager->createObjectContainer('mvFamily');
            $manager->createObjectContainer('mvBeitragstyp');
            $manager->createObjectContainer('mvMailLog');
            $manager->createObjectContainer('mvMailLogRecipient');
            $manager->createObjectContainer('mvMimeType');
            $manager->createObjectContainer('mvDoorAccesscode');

            break;
    }
}

return true;