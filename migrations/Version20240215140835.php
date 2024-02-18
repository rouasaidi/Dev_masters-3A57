<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215140835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donation ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE donation ADD CONSTRAINT FK_31E581A0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_31E581A0A76ED395 ON donation (user_id)');
        $this->addSql('ALTER TABLE feedback_don ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback_don ADD CONSTRAINT FK_E02AB661A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E02AB661A76ED395 ON feedback_don (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE donation DROP FOREIGN KEY FK_31E581A0A76ED395');
        $this->addSql('DROP INDEX IDX_31E581A0A76ED395 ON donation');
        $this->addSql('ALTER TABLE donation DROP user_id');
        $this->addSql('ALTER TABLE feedback_don DROP FOREIGN KEY FK_E02AB661A76ED395');
        $this->addSql('DROP INDEX IDX_E02AB661A76ED395 ON feedback_don');
        $this->addSql('ALTER TABLE feedback_don DROP user_id');
    }
}
