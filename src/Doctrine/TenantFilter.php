<?php
namespace App\Doctrine;
use App\Entity\TenantOwnedTrait;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
final class TenantFilter extends SQLFilter {
 public function addFilterConstraint(ClassMetadata $targetEntity,string $alias):string { return in_array(TenantOwnedTrait::class,class_uses($targetEntity->getName()),true) ? sprintf('%s.projeto_id = %s',$alias,$this->getParameter('projeto_id')) : ''; }
}
