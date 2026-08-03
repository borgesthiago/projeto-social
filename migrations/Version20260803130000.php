<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20260803130000 extends AbstractMigration {
 public function getDescription():string{return 'Identidade visual personalizada por projeto';}
 public function up(Schema $schema):void{$this->addSql("ALTER TABLE projeto_social ADD logo_path VARCHAR(255) DEFAULT NULL, ADD banner_path VARCHAR(255) DEFAULT NULL, ADD cor_primaria VARCHAR(7) NOT NULL DEFAULT '#1769e0', ADD cor_secundaria VARCHAR(7) NOT NULL DEFAULT '#15a884', ADD cor_sidebar VARCHAR(7) NOT NULL DEFAULT '#ffffff', ADD cor_texto_botao VARCHAR(7) NOT NULL DEFAULT '#ffffff', ADD slogan VARCHAR(160) DEFAULT NULL");}
 public function down(Schema $schema):void{$this->addSql('ALTER TABLE projeto_social DROP logo_path, DROP banner_path, DROP cor_primaria, DROP cor_secundaria, DROP cor_sidebar, DROP cor_texto_botao, DROP slogan');}
}
