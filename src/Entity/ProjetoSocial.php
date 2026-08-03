<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProjetoSocial
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column] public ?int $id = null;
    #[ORM\Column(length: 140)] public string $nome = '';
    #[ORM\Column(length: 80, unique: true)] public string $slug = '';
    #[ORM\Column(length: 180, nullable: true)] public ?string $email = null;
    #[ORM\Column(length: 14, unique: true, nullable: true)] public ?string $cnpj = null;
    #[ORM\Column(length: 30, nullable: true)] public ?string $telefone = null;
    #[ORM\Column(length: 255, nullable: true)] public ?string $logoPath = null;
    #[ORM\Column(length: 255, nullable: true)] public ?string $bannerPath = null;
    #[ORM\Column(length: 7)] public string $corPrimaria = '#1769e0';
    #[ORM\Column(length: 7)] public string $corSecundaria = '#15a884';
    #[ORM\Column(length: 7)] public string $corSidebar = '#ffffff';
    #[ORM\Column(length: 7)] public string $corTextoBotao = '#ffffff';
    #[ORM\Column(length: 160, nullable: true)] public ?string $slogan = null;
    #[ORM\Column] public bool $ativo = true;
    #[ORM\Column(type: 'datetime_immutable')] public \DateTimeImmutable $criadoEm;

    public function __construct() { $this->criadoEm = new \DateTimeImmutable(); }
    public function __toString(): string { return $this->nome; }
}
