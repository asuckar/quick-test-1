<?php

include_once "php/filemanager.php";

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

final class filemanagerTest extends TestCase
{
    static private $pdo = null;
    private $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null)
                self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    public function testInventory(): void
    {
        $this->assertEquals(true, parseCSV("./unit-tests/happy-path/inventory.csv"));
    }

    public function testProducts(): void
    {
        $this->assertEquals(true, parseXML("./unit-tests/happy-path/products.xml"));
    }

    public function testCorruptedFileCSV(): void
    {
        $this->expectException(InvalidCSVException::class);
        parseCSV("./unit-tests/corrupted-files/inventory.csv");
    }

    public function testCorruptedFileXML(): void
    {
        $this->expectException(InvalidXMLException::class);
        parseXML("./unit-tests/corrupted-files/products.xml");
    }

    public function testSQLInjectionBypassFromCSV(): void
    {
        $this->assertEquals(true, parseCSV("./unit-tests/sql-injection/inventory.csv"));
    }

    public function testSQLInjectionBypassFromXML(): void
    {
        $this->assertEquals(true, parseXML("./unit-tests/sql-injection/products.xml"));
    }

    public function testEmptyFileCSV(): void
    {
        $this->expectException(InvalidCSVException::class);
        parseCSV("./unit-tests/empty-files/inventory.csv");
    }

    public function testEmptyFileXML(): void
    {
        $this->expectException(InvalidXMLException::class);
        parseXML("./unit-tests/empty-files/products.xml");
    }

    public function testDatabaseMissingForeignKey(): void
    {
        $this->expectException(InvalidMySQLCommandException::class);
        parseXML("./unit-tests/missing-foreign-key/products.xml");
    }

    public function testContentTypeCompatibilityCSV(): void
    {
        $this->expectException(InvalidFileContentException::class);
        parseCSV("./unit-tests/wrong-mysql-types/inventory.csv");
    }

    public function testContentTypeCompatibilityXML(): void
    {
        $this->expectException(InvalidFileContentException::class);
        parseXML("./unit-tests/wrong-mysql-types/products.xml");
    }
}

