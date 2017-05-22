<?php
declare(strict_types = 1);

namespace Zippy1981\PhpSqlServerDateTime;

use PDO;
use PHPUnit\Framework\TestCase;

class OdbcDateTimeOffsetTests extends TestCase
{
    private const CONNECTION_STRING =
        'odbc:Driver={ODBC Driver 13 for SQL Server};Server=localhost,1433;' .
    '   UID=sa;PWD=alwaysB3Encrypt1ng;APP=PHP Unit -- OdbcDateTimeOffsetTests';

    private const TEMP_TABLE_DDL = <<< EOSQL
DROP TABLE IF EXISTS #dateTable;
CREATE TABLE #dateTable (
	Id INT NOT NULL PRIMARY KEY CLUSTERED IDENTITY (1,1),
	[timestamp] DATETIMEOFFSET NOT NULL,
	message NVARCHAR(255) NOT NULL
);
EOSQL;

    /**
     * @var PDO $cn
     */
    private $cn;

    public function setup() {
        $this->cn = new PDO(self::CONNECTION_STRING);
        $this->cn->exec(self::TEMP_TABLE_DDL);
        $this->cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function testStringInsert() {
        $sql = <<< EOSQL
INSERT INTO #dateTable (message, [timestamp]) VALUES (
		'Solid string insert.',
		'%s'
	);
EOSQL;

        $result = $this->cn->exec(sprintf($sql, date(DATE_ATOM)));
        $this->assertNotFalse($result);
    }

    public function testParamInsert() {
        $sql = <<< EOSQL
INSERT INTO #dateTable (message, [timestamp]) VALUES (
		'Solid string insert.',
		:timestamp
	);
EOSQL;

        $stmt = $this->cn->prepare($sql);
        $result = $stmt->execute([date(DATE_ATOM)]);
        $this->assertNotFalse($result);
    }

    public function testBoundValueParamInsert() {
        $sql = <<< EOSQL
INSERT INTO #dateTable (message, [timestamp]) VALUES (
		'Solid string insert.',
		:timestamp
	);
EOSQL;

        $stmt = $this->cn->prepare($sql);
        $stmt->bindValue(':timestamp', date(DATE_ATOM));
        $result = $stmt->execute();
        $this->assertNotFalse($result);
    }

    public function testBoundVariableParamInsert() {
        $sql = <<< EOSQL
INSERT INTO #dateTable (message, [timestamp]) VALUES (
		'Solid string insert.',
		:timestamp
	);
EOSQL;

        $stmt = $this->cn->prepare($sql);
        $timestamp = date(DATE_ATOM);
        $stmt->bindParam(':timestamp', $timestamp);
        $result = $stmt->execute();
        $this->assertNotFalse($result);
    }

    public function testDeclaredParamInsert() {
        $sql = <<< EOSQL
DECLARE @timestamp VARCHAR(35) = :datetime;
INSERT INTO #dateTable (message, [timestamp]) VALUES (
		'Solid string insert.',
		@timestamp
	);
EOSQL;

        $stmt = $this->cn->prepare($sql);
        $result = $stmt->execute([date(DATE_ATOM)]);
        $this->assertNotFalse($result);
    }
}