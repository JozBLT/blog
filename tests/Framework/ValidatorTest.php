<?php

namespace Tests\Framework;

use Framework\Validator;
use GuzzleHttp\Psr7\UploadedFile;
use Tests\DatabaseTestCase;

class ValidatorTest extends DatabaseTestCase
{

    public function testNotEmpty()
    {
        $errors = $this->makeValidator([
            'name' => 'john',
            'content' => ''
        ])
            ->notEmpty('content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testRequiredIfFailed()
    {
        $errors = $this->makeValidator([
            'name' => 'john'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(1, $errors);
    }

    public function testRequiredIfSuccess()
    {
        $errors = $this->makeValidator([
            'name' => 'john',
            'content' => 'un contenu'
        ])
            ->required('name', 'content')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testSlugFail()
    {
        $errors = $this->makeValidator([
            'slug' => 'Bla-blu-Blablu114',
            'slug2' => 'bla-blu_blablu114',
            'slug3' => 'bla--blu-blablu114',
            'slug4' => 'bla-blu-blablu-',
        ])
            ->slug('slug')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();
        $this->assertEquals(['slug', 'slug2', 'slug3', 'slug4'], array_keys($errors));
    }

    public function testSlugSuccess()
    {
        $errors = $this->makeValidator([
            'slug' => 'bla-blu-blablu114',
            'slug2' => 'blablu'

        ])
            ->slug('slug')
            ->slug('slug2')
            ->getErrors();
        $this->assertCount(0, $errors);
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDateTime()
    {
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->dateTime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-30 11:12:13'])->dateTime('date')->getErrors());
    }

    public function testExists()
    {
        $pdo = $this->getPDO();
        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )');
        $pdo->exec('INSERT INTO test (name) VALUES ("a1")');
        $pdo->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertTrue($this->makeValidator(['category' => 1])->exists('category', 'test', $pdo)->isValid());
        $this->assertFalse($this->makeValidator(['category' => 99999])->exists('category', 'test', $pdo)->isValid());
    }

    public function testUnique()
    {
        $pdo = $this->getPDO();
        $pdo->exec('CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )');
        $pdo->exec('INSERT INTO test (name) VALUES ("a1")');
        $pdo->exec('INSERT INTO test (name) VALUES ("a2")');
        $this->assertFalse($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a1111'])->unique('name', 'test', $pdo)->isValid());
        $this->assertTrue($this->makeValidator(['name' => 'a1'])->unique('name', 'test', $pdo, 1)->isValid());
        $this->assertFalse($this->makeValidator(['name' => 'a2'])->unique('name', 'test', $pdo, 1)->isValid());
    }

    public function testUploadedFile()
    {
        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getError'])
            ->getMock();
        $file->expects($this->once())->method('getError')->willReturn(UPLOAD_ERR_OK);
        $file2 = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getError'])
            ->getMock();
        $file2->expects($this->once())->method('getError')->willReturn(UPLOAD_ERR_CANT_WRITE);
        $this->assertTrue($this->makeValidator(['image' => $file])->uploaded('image')->isValid());
        $this->assertFalse($this->makeValidator(['image' => $file2])->uploaded('image')->isValid());
    }

    public function testExtension()
    {
        $file = $this->getMockBuilder(UploadedFile::class)->disableOriginalConstructor()->getMock();
        $file->expects($this->any())->method('getError')->willReturn(UPLOAD_ERR_OK);
        $file->expects($this->any())->method('getClientFileName')->willReturn('demo.jpg');
        $file->expects($this->any())
            ->method('getClientMediaType')
            ->willReturn('image/jpeg', 'fake/php');
        $this->assertTrue($this->makeValidator(['image' => $file])->extension('image', ['jpg'])->isValid());
        $this->assertFalse($this->makeValidator(['image' => $file])->extension('image', ['jpg'])->isValid());
    }

    public function testEmail()
    {
        $this->assertTrue($this->makeValidator(['email' => 'demo@local.dev'])->email('email')->isValid());
        $this->assertFalse($this->makeValidator(['email' => 'azeeaz'])->email('email')->isValid());
    }

    private function makeValidator(array $params): Validator
    {
        return new Validator($params);
    }

}
