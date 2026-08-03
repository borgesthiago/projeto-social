<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait TenantOwnedTrait
{
    #[ORM\ManyToOne, ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    public ?ProjetoSocial $projeto = null;
}
