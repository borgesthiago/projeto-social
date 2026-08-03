<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity, ORM\Table(name: '`user`'), UniqueEntity(fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column] private ?int $id = null;
    #[ORM\Column(length: 180, unique: true)] private string $email = '';
    #[ORM\Column(length: 120)] private string $nome = '';
    #[ORM\Column] private array $roles = [];
    #[ORM\Column] private string $password = '';
    #[ORM\Column] private bool $ativo = true;
    #[ORM\Column(length: 64, nullable: true, unique: true)] private ?string $conviteToken = null;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)] private ?\DateTimeImmutable $conviteExpiraEm = null;
    #[ORM\ManyToOne, ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')] private ?ProjetoSocial $projeto = null;
    public function getId(): ?int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $v): self { $this->email = mb_strtolower($v); return $this; }
    public function getNome(): string { return $this->nome; }
    public function setNome(string $v): self { $this->nome = $v; return $this; }
    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return array_unique([...$this->roles, 'ROLE_USER']); }
    public function setRoles(array $v): self { $this->roles = $v; return $this; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $v): self { $this->password = $v; return $this; }
    public function isAtivo(): bool { return $this->ativo; }
    public function setAtivo(bool $v): self { $this->ativo = $v; return $this; }
    public function getConviteToken(): ?string { return $this->conviteToken; }
    public function setConviteToken(?string $v): self { $this->conviteToken = $v; return $this; }
    public function getConviteExpiraEm(): ?\DateTimeImmutable { return $this->conviteExpiraEm; }
    public function setConviteExpiraEm(?\DateTimeImmutable $v): self { $this->conviteExpiraEm = $v; return $this; }
    public function isConvitePendente(): bool { return $this->conviteToken !== null; }
    public function getProjeto(): ?ProjetoSocial { return $this->projeto; }
    public function setProjeto(?ProjetoSocial $v): self { $this->projeto = $v; return $this; }
    public function eraseCredentials(): void {}
}
