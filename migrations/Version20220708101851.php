<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708101851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (id UUID NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF34668989D9B62 ON categories (slug)');
        $this->addSql('CREATE TABLE links (id UUID NOT NULL, url TEXT NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN links.created IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE categories2links (link_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY(link_id, category_id))');
        $this->addSql('CREATE INDEX IDX_61EBA8D1ADA40271 ON categories2links (link_id)');
        $this->addSql('CREATE INDEX IDX_61EBA8D112469DE2 ON categories2links (category_id)');
        $this->addSql('CREATE TABLE tags2links (link_id UUID NOT NULL, tag_id UUID NOT NULL, PRIMARY KEY(link_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_E8C4BC4ADA40271 ON tags2links (link_id)');
        $this->addSql('CREATE INDEX IDX_E8C4BC4BAD26311 ON tags2links (tag_id)');
        $this->addSql('CREATE TABLE tags (id UUID NOT NULL, tag VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FBC9426389B783 ON tags (tag)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE categories2links ADD CONSTRAINT FK_61EBA8D1ADA40271 FOREIGN KEY (link_id) REFERENCES links (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categories2links ADD CONSTRAINT FK_61EBA8D112469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tags2links ADD CONSTRAINT FK_E8C4BC4ADA40271 FOREIGN KEY (link_id) REFERENCES links (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tags2links ADD CONSTRAINT FK_E8C4BC4BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE categories2links DROP CONSTRAINT FK_61EBA8D112469DE2');
        $this->addSql('ALTER TABLE categories2links DROP CONSTRAINT FK_61EBA8D1ADA40271');
        $this->addSql('ALTER TABLE tags2links DROP CONSTRAINT FK_E8C4BC4ADA40271');
        $this->addSql('ALTER TABLE tags2links DROP CONSTRAINT FK_E8C4BC4BAD26311');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE links');
        $this->addSql('DROP TABLE categories2links');
        $this->addSql('DROP TABLE tags2links');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
