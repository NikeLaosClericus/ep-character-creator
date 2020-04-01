<?php
declare(strict_types=1);

namespace App\Creator\Atoms;

use App\Models\Aptitude;

/**
 * A character's Aptitude
 *
 * This determines unskilled rolls, and is the base value for skills.
 * Normally has a default max of 30, but can be modified by many things.
 *
 * @author reinhardt
 */
class EPAptitude extends EPAtom{
    static $COGNITION = 'COG';
    static $COORDINATION = 'COO';
    static $INTUITION = 'INT';
    static $REFLEXS = 'REF';
    static $SAVVY = 'SAV';
    static $SOMATICS = 'SOM';
    static $WILLPOWER  = 'WIL';

    /**
     * @var Aptitude
     */
    protected $model;

    /**
     * @var int
     */
    public $value;

    public $maxEgoValue;
    public $maxEgoValueMorphMod;
    public $maxEgoValueTraitMod;
    public $maxEgoValueBackgroundMod;
    public $maxEgoValueFactionMod;
    public $maxEgoValueSoftgearMod;
    public $maxEgoValuePsyMod;

    public $minEgoValue;
    public $minEgoValueMorphMod;
    public $minEgoValueTraitMod;
    public $minEgoValueBackgroundMod;
    public $minEgoValueFactionMod;
    public $minEgoValueSoftgearMod;
    public $minEgoValuePsyMod;

    public $maxMorphValue;
    public $maxMorphValueMorphMod;
    public $maxMorphValueTraitMod;
    public $maxMorphValueBackgroundMod;
    public $maxMorphValueFactionMod;
    public $maxMorphValueSoftgearMod;
    public $maxMorphValuePsyMod;

    public $minMorphValue;
    public $minMorphValueMorphMod;
    public $minMorphValueTraitMod;
    public $minMorphValueBackgroundMod;
    public $minMorphValueFactionMod;
    public $minMorphValueSoftgearMod;
    public $minMorphValuePsyMod;

    //Special for feeble negative trait
    public $feebleMax;

    /**
     * @var int
     */
    public $absoluteMaxValue;
    /**
     * @var int
     */
    public $morphMod;
    /**
     * @var int
     */
    public $traitMod;
    /**
     * @var int
     */
    public $backgroundMod;
    /**
     * @var int
     */
    public $factionMod;
    /**
     * @var int
     */
    public $softgearMod;
    /**
     * @var int
     */
    public $psyMod;

    /**
     * TODO:  This is way too much coupling, and should be removed
     * @var EPMorph|null
     */
    public $activMorph;
    /**
     * @var int
     */
    public $maxValue;
    /**
     * @var int
     */
    public $minValue;

