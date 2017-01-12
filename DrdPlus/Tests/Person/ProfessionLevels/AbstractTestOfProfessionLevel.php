<?php
namespace DrdPlus\Tests\Person\ProfessionLevels;

use DrdPlus\Codes\ProfessionCode;
use DrdPlus\Codes\PropertyCode;
use DrdPlus\Professions\Fighter;
use DrdPlus\Professions\Priest;
use DrdPlus\Professions\Ranger;
use DrdPlus\Professions\Theurgist;
use DrdPlus\Professions\Thief;
use DrdPlus\Professions\Wizard;
use \DrdPlus\Professions\Profession;
use Granam\Tests\Tools\TestWithMockery;
use Granam\Tools\ValueDescriber;
use Mockery\MockInterface;

abstract class AbstractTestOfProfessionLevel extends TestWithMockery
{

    abstract public function I_can_create_it($professionCode);

    abstract public function I_can_create_it_with_default_level_up_at();

    abstract public function I_can_get_level_details($professionCode);

    /**
     * @param string $propertyCode
     * @param string $professionCode
     *
     * @return bool
     */
    protected function isPrimaryProperty($propertyCode, $professionCode)
    {
        return in_array($propertyCode, $this->getPrimaryProperties($professionCode), true);
    }

    /**
     * @param string $professionCode
     * @return string[]
     * @throws \LogicException
     */
    private function getPrimaryProperties($professionCode)
    {
        switch ($professionCode) {
            case ProfessionCode::FIGHTER :
                return [PropertyCode::STRENGTH, PropertyCode::AGILITY];
            case ProfessionCode::THIEF :
                return [PropertyCode::AGILITY, PropertyCode::KNACK];
            case ProfessionCode::RANGER :
                return [PropertyCode::STRENGTH, PropertyCode::KNACK];
            case ProfessionCode::WIZARD :
                return [PropertyCode::WILL, PropertyCode::INTELLIGENCE];
            case ProfessionCode::THEURGIST :
                return [PropertyCode::INTELLIGENCE, PropertyCode::CHARISMA];
            case ProfessionCode::PRIEST :
                return [PropertyCode::WILL, PropertyCode::CHARISMA];
        }
        throw new \LogicException('Unknown profession code ' . ValueDescriber::describe($professionCode));
    }

    /**
     * @param string $professionCode
     * @return MockInterface|Profession|Fighter|Wizard|Priest|Theurgist|Thief|Ranger
     */
    protected function createProfession($professionCode)
    {
        $profession = \Mockery::mock($this->getProfessionClass($professionCode));
        $profession->shouldReceive('isPrimaryProperty')
            ->with($this->type(PropertyCode::class))
            ->andReturnUsing(
                function (PropertyCode $propertyCode) use ($professionCode) {
                    return in_array($propertyCode->getValue(), $this->getPrimaryProperties($professionCode), true);
                }
            );
        $profession->shouldReceive('getPrimaryProperties')
            ->andReturn($this->getPrimaryProperties($professionCode));
        $profession->shouldReceive('getValue')
            ->andReturn($professionCode);

        return $profession;
    }

    /**
     * @param string $professionCode
     * @return string|Profession
     */
    private function getProfessionClass($professionCode)
    {
        $reflection = new \ReflectionClass(Profession::class);
        $namespace = $reflection->getNamespaceName();

        return $namespace . '\\' . ucfirst($professionCode);
    }

}
