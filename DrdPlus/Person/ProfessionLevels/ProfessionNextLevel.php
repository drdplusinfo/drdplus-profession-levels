<?php
namespace DrdPlus\Person\ProfessionLevels;

use DrdPlus\Professions\Profession;
use DrdPlus\Properties\Base\Agility;
use DrdPlus\Properties\Base\BaseProperty;
use DrdPlus\Properties\Base\Charisma;
use DrdPlus\Properties\Base\Intelligence;
use DrdPlus\Properties\Base\Knack;
use DrdPlus\Properties\Base\Strength;
use DrdPlus\Properties\Base\Will;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(uniqueConstraints={})
 */
class ProfessionNextLevel extends ProfessionLevel
{
    /**
     * @var ProfessionLevels
     * @ORM\ManyToOne(targetEntity="ProfessionLevels", inversedBy="professionNextLevels", cascade={"persist"})
     */
    private $professionLevels;

    /**
     * @param Profession $profession
     * @param LevelRank $nextLevelRank
     * @param Strength $strengthIncrement
     * @param Agility $agilityIncrement
     * @param Knack $knackIncrement
     * @param Will $willIncrement
     * @param Intelligence $intelligenceIncrement
     * @param Charisma $charismaIncrement
     * @param \DateTimeImmutable|null $levelUpAt
     * @return ProfessionNextLevel
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MaximumLevelExceeded
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\InvalidNextLevelPropertiesSum
     */
    public static function createNextLevel(
        Profession $profession,
        LevelRank $nextLevelRank,
        Strength $strengthIncrement,
        Agility $agilityIncrement,
        Knack $knackIncrement,
        Will $willIncrement,
        Intelligence $intelligenceIncrement,
        Charisma $charismaIncrement,
        \DateTimeImmutable $levelUpAt = null
    ): ProfessionNextLevel
    {
        return new static(
            $profession, $nextLevelRank, $strengthIncrement, $agilityIncrement, $knackIncrement,
            $willIncrement, $intelligenceIncrement, $charismaIncrement, $levelUpAt
        );
    }

    const MINIMUM_NEXT_LEVEL = 2;
    const MAXIMUM_NEXT_LEVEL = 21;

    /**
     * @param LevelRank $levelRank
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MinimumLevelExceeded
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\MaximumLevelExceeded
     */
    protected function checkLevelRank(LevelRank $levelRank)
    {
        if ($levelRank->getValue() < self::MINIMUM_NEXT_LEVEL) {
            throw new Exceptions\MinimumLevelExceeded(
                'Next level can not be lesser than ' . self::MINIMUM_NEXT_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
        if ($levelRank->getValue() > self::MAXIMUM_NEXT_LEVEL) {
            throw new Exceptions\MaximumLevelExceeded(
                'Level can not be greater than ' . self::MAXIMUM_NEXT_LEVEL . ", got {$levelRank->getValue()}"
            );
        }
    }

    const MAX_NEXT_LEVEL_PROPERTY_MODIFIER = 1;

    /**
     * @param BaseProperty $baseProperty
     * @param Profession $profession
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\NegativeNextLevelProperty
     * @throws \DrdPlus\Person\ProfessionLevels\Exceptions\TooHighNextLevelPropertyIncrement
     */
    protected function checkPropertyIncrement(BaseProperty $baseProperty, Profession $profession)
    {
        if ($baseProperty->getValue() < 0) {
            throw new Exceptions\NegativeNextLevelProperty(
                "Next level property increment can not be negative, got {$baseProperty->getValue()}"
            );
        }
        if ($baseProperty->getValue() > self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER) {
            throw new Exceptions\TooHighNextLevelPropertyIncrement(
                'Next level property increment has to be at most '
                . self::MAX_NEXT_LEVEL_PROPERTY_MODIFIER . ", got {$baseProperty->getValue()}"
            );
        }
    }

    /**
     * @return ProfessionLevels|null
     */
    public function getProfessionLevels():? ProfessionLevels
    {
        return $this->professionLevels;
    }

    /**
     * @param ProfessionLevels $professionLevels
     */
    public function setProfessionLevels(ProfessionLevels $professionLevels)
    {
        $this->professionLevels = $professionLevels;
    }
}