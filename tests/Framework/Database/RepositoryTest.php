<?php

namespace Tests\Framework\Database;

use Framework\Database\Repository;
use PDO;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{

    private Repository $repository;

    public function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        ]);
        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )');

        $this->repository = new Repository($pdo);
        $reflection = new \ReflectionClass($this->repository);
        $property = $reflection->getProperty('repository');
        $property->setAccessible(true);
        $property->setValue($this->repository, 'test');
    }

    public function testFind()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $test = $this->repository->find(1);
        $this->assertInstanceOf(\stdClass::class, $test);
        $this->assertEquals('a1', $test->name);
    }

    public function testFindList()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertEquals(['1' => 'a1', '2' => 'a2'], $this->repository->findList());
    }

    public function testExists()
    {
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a1")');
        $this->repository->getPdo()->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertTrue($this->repository->exists(1));
        $this->assertTrue($this->repository->exists(2));
        $this->assertFalse($this->repository->exists(99548));
    }

}
