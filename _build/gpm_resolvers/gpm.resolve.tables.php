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
            $manager->createObjectContainer('fbuchDate');
            $manager->createObjectContainer('fbuchDateNames');
            $manager->createObjectContainer('fbuchDateInvited');
            $manager->createObjectContainer('fbuchDateComment');
            $manager->createObjectContainer('fbuchNames');
            $manager->createObjectContainer('fbuchFahrtNames');
            $manager->createObjectContainer('fbuchBoot');
            $manager->createObjectContainer('fbuchBootAllowedNames');
            $manager->createObjectContainer('fbuchBootComment');
            $manager->createObjectContainer('fbuchBootSetting');
            $manager->createObjectContainer('fbuchBootsGattung');
            $manager->createObjectContainer('fbuchBootsNutzergruppe');
            $manager->createObjectContainer('fbuchMailinglist');
            $manager->createObjectContainer('fbuchMailinglistNames');

            break;
    }
}

return true;