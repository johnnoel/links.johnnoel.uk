<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220710100134 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE link_metadata (id UUID NOT NULL, link_id UUID DEFAULT NULL, title TEXT NOT NULL, description TEXT NOT NULL, extra JSONB NOT NULL, fetched TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_82BE54DDADA40271 ON link_metadata (link_id)');
        $this->addSql('COMMENT ON COLUMN link_metadata.fetched IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE link_metadata ADD CONSTRAINT FK_82BE54DDADA40271 FOREIGN KEY (link_id) REFERENCES links (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE link_metadata');
    }
}
