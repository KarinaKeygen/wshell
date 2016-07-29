Массивы:

    $this->assertArrayHasKey('foo', ['foo' => 'baz']);
    $this->assertContains(4, [1, 2, 3, 4]);
    // проверка на тип данных
    $this->assertContainsOnly('string', ['1', '2', '3']);
    $this->assertCount(1, ['foo']);
    $this->assertEmpty([]);

Классы и объекты:

    $this->assertClassHasAttribute('foo', 'stdClass');
    $this->assertObjectHasAttribute('foo', new stdClass);
    $this->assertClassHasStaticAttribute('foo', 'stdClass');
    $this->assertContainsOnlyInstancesOf('Foo', array(new Foo(), new Bar(), new Foo()));
    $this->assertInstanceOf('RuntimeException', new Exception);

Цифры:

    $this->assertGreaterThan(1, 2);
    $this->assertGreaterThanOrEqual(2, 2);
    $this->assertLessThan(2, 1);
    $this->assertLessThanOrEqual(2, 2);

Строки:

    $this->assertContains('bar', 'foobar');
    $this->assertStringMatchesFormat('%i', 'foo');
    $this->assertRegExp(pattern, 'bar');
    $this->assertStringMatchesFormatFile()
    assertStringEndsWith()
    assertStringStartsWith()

Файлы:

    $this->assertFileEquals('/home/sb/actual', '/home/sb/actual');
    $this->assertFileExists('/path/to/file');

Логика:

    $this->assertFalse(FALSE);
    $this->assertTrue(TRUE);

Другое:

    // тот же тип
    $this->assertSame('2204', '2204');
    $this->assertNull(Null);
    $this->assertEquals(1, 1);
    $this->assertInternalType('string', '42');
    assertTag();
    $this->assertJsonFileEqualsJsonFile('path/to/fixture/file', 'path/to/actual/file');
    $this->assertJsonStringEqualsJsonFile('path/to/fixture/file', json_encode(["Mascott" => "ux"]);
    $this->assertJsonStringEqualsJsonString();
    $this->assertEqualXMLStructure(new DOMElement('foo'), new DOMElement('bar'));
