<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180828203955 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $dbData = [
            ['currencyFor'=> 'USA', 'exchangeRate' =>  27.85, 'convertedCurrency' => 'UAH'],
            ['currencyFor'=> 'USA', 'exchangeRate' =>  0.86, 'convertedCurrency' => 'EUR'],
            ['currencyFor'=> 'USA', 'exchangeRate' =>  68.01, 'convertedCurrency' => 'RUB'],
            ['currencyFor'=> 'UAH', 'exchangeRate' =>  0.036, 'convertedCurrency' => 'USD'],
            ['currencyFor'=> 'UAH', 'exchangeRate' =>  68.01, 'convertedCurrency' => 'RUB'],
            ['currencyFor'=> 'UAH', 'exchangeRate' =>  2.43, 'convertedCurrency' => 'RUB'],
            ['currencyFor'=> 'EUR', 'exchangeRate' =>  1.17, 'convertedCurrency' => 'USD'],
            ['currencyFor'=> 'EUR', 'exchangeRate' =>  32.80, 'convertedCurrency' => 'UAH'],
            ['currencyFor'=> 'EUR', 'exchangeRate' =>  79.37, 'convertedCurrency' => 'RUB'],
            ['currencyFor'=> 'RUB', 'exchangeRate' =>  0.41, 'convertedCurrency' => 'UAH'],
            ['currencyFor'=> 'RUB', 'exchangeRate' =>  0.015, 'convertedCurrency' => 'USD'],
            ['currencyFor'=> 'RUB', 'exchangeRate' =>  0.013, 'convertedCurrency' => 'EUR']
        ];
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE currency_exchange_rate (id INT AUTO_INCREMENT NOT NULL, currency_for_convert VARCHAR(30) NOT NULL, exchange_rate DOUBLE PRECISION NOT NULL, converted_currency VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');

        foreach ($dbData as $data ) {
            $this->addSql('INSERT INTO currency_exchange_rate (currency_for_convert, exchange_rate, converted_currency) VALUES(:currencyFor,:exchangeRate, :convertedCurrency)', $data);
        }
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE currency_exchange_rate');
    }
}
