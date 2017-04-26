<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170424142639 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            INSERT INTO `roles` (`id`, `parent_id`, `role_id`) VALUES
                (1, NULL, \'guest\'),
                (2, 1, \'user\'),
                (3, 2, \'admin\');
            INSERT INTO `users` (`id`, `username`, `email`, `displayName`, `password`, `state`) VALUES
                (1, \'admin\', \'lol123@what.ru\', \'Администратор\', \'$2y$14$lVig1ZDpX8U7och4y4x/z.Ng2bBeXejLBUUwoNlEUK74Gj7W7RqgO\', 1);
            INSERT INTO `user_role_linker` (`user_id`, `role_id`) VALUES
                (1, 3);
        ');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
