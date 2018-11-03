<?php
declare(strict_types=1);

use App\Creator\Atoms\EPAtom;
use App\Creator\DisplayHelpers\Panel;
use App\Creator\DisplayHelpers\Helpers;

$currentTraitsList = creator()->getCurrentTraits();
$currentTrait = EPAtom::getAtomByName($currentTraitsList,$_SESSION['currentTraitName']);
if($currentTrait == null){
    $currentTrait = EpDatabase()->getTraitByName($_SESSION['currentTraitName']);
}

$myPanel = new Panel();
$myPanel->startDescriptivePanel($currentTrait->getName());
$myPanel->addDescription($currentTrait->getDescription());
$myPanel->addRawHtml( Helpers::getBMHtml(creator(), $currentTrait->bonusMalus,$currentTrait->getName(),'trait') );
echo $myPanel->getHtml();
echo Panel::endPanel();