    function getMaxEgoValue(){
        $res =  $this->maxEgoValue + $this->maxEgoValueMorphMod + $this->maxEgoValueTraitMod +
                $this->maxEgoValueBackgroundMod + $this->maxEgoValueFactionMod +
                $this->maxEgoValueSoftgearMod + $this->maxEgoValuePsyMod;
        // Special case Feeble negative trait
        if ($this->feebleMax){
            $res = min(4,$res);
        }
        return min($res,$this->absoluteMaxValue);
    }
    function getMinEgoValue(){
        return  $this->minEgoValue + $this->minEgoValueMorphMod + $this->minEgoValueTraitMod +
                $this->minEgoValueBackgroundMod + $this->minEgoValueFactionMod +
                $this->minEgoValueSoftgearMod + $this->minEgoValuePsyMod;
    }
    function getMaxMorphValue(){
        $res =  $this->maxMorphValue + $this->maxMorphValueMorphMod + $this->maxMorphValueTraitMod +
                $this->maxMorphValueBackgroundMod + $this->maxMorphValueFactionMod +
                $this->maxMorphValueSoftgearMod + $this->maxMorphValuePsyMod;
        // Special case Feeble negative trait
        if ($this->feebleMax){
            $res = min(10,$res);
        }
        return min($res,$this->absoluteMaxValue);
    }
    function getMinMorphValue(){
        return  $this->minMorphValue + $this->minMorphValueMorphMod + $this->minMorphValueTraitMod +
                $this->minMorphValueBackgroundMod + $this->minMorphValueFactionMod +
                $this->minMorphValueSoftgearMod + $this->minMorphValuePsyMod;
    }
    function getValue(){
        $res = $this->value + $this->backgroundMod + $this->factionMod;
        $res = min($res,$this->getMaxEgoValue());
        $res += $this->traitMod + $this->softgearMod + $this->psyMod;

        if ($this->activMorph){
            $res += $this->morphMod;
            $res = min($res,$this->getMaxMorphValue());
        }

        return min($res,$this->absoluteMaxValue);
    }
    function getValueForCpCost(){
        $res = $this->value + $this->backgroundMod + $this->factionMod;
        $res = min($res,$this->getMaxEgoValue());

        return $res;
    }
    function getEgoValue(){
        $res = $this->value + $this->backgroundMod + $this->factionMod;
        $res = min($res,$this->getMaxEgoValue());
        $res += $this->softgearMod + $this->psyMod;

        return min($res,$this->absoluteMaxValue);
    }
    function getSavePack(): array
    {
        $savePack = parent::getSavePack();

        $savePack['value'] =  $this->value;
        $savePack['maxValue'] =  $this->maxValue;
        $savePack['minValue'] =  $this->minValue;
        $savePack['morphMod'] =  $this->morphMod;
        $savePack['traitMod'] =  $this->traitMod;
        $savePack['backgroundMod'] =  $this->backgroundMod;
        $savePack['factionMod'] =  $this->factionMod;
        $savePack['softgearMod'] =  $this->softgearMod;
        $savePack['psyMod'] =  $this->psyMod;

        return $savePack;
    }

    /**
     * @param array $an_array
     * @return EPAptitude
     */
    public static function __set_state(array $an_array)
    {
        $object = new self(Aptitude::whereName((string)$an_array['name'])->first());
        parent::set_state_helper($object, $an_array);

        $object->value         = (int)$an_array['value'];
        $object->maxValue      = (int)$an_array['maxValue'];
        $object->minValue      = (int)$an_array['minValue'];
        $object->morphMod      = (int)$an_array['morphMod'];
        $object->traitMod      = (int)$an_array['traitMod'];
        $object->backgroundMod = (int)$an_array['backgroundMod'];
        $object->factionMod    = (int)$an_array['factionMod'];
        $object->softgearMod   = (int)$an_array['softgearMod'];
        $object->psyMod        = (int)$an_array['psyMod'];

        return $object;
    }

    /**
     * EPAptitude constructor.
     * @param Aptitude $model
     */
    function __construct(Aptitude $model) {
        parent::__construct("Unused", "");
        $this->model = $model;

        $this->value            = config('epcc.AptitudesMinValue');
        $this->morphMod = 0;
        $this->traitMod = 0;
        $this->backgroundMod = 0;
        $this->factionMod = 0;
        $this->softgearMod = 0;
        $this->psyMod = 0;
        $this->maxValue         = config('epcc.AptitudesMaxValue');
        $this->minValue         = config('epcc.AptitudesMinValue');
        $this->minEgoValue      = config('epcc.AptitudesMinValue');
        $this->maxEgoValue      = config('epcc.AptitudesMaxValue');
        $this->minMorphValue    = config('epcc.AptitudesMinValue');
        $this->maxMorphValue    = config('epcc.AptitudesMaxValue');
        $this->activMorph = null;
        $this->feebleMax = false;
        $this->absoluteMaxValue = config('epcc.AbsoluteAptitudesMaxValue');
    }

    public function getName(): string
    {
        return $this->model->name;
    }

    public function getDescription(): string
    {
        return $this->model->description;
    }

    public function getAbbreviation(): string
    {
        return $this->model->abbreviation;
    }

    /**
     * Match identical Aptitudes, even if atom Uids differ
     *
     * This is more expensive than EPAtom's version, but catches duplicate Psy with different Uids.
     * @param EPAptitude $atom
     * @return bool
     */
    public function match($atom): bool
    {
        return $this->model->getKey() === $atom->model->getKey();
    }
}
